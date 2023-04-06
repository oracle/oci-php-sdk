<?php
/**Copyright (c) 2023, Oracle and/or its affiliates. All rights reserved.
 * This software is dual-licensed to you under the Universal Permissive License
 * (UPL) 1.0 as shown at https://oss.oracle.com/licenses/upl or Apache License
 * 2.0 as shown at http://www.apache.org/licenses/LICENSE-2.0. You may choose
 * either license.
*/
namespace Oracle\Oci\ObjectStorage\StreamWrapper;

use Exception;
use Oracle\Oci\Common\Logging\Logger;
use Oracle\Oci\ObjectStorage\ObjectStorageAsyncClient;
use Oracle\Oci\ObjectStorage\ObjectStorageClient;
use Oracle\Oci\ObjectStorage\Transfer\UploadManager;
use Oracle\Oci\ObjectStorage\Transfer\UploadManagerRequest;

/**
 * An uploader using the UploadManager.
 */
class UploadManagerUploader implements UploaderInterface
{
    /**
     * The async Object Storage client.
     * @var ObjectStorageAsyncClient
     */
    private $asyncClient;

    /**
     * The upload manager.
     * @var UploadManager
     */
    private $uploadManager;

    /**
     * Extras.
     * @var array
     */
    private $extras;

    /**
     * Construct the uploader.
     * @param ObjectStorageClient $client the Object Storage client
     * @param array $extras optional properties to config the upload request
     * @param array $uploadManagerOptions optional settings for the upload manager
     */
    public function __construct(ObjectStorageClient $client, $extras = [], $uploadManagerOptions = [])
    {
        $this->asyncClient = new ObjectStorageAsyncClient($client->getAuthProvider(), $client->getRegion(), $client->getEndpoint());
        $this->uploadManager = new UploadManager($this->asyncClient, $uploadManagerOptions);
        $this->extras = $extras;
    }

    /**
     * Set optional properties to config the upload request.
     * @param array $extras
     */
    public function setExtras($extras)
    {
        $this->extras = $extras;
    }

    /**
     * Upload the stream.
     * @param array $params upload parameters
     * @param AbstractHttpStream $stream stream to upload
     */
    public function upload($params, $stream)
    {
        $uploadLogger = Logger::logger(static::class);

        $uploadLogger->debug("Calling upload, putObjectParams=" . json_encode($params));

        try {
            $uploadPromiseForStream = $this->uploadManager->upload(UploadManagerRequest::createUploadManagerRequest(
                $params[StreamWrapper::NAMESPACE_NAME_PARAM],
                $params[StreamWrapper::BUCKET_NAME_PARAM],
                $params[StreamWrapper::OBJECT_NAME_PARAM],
                $stream->getStream(),
                $this->extras
            ));

            $uploadLogger->debug("Upload manager returned promise, started to upload");
            
            $response = $uploadPromiseForStream->wait();

            $uploadLogger->debug("Response from upload: $response");
        } catch (Exception $e) {
            $uploadLogger->debug("Exception: $e");
        }
    }
}
