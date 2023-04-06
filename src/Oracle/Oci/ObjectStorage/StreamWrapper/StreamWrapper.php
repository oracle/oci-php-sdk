<?php
/**Copyright (c) 2023, Oracle and/or its affiliates. All rights reserved.
 * This software is dual-licensed to you under the Universal Permissive License
 * (UPL) 1.0 as shown at https://oss.oracle.com/licenses/upl or Apache License
 * 2.0 as shown at http://www.apache.org/licenses/LICENSE-2.0. You may choose
 * either license.
*/
namespace Oracle\Oci\ObjectStorage\StreamWrapper;

use Exception;
use GuzzleHttp\Psr7\CachingStream;
use GuzzleHttp\Psr7\MimeType;
use InvalidArgumentException;
use Oracle\Oci\Common\Constants;
use Oracle\Oci\Common\Logging\Logger;
use Oracle\Oci\Common\Logging\NamedLogAdapterDecorator;
use Oracle\Oci\Common\OciBadResponseException;
use Oracle\Oci\Common\OciException;
use Oracle\Oci\Common\OciItemIterator;
use Oracle\Oci\Common\OciResponseIterator;
use Oracle\Oci\Common\Region;
use Oracle\Oci\Common\StringUtils;
use Oracle\Oci\ObjectStorage\ObjectStorageClient;
use Psr\Http\Message\StreamInterface;

/**
 * PHP Stream Wrapper for OCI Object Storage.
 */
class StreamWrapper
{
    const PROTOCOL = "oci";
    const PROTOCOL_SEPARATOR = "://";
    const PROTOCOL_AND_SEPARATOR_LENGTH = 6;
    const SUPPORTED_MODES = ["r", "w", "a", "x"];
    const DEFAULT_WORKREQUEST_SLEEP_TIME_IN_SECONDS = 1;

    const REGION_PARAM = 'region';
    const NAMESPACE_NAME_PARAM = 'namespaceName';
    const BUCKET_NAME_PARAM = 'bucketName';
    const OBJECT_NAME_PARAM = 'objectName';
    const COMPARTMENT_ID_PARAM = 'compartmentId';
    const CLIENT_PARAM = 'client';
    const WORKREQUEST_SLEEP_TIME_IN_SECONDS_PARAM = 'workrequestSleepTimeInSeconds';
    const PAR_LIFETIME_PARAM = ParHttpStream::PAR_LIFETIME_PARAM;
    const PAR_RETRIES_PARAM = ParHttpStream::PAR_RETRIES_PARAM;

    /**
     * For fopen. The value can be either a class name (subclass of AbstractHttpStream), or an instance
     * (of a subclass of AbstractHttpStream).
     */
    const STREAM_IMPLEMENTATION_PARAM = "streamImplementation";

    /**
     * For fopen.
     */
    const CONTENT_TYPE = 'contentType';

    /**
     * For fopen.
     */
    const SEEKABLE_PARAM = 'seekable';

    /**
     * For opendir.
     */
    const OPENDIR_DELIMITER = 'delimiter';
    const OPENDIR_LIST_FILTER = 'listFilter';

    /**
     * For writing. The value can be either a class name (subclass of UploaderInterface), or an instance
     * (of a subclass of UploaderInterface).
     */
    const UPLOADER_IMPLEMENTATION_PARAM = "uploaderImplementation";

    /* Properties */

    /*resource*/ public $context;

    /**
     * @var NamedLogAdapterDecorator
     */
    protected $logger;

    /**
     * @var string
     */
    /*string*/ protected $mode;
    
    /**
     * @var array
     */
    /*array*/ protected $params;

    /**
     * @var StreamInterface|HasFileHandle|null
     */
    /*StreamInterface*/ protected $body;

    /**
     * @var int
     */
    protected $position;

    /**
     * @var string
     */
    protected $dir_bucket;

    /**
     * @var string
     */
    protected $dir_bucketPrefix;

    /**
     * @var OciItemIterator|null
     */
    protected $dir_iterator = null;
    
    /**
     * @var callable|null
     */
    protected $dir_listFilterFn = null;
    
    /**
     * @var ObjectStorageClient
     */
    protected static $staticClient;
    
    /**
     * @var array
     */
    protected static $statCache = [];

    /**
     * @var array
     */
    /*array*/ protected static $staticOptions;

    /* Static methods */

    /**
     * Register the stream wrapper and use the provided ObjectStorageClient and options.
     *
     * Most likely, you also have to provide the following options:
     * - namespace (StreamWrapper::NAMESPACE_NAME_PARAM => "...", for all operations, unless you always use the "oci://bucket@namespace" syntax for paths)
     * - compartment OCID (StreamWrapper::COMPARTMENT_ID_PARAM => "...", for mkdir and opendir)
     * - region (StreamWrapper::REGION_PARAM => "...", for rename, unless the client knows its region)
     *
     * @param ObjectStorageClient $client ObjectStorageClient to use
     * @param array|null $staticOptions options for the StreamWrapper
     */
    public static function register(ObjectStorageClient $client, /*array*/ $staticOptions = [])
    {
        if (in_array(self::PROTOCOL, stream_get_wrappers())) {
            stream_wrapper_unregister(self::PROTOCOL);
        }

        stream_wrapper_register(self::PROTOCOL, static::class, STREAM_IS_URL);
        self::$staticClient = $client;
        self::$staticOptions = $staticOptions;
    }
    
    /**
     * Returns the context of the statically registered options
     * @return resource stream context
     */
    public static function getContext()
    {
        return stream_context_create(self::$staticOptions);
    }
    
    /**
     * Returns the context of the statically registered options, optionally with new keys merged into
     * the 'oci' key.
     * @param array $addToOciKey options merged into the 'oci' key
     * @return resource stream context
     */
    public static function getContextAndAdd($addToOciKey=[])
    {
        $options = self::$staticOptions;
        $options[self::PROTOCOL] = array_merge($options[self::PROTOCOL], $addToOciKey);
        return stream_context_create($options);
    }

    /* Methods */

    public function __construct()
    {
        $this->logger = Logger::logger(static::class);
    }

    /**
     * Called during closedir.
     * @return bool true
     */
    public function dir_closedir() // : bool
    {
        $this->dir_iterator = null;

        return true;
    }

    /**
     * Called during opendir.
     * @param string $path path the path to open ("oci://...")
     * @param int $options not used
     * @return true if the directory iteration could be started
     */
    public function dir_opendir(/*string*/ $path, /*int*/ $options) // : bool
    {
        $opendirLogger = $this->logger->scope("dir_opendir");
        $opendirLogger->debug("dir_opendir, path=$path, options=$options");

        $this->clearStatCache();

        $this->params = $params = $this->getParams($path);
        $delimiter = '/';
        $opendirLogger->debug("options=" . json_encode($this->getOptions()));
        if (array_key_exists(self::OPENDIR_DELIMITER, $this->getOptions())) {
            $opendirLogger->debug("Delimiter is set in options");
            $delimiter = $this->getOption(self::OPENDIR_DELIMITER);
        }
        $this->dir_listFilterFn = $this->getOption(self::OPENDIR_LIST_FILTER);

        $opendirLogger->debug("params: " . json_encode($params));
        $opendirLogger->debug("delimiter: $delimiter; listFilterFn " . ($this->dir_listFilterFn != null ? "set" : "not set"));

        if (array_key_exists(self::OBJECT_NAME_PARAM, $params) && $params[self::OBJECT_NAME_PARAM]) {
            $params[self::OBJECT_NAME_PARAM] = self::withTrailingSingleSlash($params[self::OBJECT_NAME_PARAM]);
        } else {
            $params[self::OBJECT_NAME_PARAM] = "";
        }

        if (array_key_exists(self::BUCKET_NAME_PARAM, $params) && $params[self::BUCKET_NAME_PARAM]) {
            return $this->dir_opendir_listObjects($params, $delimiter);
        } else {
            return $this->dir_opendir_listBuckets($params);
        }
    }

    /**
     * Helper for dir_opendir to list objects.
     * @param array $params parameters that can be used for ListObjects
     * @param string $delimiter delimiter character between directories (only '/' and null are supported by Object Storage)
     * @return true if the directory iteration could be started
     */
    protected function dir_opendir_listObjects($params, $delimiter)
    {
        $opendirLogger = $this->logger->scope("dir_opendir\\listObjects");
        
        $this->dir_bucket = $params[self::BUCKET_NAME_PARAM];
        $this->dir_bucketPrefix = $params[self::OBJECT_NAME_PARAM];
        $listObjectsParams = [
            self::NAMESPACE_NAME_PARAM => $params[self::NAMESPACE_NAME_PARAM],
            self::BUCKET_NAME_PARAM => $params[self::BUCKET_NAME_PARAM],
            'prefix' => $params[self::OBJECT_NAME_PARAM],
            'fields' => 'name,size,timeModified'
        ];

        if ($delimiter) {
            $listObjectsParams[self::OPENDIR_DELIMITER] = $delimiter;
        }

        $opendirLogger->debug("listObjectsParams: " . json_encode($listObjectsParams));

        // custom iterator that also iterates over prefixes
        $this->dir_iterator = new OciItemIterator(
            $this->getClient(),
            'listObjects',
            $listObjectsParams,
            OciResponseIterator::buildNextTokenResponseGetterFromJson('nextStartWith'),
            OciResponseIterator::buildPageRequestSetterToParams('start'),
            OciItemIterator::buildResponseItemsGetterForObjectStorageListObjects(null)
        );

        if ($params[self::OBJECT_NAME_PARAM]) {
            $this->dir_iterator->next(); // skip the pseudo-directory iteself
        }

        return true;
    }

    /**
     * Helper for dir_opendir to list buckets.
     * @param array $params parameters that can be used for ListBuckets
     * @return true if the directory iteration could be started
     */
    protected function dir_opendir_listBuckets($params)
    {
        $opendirLogger = $this->logger->scope("dir_opendir\\listBuckets");
        $this->dir_bucket = "";
        $this->dir_bucketPrefix = "";
        $listBucketsParams = [
            self::NAMESPACE_NAME_PARAM => $params[self::NAMESPACE_NAME_PARAM],
            self::COMPARTMENT_ID_PARAM => $params[self::COMPARTMENT_ID_PARAM]
        ];

        $opendirLogger->debug("listBucketsParams: " . json_encode($listBucketsParams));

        $this->dir_iterator = $this->getClient()->listBucketsIterator($listBucketsParams);

        return true;
    }

    /**
     * Called during readdir. Provides the next item.
     * @return string|false next item, or false if there was an error
     */
    public function dir_readdir() // : string
    {
        $readdirLogger = $this->logger->scope("dir_readdir");
        if (!$this->dir_iterator || !$this->dir_iterator->valid()) {
            return false;
        }

        if ($this->dir_listFilterFn) {
            do {
                $currentObject = $this->dir_iterator->current();
                $accepted = call_user_func($this->dir_listFilterFn, $currentObject);
                $readdirLogger->debug(json_encode($currentObject) . " accepted by filter? " . ($accepted ? "yes" : "no"));
                if (!$accepted) {
                    $this->dir_iterator->next();
                }
            } while (!$accepted && $this->dir_iterator->valid());
            if (!$this->dir_iterator->valid()) {
                return false;
            }
        } else {
            $currentObject = $this->dir_iterator->current();
        }

        $delimiter = '/';
        if (array_key_exists(self::OPENDIR_DELIMITER, $this->getOptions())) {
            $delimiter = $this->getOption(self::OPENDIR_DELIMITER);
        }
        $readdirLogger->debug("delimiter='$delimiter', dir_bucket='{$this->dir_bucket}', dir_bucketPrefix='{$this->dir_bucketPrefix}', name='{$currentObject->name}'");
        if ($this->dir_bucket) {
            if ($delimiter == '/') {
                $result = str_replace($this->dir_bucketPrefix, '', $currentObject->name);
                $path = self::PROTOCOL . self::PROTOCOL_SEPARATOR . "{$this->dir_bucket}/" . $currentObject->name;
            } else {
                $result = self::PROTOCOL . self::PROTOCOL_SEPARATOR . $this->dir_bucket . '/' . $currentObject->name;
                $path = $result;
            }
        } else {
            // we're actually listing buckets, not objects
            if ($delimiter == '/') {
                $result = str_replace($this->dir_bucketPrefix, '', $currentObject->name);
                $result .= "/";
                $path = self::PROTOCOL . self::PROTOCOL_SEPARATOR . "{$this->dir_bucket}/" . $currentObject->name;
            } else {
                $result = self::PROTOCOL . self::PROTOCOL_SEPARATOR . $currentObject->name . "/";
                $path = $result;
            }
        }
        $readdirLogger->debug("Result: '$result' (Path: '$path')");
        $stat = $this->buildUrlStat($currentObject);

        $this->cacheStat([$path => $stat]);
        $this->dir_iterator->next();

        return $result;
    }

    /**
     * Called during rewinddir.
     * @return bool true if the directory iteration could be rewound to the beginning
     */
    public function dir_rewinddir() // : bool
    {
        $rewinddirLogger = $this->logger->scope("dir_rewinddir");
        $rewinddirLogger->debug("Rewinding iterator, params: " . json_encode($this->params));

        $this->clearStatCache();
        $this->dir_iterator->rewind();

        if ($this->params[self::OBJECT_NAME_PARAM]) {
            $this->dir_iterator->next(); // skip the pseudo-directory iteself
            $rewinddirLogger->debug("Skipped pseudo-directory itself");
        }

        return true;
    }
    
    /**
     * Called during mkdir.
     * @param string $path path of the directory to create
     * @param int $mode not used
     * @param int $options not used
     * @return bool true if successful
     */
    public function mkdir(/*string*/ $path, /*int*/ $mode, /*int*/ $options) // :bool
    {
        $mkdirLogger = $this->logger->scope("mkdir");
        try {
            $params = $this->getParams($path);
        } catch (InvalidArgumentException $iae) {
            $mkdirLogger->debug("getParams threw " . $iae);
            return false;
        }
        $mkdirLogger->debug(json_encode($params));
        if (!array_key_exists(self::BUCKET_NAME_PARAM, $params) || !$params[self::BUCKET_NAME_PARAM]) {
            return false;
        }

        return self::hasNoObjectName($params)
            ? $this->createBucket($path, $params)
            : $this->createPseudoDirectory($path, $params);
    }
    
    /**
     * Called during rename. Renames an object, which is implemented using copy & delete.
     * This does not support renaming buckets (neither the old nor the new path may be in the form of 'oci://name').
     * @param string $path_from old name
     * @param string $path_to new name
     * @return bool true if the object could be successfully renamed.
     */
    public function rename(/*string*/ $path_from, /*string*/ $path_to) // :bool
    {
        try {
            $params_from = $this->getParams($path_from);
            $params_to = $this->getParams($path_to);
        } catch (InvalidArgumentException $iae) {
            return $this->trigger_error($iae->getMessage());
        }
        $renameLogger = $this->logger->scope("rename");
        $renameLogger->debug("From: " . json_encode($params_from) . PHP_EOL . "To: " . json_encode($params_to));

        if (!array_key_exists(self::OBJECT_NAME_PARAM, $params_from) || !$params_from[self::OBJECT_NAME_PARAM]  ||
            !array_key_exists(self::OBJECT_NAME_PARAM, $params_to) || !$params_to[self::OBJECT_NAME_PARAM]) {
            return $this->trigger_error("Cannot rename $path_from to $path_to, the OCI StreamWrapper only supports renaming objects (neither old nor new path can represent a bucket).");
        }
        $sourceObjectName = $params_from[self::OBJECT_NAME_PARAM];
        $destinationObjectName = $params_to[self::OBJECT_NAME_PARAM];
        if (substr($sourceObjectName, -1) == '/' && substr($destinationObjectName, -1) != '/') {
            return $this->trigger_error("Cannot rename $path_from to $path_to, must rename from directory name to directory name (destination path does not end in '/').");
        }
        if (substr($sourceObjectName, -1) != '/' && substr($destinationObjectName, -1) == '/') {
            return $this->trigger_error("Cannot rename $path_from to $path_to, must rename from file name to file name (destination path ends in '/').");
        }

        if (!array_key_exists(self::REGION_PARAM, $params_to)) {
            return $this->trigger_error("Must specify region in context.");
        }

        if (substr($sourceObjectName, -1) == '/') {
            // can only rename empty directories right now; check that it's empty
            $prefix = self::withTrailingSingleSlash($sourceObjectName);
            $response = $this->getClient()->listObjects([
                'namespaceName' => $params_from[self::NAMESPACE_NAME_PARAM],
                'bucketName' => $params_from[self::BUCKET_NAME_PARAM],
                'prefix' => $prefix,
                'limit' => 2]);
            if (count($response->getJson()->objects) > 1) {
                return $this->trigger_error("Cannot rename $path_from, directory not empty.");
            }
        } else {
            // moving a file, have to make sure the parent directory exists
            $lastSlash = strrpos($destinationObjectName, '/');
            if ($lastSlash !== false) {
                // check if pseudo-directory exists
                $destDir = substr($destinationObjectName, 0, $lastSlash);
                $prefix = self::withTrailingSingleSlash($destDir);
                try {
                    $response = $this->getClient()->headObject([
                        'namespaceName' => $params_to[self::NAMESPACE_NAME_PARAM],
                        'bucketName' => $params_to[self::BUCKET_NAME_PARAM],
                        'objectName' => $prefix]);
                } catch (OciBadResponseException $obre) {
                    if ($obre->getStatusCode() == 404) {
                        return $this->trigger_error("Cannot rename $path_from to $path_to, parent directory " .
                            self::PROTOCOL . self::PROTOCOL_SEPARATOR . $params_to[self::BUCKET_NAME_PARAM] . "/$destDir does not exist.");
                    } else {
                        throw $obre;
                    }
                }
            } else {
                // check if bucket exists
                try {
                    $response = $this->getClient()->headBucket([
                        'namespaceName' => $params_to[self::NAMESPACE_NAME_PARAM],
                        'bucketName' => $params_to[self::BUCKET_NAME_PARAM]]);
                } catch (OciBadResponseException $obre) {
                    if ($obre->getStatusCode() == 404) {
                        return $this->trigger_error("Cannot rename $path_from to $path_to, destination directory " .
                            self::PROTOCOL . self::PROTOCOL_SEPARATOR . $params_to[self::BUCKET_NAME_PARAM] . " does not exist.");
                    } else {
                        throw $obre;
                    }
                }
            }
        }

        $destinationRegion = $params_to[self::REGION_PARAM];
        if ($destinationRegion instanceof Region) {
            $destinationRegion = $destinationRegion->getRegionId();
        }
        $renameLogger->debug("destinationRegion: $destinationRegion");

        try {
            // we don't support renaming objects, we have to copy them
            $copy_object_details = [
                'sourceObjectName' => $params_from[self::OBJECT_NAME_PARAM],
                'destinationRegion' => $destinationRegion,
                'destinationNamespace' => $params_to[self::NAMESPACE_NAME_PARAM],
                'destinationBucket' => $params_to[self::BUCKET_NAME_PARAM],
                'destinationObjectName' => $params_to[self::OBJECT_NAME_PARAM]
            ];
            $renameLogger->debug("copyObjectDetails: " . json_encode($copy_object_details));
            $response = $this->getClient()->copyObject([
                'namespaceName' => $params_from[self::NAMESPACE_NAME_PARAM],
                'bucketName' => $params_from[self::BUCKET_NAME_PARAM],
                'copyObjectDetails' => $copy_object_details]);
            $workrequest_id = $response->getHeaders()[Constants::OPC_WORK_REQUEST_ID_HEADER_NAME][0];

            $renameLogger->debug("workRequestId: $workrequest_id");
            $isDone = false;
            // TODO: in the future, replace with waiter
            while (!$isDone) {
                $response = $this->getClient()->getWorkRequest([
                    'workRequestId' => $workrequest_id
                ]);
                $renameLogger->debug("Work request: " . json_encode($response->getJson()));
                $status = $response->getJson()->status;
                $timeFinished = property_exists($response->getJson(), 'timeFinished') ? $response->getJson()->timeFinished : null;
                if ($status == "COMPLETED" || $timeFinished != null) {
                    $isDone = true;
                } else {
                    $sleepTime = $params_to[self::WORKREQUEST_SLEEP_TIME_IN_SECONDS_PARAM];
                    $renameLogger->debug("Work request not done, sleeping for $sleepTime seconds.");
                    sleep($sleepTime);
                }
            }
            $renameLogger->debug("Deleting $path_from");
            $this->getClient()->deleteObject($params_from);
            return true;
        } catch (OciBadResponseException $obre) {
            $this->trigger_error($obre->getMessage());
        }
        return false;
    }

    /**
     * Returns true if there is no object name.
     * @param array $params parameters
     * @return bool true if no object name
     */
    private static function hasNoObjectName(&$params)
    {
        return (!array_key_exists(self::OBJECT_NAME_PARAM, $params)
        || $params[self::OBJECT_NAME_PARAM] === null
        || $params[self::OBJECT_NAME_PARAM] === '/');
    }
    
    /**
     * Returns the path, ending with a single slash. Turns multiple slashes at the end into a single slash.
     * If no slash at the end, adds a single slash.
     * @param string $str path
     * @return string the path with a single slash at the end
     */
    private static function withTrailingSingleSlash($str)
    {
        return rtrim($str, '/') . '/';
    }

    /**
     * Called during rmdir. Deletes a directory, which can be either a bucket or a pseudo-directory.
     * @param string $path the path to delete
     * @param int $options not used
     * @return bool true if the directory could be removed
     */
    public function rmdir(/*string*/ $path, /*int*/ $options) // :bool
    {
        try {
            $params = $this->getParams($path);
        } catch (InvalidArgumentException $iae) {
            return $this->trigger_error($iae->getMessage());
        }
        $rmdirLogger = $this->logger->scope("rmdir");
        $rmdirLogger->debug("path: $path, params: " . json_encode($params));
        if (!array_key_exists(self::BUCKET_NAME_PARAM, $params) || !$params[self::BUCKET_NAME_PARAM]) {
            return $this->trigger_error("Cannot delete $path, must specify a bucket in " . self::PROTOCOL . self::PROTOCOL_SEPARATOR . "bucket or " . self::PROTOCOL_SEPARATOR . self::PROTOCOL_SEPARATOR . "bucket@namespace format");
        }

        if (self::hasNoObjectName($params)) {
            // only a bucket, no object name
            $rmdirLogger->debug("Only a bucket name, no object name, deleting bucket " . $params[self::BUCKET_NAME_PARAM]);
            try {
                $this->getClient()->deleteBucket($params);
            } catch (OciBadResponseException $obre) {
                return $this->trigger_error($obre->getMessage());
            }
            $this->clearStatCache();
            return true;
        } else {
            // deleting a pseudo-directory
            try {
                $prefix = self::withTrailingSingleSlash($params[self::OBJECT_NAME_PARAM]);
                $rmdirLogger->debug("Bucket bucket and object name, deleting pseudo-directory " . $prefix);
                $response = $this->getClient()->listObjects([
                    'namespaceName' => $params[self::NAMESPACE_NAME_PARAM],
                    'bucketName' => $params[self::BUCKET_NAME_PARAM],
                    'prefix' => $prefix,
                    'limit' => 2]);
                if (count($response->getJson()->objects) > 1) {
                    return $this->trigger_error("Cannot delete $path, directory not empty.");
                }
                return $this->unlink(self::withTrailingSingleSlash($path));
            } catch (OciBadResponseException $obre) {
                return $this->trigger_error($obre->getMessage());
            }
        }
    }
    
    /**
     * Return the underlying file resource.
     *
     * @param int $cast_as not used
     *
     * @return resource
     */
    public function stream_cast(/*int*/ $cast_as) // :resource
    {
        if ($this->body == null) {
            throw new OciException("Stream was not opened, illegal to call stream_cast");
        }
        return $this->body->getStream();
    }
    
    /**
     * Close the underlying stream.
     */
    public function stream_close() // :void
    {
        if ($this->body == null) {
            throw new OciException("Stream was not opened, illegal to call stream_close");
        }
        $this->body->close();
        $this->body = null;
    }
    
    /**
     * Return true if the underlying stream is at its end.
     * @return bool true if at the end of the stream
     */
    public function stream_eof() // :bool
    {
        if ($this->body == null) {
            throw new OciException("Stream was not opened, illegal to call stream_eof");
        }
        return $this->body->eof();
    }
    
    /**
     * Flushes a file being written to, which means this is where we actually upload to Object Storage.
     * @return bool true if the contents could be flushed to Object Storage
     */
    public function stream_flush() // :bool
    {
        $flushLogger = $this->logger->scope("stream_flush");

        if ($this->mode == 'r') {
            return false;
        }

        $this->body->rewind();
        $putObjectParams = $this->params;

        // Attempt to guess the ContentType of the upload based on the file extension of the key
        if (!array_key_exists(self::CONTENT_TYPE, $putObjectParams) &&
            ($type = MimeType::fromFilename($putObjectParams[self::OBJECT_NAME_PARAM]))
        ) {
            $putObjectParams[self::CONTENT_TYPE] = $type;
        }

        try {
            $implementation = $this->getOption(self::UPLOADER_IMPLEMENTATION_PARAM);
            if (is_string($implementation)) {
                $uploader = new $implementation($this->getClient());
                $flushLogger->debug("Created custom $implementation to use as uploader");
            } elseif ($implementation instanceof UploaderInterface) {
                $uploader = $implementation;
                $flushLogger->debug("Using custom " . StringUtils::get_type_or_class($implementation) . " to use as uploader");
            } else {
                $uploader = new UploadManagerUploader($this->getClient());
                $flushLogger->debug("Created default " . StringUtils::get_type_or_class($uploader) . " to use as uploader");
            }

            $uploader->upload($putObjectParams, $this->body);
            return true;
        } catch (Exception $e) {
            return $this->trigger_error($e->getMessage());
        }
    }
    
    public function stream_lock(/*int*/ $operation) // :bool
    {
        // TODO (not implemented in AWS PHP SDK either)
        return false;
    }
    
    public function stream_metadata(/*string*/ $path, /*int*/ $option, /*mixed*/ $value) // :bool
    {
        // TODO (not implemented in AWS PHP SDK either)
        return false;
    }
    
    /**
     * Called during fopen. Opens a stream.
     * @param string $path path of the file to open
     * @param string $mode mode (supported are 'r', 'w', 'a', and 'x')
     * @param int $options not used
     * @param string|null $opened_path not used
     */
    public function stream_open(
        /*string*/
        $path,
        /*string*/
        $mode,
        /*int*/
        $options,
        /*?string*/
        &$opened_path
    ) // :bool
    {
        $this->logger->scope("stream_open")->debug("path: '$path', mode: '$mode', options: '$options', opened_path: $opened_path");
        $this->params = $this->getParams($path);
        $this->logger->scope("stream_open")->debug(json_encode($this->params));

        if (self::hasNoObjectName($this->params)) {
            return $this->trigger_error("Cannot open a bucket. You must specify a path that includes an object in the form of " . self::PROTOCOL . self::PROTOCOL_SEPARATOR . "bucket/object");
        }

        // ignore binary (b) and Windows translation (t) modes
        $this->mode = $mode = rtrim($mode, 'bt');

        if (!in_array($mode, self::SUPPORTED_MODES)) {
            return $this->trigger_error("Mode not supported: {$mode}. Use one " . implode(", ", self::SUPPORTED_MODES) . ".");
        }

        // For mode "x", ensure object doesn't already exist
        if ($mode == 'x') {
            try {
                $this->getClient()->headObject([
                    'namespaceName' => $this->params[self::NAMESPACE_NAME_PARAM],
                    'bucketName' => $this->params[self::BUCKET_NAME_PARAM],
                    'objectName' => $this->params[self::OBJECT_NAME_PARAM]]);
                return $this->trigger_error("{$path} already exists.");
            } catch (OciBadResponseException $e) {
                $statusCode = $e->getStatusCode();
                if ($statusCode != 404) {
                    return $this->trigger_error($e->getMessage());
                }
            } catch (Exception $e) {
                return $this->trigger_error($e->getMessage());
            }
        }

        switch ($mode) {
            case "r": return $this->stream_open_read();
            case "a": return $this->stream_open_append();
            // "w" or "x"
            default: return $this->stream_open_write();
        }
    }

    /**
     * Returns the StreamInterface that the StreamWrapper is working with.
     * @return StreamInterface stream interface being used by this StreamWrapper.
     */
    public function get_stream()
    {
        return $this->body;
    }
    
    /**
     * Called during fread.
     * @param int $count number of bytes to read
     * @return string data read
     */
    public function stream_read(/*int*/ $count) // :string
    {
        if ($this->body == null) {
            throw new OciException("Stream was not opened, illegal to call stream_read");
        }
        return $this->body->read($count);
    }
    
    /**
     * Called during fseek.
     * @param int $offset offset in bytes
     * @param int $whence one of SEEK_SET (to seek from the beginning), SEEK_END (to seek from the end; $offset should be negative), or SEEK_CUR (to seek from current position)
     */
    public function stream_seek(/*int*/ $offset, /*int*/ $whence = SEEK_SET) // :bool
    {
        if ($this->body == null) {
            throw new OciException("Stream was not opened, illegal to call stream_seek");
        }

        if (!$this->getOption(self::SEEKABLE_PARAM)) {
            $this->logger->debug("stream_seek, seekable option is not set, not allowing seeking");
            return false;
        }
        if ($whence == SEEK_END) {
            $this->logger->debug("stream_seek, offset = $offset, whence = SEEK_END; this may require streaming the entire file");
        }
        $this->body->seek($offset, $whence);
        // stream_seek in stream wrappers returns a bool, and fseek returns 0 for success and -1 for failure.
        // But StreamInterface::seek doesn't return anything :dumpsterfire:
        return true;
    }
    
    public function stream_set_option(/*int*/ $option, /*int*/ $arg1, /*int*/ $arg2) // :bool
    {
        // TODO (not implemented in AWS PHP SDK either)
        return false;
    }
    
    /**
     * Called during stream_stat.
     * @return array|false stat of the underlying stream, or false
     */
    public function stream_stat() // :array|false
    {
        return fstat($this->body->getStream());
    }

    /**
     * Called during ftell.
     * @return int position in the stream
     */
    public function stream_tell() // :int
    {
        if ($this->body == null) {
            throw new OciException("Stream was not opened, illegal to call stream_read");
        }
        return $this->body->tell();
    }
    
    public function stream_truncate(/*int*/ $new_size) // :bool
    {
        // TODO (not implemented in AWS PHP SDK either)
        return false;
    }
    
    /**
     * Called during fwrite.
     * @param string $data data to write
     * @return int number of bytes written
     */
    public function stream_write(/*string*/ $data) // :int
    {
        if ($this->body == null) {
            throw new OciException("Stream was not opened, illegal to call stream_write");
        }
        return $this->body->write($data);
    }
    
    /**
     * Called during unlink. Deletes a file
     * @param string $path path to the file
     * @return bool true if the file could be deleted
     */
    public function unlink(/*string*/ $path) // :bool
    {
        try {
            $params = $this->getParams($path);
            if (!array_key_exists(self::OBJECT_NAME_PARAM, $params)) {
                return $this->trigger_error("Can only delete files, $path is not a file");
            }
            $this->clearStatCache($path);
            $this->getClient()->deleteObject($params);
            return true;
        } catch (Exception $e) {
            return $this->trigger_error($e->getMessage());
        }
    }
    
    /**
     * Called during stat, file_exists, etc.
     * @param string $path path to the file or directory
     * @param int $flags these may be used to suppress errors
     * @return array|false stat or false
     */
    public function url_stat(/*string*/ $path, /*int*/ $flags) // :array|false
    {
        $statLogger = $this->logger->scope("url_stat");

        $statLogger->debug("url_stat: path: $path");
        $statLogger->debug("statCache: " . json_encode(self::$statCache));

        // maybe it's in the cache
        if (array_key_exists($path, self::$statCache)) {
            $statLogger->debug("Using cached stat for $path: " . json_encode(self::$statCache[$path]));
            return self::$statCache[$path];
        }

        $params = $this->getParams($path);

        $statLogger->debug("params: " . json_encode($params));

        if (array_key_exists(self::OBJECT_NAME_PARAM, $params) && $params[self::OBJECT_NAME_PARAM] && $params[self::OBJECT_NAME_PARAM] !== '/' &&
            array_key_exists(self::BUCKET_NAME_PARAM, $params) && $params[self::BUCKET_NAME_PARAM]) {
            // object: file or pseudo-directory
            try {
                $response = $this->getClient()->headObject([
                    'namespaceName' => $params[self::NAMESPACE_NAME_PARAM],
                    'bucketName' => $params[self::BUCKET_NAME_PARAM],
                    'objectName' => $params[self::OBJECT_NAME_PARAM]]);
                if (substr($path, -1) == '/') {
                    return $this->buildUrlStat($path);
                } else {
                    return $this->buildUrlStat($response->getHeaders());
                }
            } catch (OciBadResponseException $obre) {
                if ($obre->getStatusCode() == 404) {
                    // a file or pseudo-directory does not exist, but perhaps it's an ancestor directory
                    // of a file that does exist. E.g. "oci://bucket/dir/file" exists and we're getting
                    // the stat for "oci://bucket/dir/".
                    $prefix = self::withTrailingSingleSlash($params[self::OBJECT_NAME_PARAM]);
                    try {
                        $response = $this->getClient()->listObjects([
                            'namespaceName' => $params[self::NAMESPACE_NAME_PARAM],
                            'bucketName' => $params[self::BUCKET_NAME_PARAM],
                            'prefix' => $prefix,
                            'limit' => 1
                        ]);
                        if ($response->getJson() && property_exists($response->getJson(), 'objects') && $response->getJson()->objects) {
                            $o = $response->getJson()->objects[0];
                            $statLogger->debug("ListObjects, count: " . count($response->getJson()->objects) . ", object: " . json_encode($o));
                            $statLogger->debug("Is '{$prefix}' a prefix of '{$o->name}'? " . ((strpos($o->name, $prefix) === 0) ? "yes" : "no"));
                            if (strpos($o->name, $prefix) === 0) {
                                // starts with the prefix
                                return $this->buildUrlStat($prefix);
                            }
                        }
                        return $this->maybe_trigger_error("File or directory not found: {$path}", $flags);
                    } catch (OciBadResponseException $listObre) {
                        return $this->maybe_trigger_error($listObre->getMessage() . " Path: {$path}", $flags);
                    }
                } else {
                    return $this->maybe_trigger_error($obre->getMessage() . " Path: {$path}", $flags);
                }
            }
        } elseif (array_key_exists(self::BUCKET_NAME_PARAM, $params) && $params[self::BUCKET_NAME_PARAM]) {
            // bucket: top-level
            try {
                $response = $this->getClient()->headBucket([
                    'namespaceName' => $params[self::NAMESPACE_NAME_PARAM],
                    'bucketName' => $params[self::BUCKET_NAME_PARAM]]);
                return $this->buildUrlStat($path);
            } catch (OciBadResponseException $obre) {
                return $this->maybe_trigger_error("File or directory not found: {$path}", $flags);
            }
        } elseif ((!array_key_exists(self::OBJECT_NAME_PARAM, $params) || !$params[self::OBJECT_NAME_PARAM]) &&
                (!array_key_exists(self::BUCKET_NAME_PARAM, $params) || !$params[self::BUCKET_NAME_PARAM])) {
            // oci:// (no bucket, no object)
            return $this->buildUrlStat(self::PROTOCOL . self::PROTOCOL_SEPARATOR);
        } else {
            return $this->maybe_trigger_error("File or directory not found: {$path}", $flags);
        }
    }

    // Helpers

    /**
     * Get the client to use. Use $staticClient if none set in stream context.
     * @return ObjectStorageClient the client to use
     */
    protected function getClient()
    {
        $contextClient = $this->getOption(self::CLIENT_PARAM);
        if ($contextClient != null) {
            return $contextClient;
        }
        return self::$staticClient;
    }

    /**
     * Get the options set in the stream context.
     *
     * @return array
     */
    protected function getOptions()
    {
        $context = $this->context ?: stream_context_get_default();
        $options = stream_context_get_options($context);

        $options += self::$staticOptions;

        return array_key_exists(self::PROTOCOL, $options) ? $options[self::PROTOCOL] : [];
    }

    /**
     * Get the value of the specified option.
     *
     * @param string $name Name of the option to retrieve
     * @return object|mixed|null the value of the option
     */
    protected function getOption($name)
    {
        $options = $this->getOptions();
        return array_key_exists($name, $options) ? $options[$name] : null;
    }

    /**
     * Get the bucket and object name from the path path (e.g. oci://bucket/object/name becomes
     * ['bucketName' => "bucket", "objectName" => "object/name"])
     *
     * @param string $path path given to the stream wrapper
     * @return array map of namespace name, bucket name, object name, and other parameters
     */
    protected function getParams($path)
    {
        if (substr($path, 0, self::PROTOCOL_AND_SEPARATOR_LENGTH) != (self::PROTOCOL . self::PROTOCOL_SEPARATOR)) {
            throw new InvalidArgumentException("Path $path didn't have prefix " . self::PROTOCOL . self::PROTOCOL_SEPARATOR);
        }

        $getParamsLogger = $this->logger->scope("getParams");

        $getParamsLogger->debug("getParams, path: $path");

        // trim superfluous slashes after the "oci://"
        $pathWithoutPrefix = substr($path, self::PROTOCOL_AND_SEPARATOR_LENGTH);
        $pathWithoutPrefix = ltrim($pathWithoutPrefix, '/');
        $fixedPath = self::PROTOCOL . self::PROTOCOL_SEPARATOR . $pathWithoutPrefix;
        if ($path != $fixedPath) {
            $path = $fixedPath;
            $getParamsLogger->debug("getParams, fixed path to path: $path");
        }

        // split into two parts at the first '/'
        $parts = explode('/', substr($path, strlen(self::PROTOCOL) + strlen(self::PROTOCOL_SEPARATOR)), 2);

        $params = $this->getOptions();

        unset($params[self::SEEKABLE_PARAM]);

        $bucketName = $parts[0];
        $namespaceName = null;
        if (strpos($bucketName, "@") !== false) {
            $atParts = explode('@', $bucketName, 2);
            if (count($atParts) != 2) {
                throw new InvalidArgumentException("For the 'bucket@namespace' format, need exactly two parts separated by the '@' character, had: $bucketName");
            }
            $bucketName = $atParts[0];
            $namespaceName = $atParts[1];
            if (strlen($namespaceName) == 0 || strlen($bucketName) == 0) {
                throw new InvalidArgumentException("For the 'bucket@namespace' format, need exactly two non-empty parts separated by the '@' character, had: {$parts[0]}");
            }
        }

        $result = [];
        $result += $params;
        if ($bucketName != null) {
            $result[self::BUCKET_NAME_PARAM] = $bucketName;
        }
        if (isset($parts[1])) {
            $result[self::OBJECT_NAME_PARAM] = $parts[1];
        }
        if ($namespaceName != null) {
            $result[self::NAMESPACE_NAME_PARAM] = $namespaceName;
        }
        if (!array_key_exists(self::NAMESPACE_NAME_PARAM, $result)) {
            throw new InvalidArgumentException("namespace is required, either as a context setting or by using the 'bucket@namespace' format. Was given: $path");
        }

        if (!array_key_exists(self::WORKREQUEST_SLEEP_TIME_IN_SECONDS_PARAM, $result) || !$result[self::WORKREQUEST_SLEEP_TIME_IN_SECONDS_PARAM]) {
            $result[self::WORKREQUEST_SLEEP_TIME_IN_SECONDS_PARAM] = self::DEFAULT_WORKREQUEST_SLEEP_TIME_IN_SECONDS;
        }

        if (!array_key_exists(self::REGION_PARAM, $result) || !$result[self::REGION_PARAM]) {
            $regionFromClient = $this->getClient()->getRegion();
            if ($regionFromClient) {
                $result[self::REGION_PARAM] = $regionFromClient;
            }
        }

        $getParamsLogger->debug("returning " . json_encode($result));

        return $result;
    }

    /**
     * Return true if the stream is seekable.
     * @return bool true if the stream is seekable
     */
    public function stream_is_seekable()
    {
        return $this->body->isSeekable();
    }

    /**
     * Open a stream for reading. The settings are already in $this->params.
     * @return bool true if the stream could be opened for reading
     */
    protected function stream_open_read()
    {
        try {
            $implementation = $this->getOption(self::STREAM_IMPLEMENTATION_PARAM);
            if ($implementation) {
                $this->body = new $implementation($this->params, $this->getClient());
                $this->logger->debug("Created new $implementation, seekable? " . ($this->stream_is_seekable() ? "seekable" : "not seekable"));
            } elseif ($implementation instanceof AbstractHttpStream) { // @phpstan-ignore-line (says it's always false)
                $this->body = $implementation;
                $this->logger->debug("Using custom " . StringUtils::get_type_or_class($implementation) .
                    ", seekable?" . ($this->stream_is_seekable() ? "seekable" : "not seekable"));
            } else {
                $this->body = new ParHttpStream($this->params, $this->getClient());
                $this->logger->debug("Created new ParHttpStream, seekable? " . ($this->stream_is_seekable() ? "seekable" : "not seekable"));
            }

            // wrap the stream in a caching stream to make it seekable
            if (!$this->body->isSeekable() && ($this->getOption(self::SEEKABLE_PARAM))) {
                $this->body = new CachingStream($this->body);
                $this->logger->debug("Created new CachingStream, seekable? " . ($this->stream_is_seekable() ? "seekable" : "not seekable"));
            }
            return true;
        } catch (OciBadResponseException $obre) {
            return false;
        }
    }

    /**
     * Open a stream for appending. The settings are already in $this->params.
     * This opens a temporary stream for writing, and downloads the existing object.
     *
     * @return bool true if the stream could be opened for appending
     */
    protected function stream_open_append()
    {
        $this->logger->scope("stream_open_append")->debug("params: " . json_encode($this->params));
        $this->body = new WriteStream();

        $readStream = new GetObjectStream($this->params, $this->getClient());
        while (!$readStream->eof()) {
            $data = $readStream->read(8 * 1024);
            $this->body->write($data);
        }

        return true;
    }

    /**
     * Open a stream for appending. The settings are already in $this->params.
     * This opens a temporary stream for writing.
     *
     * @return bool true if the stream could be opened for writing
     */
    protected function stream_open_write()
    {
        $this->logger->scope("stream_open_write")->debug("params: " . json_encode($this->params));
        $this->body = new WriteStream();
        return true;
    }

    /**
     * Helper for mkdir: Create a bucket.
     * @param string $path path of the directory
     * @param array $params parameters for creating the bucket
     */
    protected function createBucket($path, $params)
    {
        $this->logger->debug("createBucket $path, " . json_encode($params));
        try {
            $this->getClient()->headBucket($params);
            return $this->trigger_error("Directory already exists: {$path}");
        } catch (OciBadResponseException $e) {
            if ($e->getStatusCode() != 404) {
                return $this->trigger_error($e->getMessage());
            }
        }

        if (!array_key_exists(self::COMPARTMENT_ID_PARAM, $params)) {
            throw new InvalidArgumentException(self::COMPARTMENT_ID_PARAM . " setting required in stream context.");
        }

        try {
            $params['createBucketDetails'] = [
                'name' => $params[self::BUCKET_NAME_PARAM],
                'compartmentId' => $params[self::COMPARTMENT_ID_PARAM]
            ];
            $this->getClient()->createBucket($params);
            $this->clearStatCache($path);
            return true;
        } catch (\Exception $e) {
            return $this->trigger_error($e->getMessage());
        }
    }

    /**
     * Helper for mkdir: Create a pseudo-directory (empty file ending with a '/').
     * @param string $path path of the directory
     * @param array $params parameters for creating the object
     */
    protected function createPseudoDirectory($path, array $params)
    {
        // ensure the path ends in "/" and the body is empty.
        $params[self::OBJECT_NAME_PARAM] = self::withTrailingSingleSlash($params[self::OBJECT_NAME_PARAM]);
        $params['putObjectBody'] = '';

        $this->logger->debug("createPseudoDirectory $path, " . json_encode($params));

        // fail if this pseudo directory already exists
        try {
            $this->getClient()->headObject_Helper($params[self::NAMESPACE_NAME_PARAM], $params[self::BUCKET_NAME_PARAM], $params[self::OBJECT_NAME_PARAM]);
            return $this->trigger_error("Directory already exists: {$path}");
        } catch (OciBadResponseException $e) {
            if ($e->getStatusCode() != 404) {
                return $this->trigger_error($e->getMessage());
            }
        }

        try {
            $this->getClient()->putObject($params);
            $this->clearStatCache($path);
            return true;
        } catch (\Exception $e) {
            return $this->trigger_error($e->getMessage());
        }
    }

    /**
     * Cache the provided stats for the paths.
     * @param array $cache map from path to stat to use as a cache
     */
    protected function cacheStat(array $cache)
    {
        $this->logger->debug("cacheStat: " . json_encode($cache));
        self::$statCache = $cache;
    }

    /**
     * Clear the stat cache.
     * @param string|null $path if provided, also call clearstatcache for the path
     */
    protected function clearStatCache($path = null)
    {
        self::$statCache = [];
        if ($path) {
            clearstatcache(true, $path);
        }
    }

    // see https://www.php.net/manual/en/function.stat.php
    const S_IFDIR = 0040000; /* directory */
    const S_IFREG = 0100000; /* regular */
    const S_IRUSR = 0000400; /* read permission, user */
    const S_IWUSR = 0000200; /* write permission, user */
    const S_IXUSR = 0000100; /* execute/search permission, user */
    const S_IRWXU = self::S_IRUSR | self::S_IWUSR | self::S_IXUSR; /* RWX for user */
    const S_IRGRP = 0000040; /* read permission, group */
    const S_IWGRP = 0000020; /* write permission, group */
    const S_IXGRP = 0000010; /* execute/search permission, group */
    const S_IRWXG = self::S_IRGRP | self::S_IWGRP | self::S_IXGRP; /* RWX for group */
    const S_IROTH = 0000004; /* read permission, other */
    const S_IWOTH = 0000002; /* write permission, other */
    const S_IXOTH = 0000001; /* execute/search permission, other */
    const S_IRWXO = self::S_IROTH | self::S_IWOTH | self::S_IXOTH; /* RWX for other */
    const S_IRWXALL = self::S_IRWXU | self::S_IRWXG | self::S_IRWXO; /* RWX for all (777) */

    /**
     * array_key_exists, but case-insensitive.
     * @param string $key key in the array
     * @param array $a array
     * @return bool true if array key exists, using case-insensitive comparison
     */
    public static function array_key_exists_case_insensitive($key, $a)
    {
        $key = strtolower($key);
        foreach ($a as $k => $v) {
            if (strtolower($k) == $key) {
                return true;
            }
        }
        return false;
    }

    /**
     * $a[$key], but case-insensitive.
     * @param string $key key in the array
     * @param array $a array
     * @return mixed value associated with the key
     * @throws InvalidArgumentException if the key does not exist in the array
     */
    public static function get_array_value_case_insensitive($key, $a)
    {
        $key = strtolower($key);
        foreach ($a as $k => $v) {
            if (strtolower($k) == $key) {
                return $v;
            }
        }
        throw new InvalidArgumentException("Key '$key' did not exist in array, using case-insensitive comparison.");
    }

    /**
     * Build a stat result array
     *
     * @param string|object|array|bool $result Data to add
     *
     * @return array stat result array, see https://www.php.net/manual/en/function.stat.php
     */
    protected function buildUrlStat($result = null)
    {
        static $statTemplate = array(
            0  => 0,  'dev'     => 0, // device number
            1  => 0,  'ino'     => 0, // inode number
            2  => 0,  'mode'    => 0, // inode protection mode
            3  => 0,  'nlink'   => 0, // number of links
            4  => 0,  'uid'     => 0, // userid of owner
            5  => 0,  'gid'     => 0, // groupid of owner
            6  => -1, 'rdev'    => -1, // device type, if inode device
            7  => 0,  'size'    => 0, // size in bytes
            8  => 0,  'atime'   => 0, // time of last access (not supported by OCI)
            9  => 0,  'mtime'   => 0, // time of last modification
            10 => 0,  'ctime'   => 0, // time of last inode change
            11 => -1, 'blksize' => -1, // blocksize of filesystem IO
            12 => -1, 'blocks'  => -1, // number of 512-byte blocks allocated
        );

        $stat = $statTemplate;
        $type = gettype($result);

        $str_result = print_r($result, true);
        $this->logger->scope("verbose")->debug("buildUrlStat, type: $type, value: $str_result");

        if ($result === false) {
            return $stat;
        }

        $name = null;
        $size = null;
        $timeModified = null;
        if ($type == 'array') {
            $timeModified = (self::array_key_exists_case_insensitive("last-modified", $result))
            ? self::get_array_value_case_insensitive('last-modified', $result)[0] : null;
            $size = (self::array_key_exists_case_insensitive("Content-Length", $result))
            ? self::get_array_value_case_insensitive('Content-Length', $result)[0] : null;
        } elseif (is_object($result)) {
            $timeModified = (property_exists($result, "timeModified")) ? $result->timeModified : null;
            $size = (property_exists($result, "size")) ? $result->size : null;
            $name = (property_exists($result, "name")) ? $result->name : null;
        }

        // Determine what type of data is being cached
        if ($type == 'NULL' || $type == 'string') {
            // Directory with 0777 access
            $stat['mode'] = $stat[2] = self::S_IRWXALL | self::S_IFDIR;
        } elseif ($timeModified && ($name == null || substr($name, -1) != '/')) {
            // ListObjects or HeadObject result
            $this->logger->debug("buildUrlStat, file name: " . $name);

            // treat both 'time of last modification' (mtime) and 'time of last inode change' (ctime) the same
            $stat['mtime'] = $stat[9] = $stat['ctime'] = $stat[10] = strtotime($timeModified);
            $stat['size'] = $stat[7] = $size;
            // Regular file with 0777 access
            $stat['mode'] = $stat[2] = self::S_IRWXALL | self::S_IFREG;
        } else {
            // ListBuckets or HeadBucket result
            $this->logger->debug("buildUrlStat, directory name: " . $name);

            // Directory with 0777 access
            $stat['mode'] = $stat[2] = self::S_IRWXALL | self::S_IFDIR;
        }

        $this->logger->debug("buildUrlStat returns " . json_encode($stat));

        return $stat;
    }

    /**
     * Maybe log an error.
     * @param string $message error message
     * @param int $flags flags used to suppress errors
     */
    protected function maybe_trigger_error($message, $flags = 0)
    {
        if ($flags & STREAM_URL_STAT_QUIET) {
            // don't log errors, e.g. in file_exists()

            if ($flags & STREAM_URL_STAT_LINK) {
                // this is about the symlink itself, e.g. is_link()
                return $this->buildUrlStat(false);
            }

            return false;
        }

        return $this->trigger_error($message);
    }

    /**
     * Log an error.
     * @param string $message error message
     * @return false false always
     */
    protected function trigger_error($message)
    {
        $errorLogger = $this->logger->scope("trigger_error");
        $errorLogger->debug($message);
        $errorLogger->scope("verbose")->debug(StringUtils::generateCallTrace());

        trigger_error($message, E_USER_WARNING);

        return false;
    }
}
