<?php
/**Copyright (c) 2023, Oracle and/or its affiliates. All rights reserved.
 * This software is dual-licensed to you under the Universal Permissive License
 * (UPL) 1.0 as shown at https://oss.oracle.com/licenses/upl or Apache License
 * 2.0 as shown at http://www.apache.org/licenses/LICENSE-2.0. You may choose
 * either license.
*/
namespace Oracle\Oci\ObjectStorage\Transfer;

use InvalidArgumentException;
use Oracle\Oci\Common\Logging\Logger;
use Oracle\Oci\Common\OciException;
use Oracle\Oci\Common\StringUtils;

class UploadManagerRequest
{
    protected $namespace;
    protected $bucketName;
    protected $objectName;
    protected $extras;

    protected $source;
    protected $uploadConfig;

    protected $size;

    public function __construct($namespace, $bucketName, $objectName, $source, $extras=[], $uploadConfig=[])
    {
        $this->namespace = $namespace;
        $this->bucketName = $bucketName;
        $this->objectName = $objectName;
        $this->source = $source;
        $this->extras = $extras;
        $this->uploadConfig = $uploadConfig;
        $stat = fstat($this->source);
        if (!$stat) {
            $this->size = -1;
        } else {
            $this->size = $stat['size'];
        }
    }

    /**
     * Helper method for creating an uploadManagerRequest
     *
     * @param string $namespace
     * @param string $bucketName
     * @param string $objectName
     * @param string|resource  $source the resource of the object, can be either a string to path or a raw string with content or a stream
     * @param mixed $extras optional properties to config the upload request
     * @param mixed $uploadConfig optional properties to configure upload manager per request
     */
    public static function createUploadManagerRequest($namespace, $bucketName, $objectName, $source, $extras=[], $uploadConfig=[])
    {
        if (is_string($source) && file_exists($source)) {
            return new UploadManagerUploadFileRequest($namespace, $bucketName, $objectName, $source, $extras, $uploadConfig);
        }
        if (is_string($source)) {
            return new UploadManagerUploadStringRequest($namespace, $bucketName, $objectName, $source, $extras, $uploadConfig);
        }
        if (! is_resource($source) || 'stream' != get_resource_type($source)) {
            throw new InvalidArgumentException("Invalid stream provided, please provide a valid stream resource; was given " . StringUtils::get_type_or_class($source));
        }
        Logger::logger(static::class)->debug("Constructing UploadManagerRequest for $bucketName@$namespace/$objectName");
        return new UploadManagerRequest($namespace, $bucketName, $objectName, $source, $extras, $uploadConfig);
    }

    public function updateUploadConfig($uploadConfig)
    {
        $this->uploadConfig += $uploadConfig;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }
    public function getBucketName()
    {
        return $this->bucketName;
    }

    public function getObjectName()
    {
        return $this->objectName;
    }
    public function getSource()
    {
        return $this->source;
    }
    public function getSize()
    {
        return $this->size;
    }
    public function getUploadConfig()
    {
        return $this->uploadConfig;
    }
    public function getExtras()
    {
        return $this->extras;
    }
}

class UploadManagerUploadFileRequest extends UploadManagerRequest
{
    protected $filePath;

    public function __construct($namespace, $bucketName, $objectName, $filePath, $extras=[], $uploadConfig=[])
    {
        Logger::logger(static::class)->debug("Constructing UploadManagerUploadFileRequest for $bucketName@$namespace/$objectName");
        $this->filePath = realpath($filePath);
        $source = fopen($this->filePath, 'r');
        if (!$source) {
            throw new InvalidArgumentException("unable to open file: $source");
        }

        parent::__construct($namespace, $bucketName, $objectName, $source, $extras, $uploadConfig);
    }

    public function __destruct()
    {
        Logger::logger(static::class)->debug("Destructing UploadManagerUploadFileRequest, closing file");
        fclose($this->source);
    }
}

class UploadManagerUploadStringRequest extends UploadManagerRequest
{
    protected $string;

    public function __construct($namespace, $bucketName, $objectName, &$string, $extras=[], $uploadConfig=[])
    {
        Logger::logger(static::class)->debug("Constructing UploadManagerUploadStringRequest for $bucketName@$namespace/$objectName");
        $source = fopen('php://temp', 'r+');
        if (!$source) {
            throw new OciException("unable to create php://temp source");
        }

        fwrite($source, $string);
        rewind($source);

        parent::__construct($namespace, $bucketName, $objectName, $source, $extras, $uploadConfig);
    }

    public function __destruct()
    {
        Logger::logger(static::class)->debug("Destructing UploadManagerUploadFileRequest, closing temp stream");
        fclose($this->source);
    }
}
