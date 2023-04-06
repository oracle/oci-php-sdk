<?php
/**Copyright (c) 2023, Oracle and/or its affiliates. All rights reserved.
 * This software is dual-licensed to you under the Universal Permissive License
 * (UPL) 1.0 as shown at https://oss.oracle.com/licenses/upl or Apache License
 * 2.0 as shown at http://www.apache.org/licenses/LICENSE-2.0. You may choose
 * either license.
*/
namespace Oracle\Oci\ObjectStorage\StreamWrapper;

use Oracle\Oci\Common\Logging\Logger;
use Oracle\Oci\ObjectStorage\ObjectStorageClient;
use Psr\Http\Message\StreamInterface;

/**
 * An uploader using putObject
 */
class PutObjectUploader implements UploaderInterface
{
    /**
     * The Object Storage client.
     * @var ObjectStorageClient
     */
    private $client;

    /**
     * Construct the uploader.
     * @param ObjectStorageClient $client the Object Storage client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * Upload the stream.
     * @param array $params upload parameters
     * @param StreamInterface $stream stream to upload
     */
    public function upload($params, $stream)
    {
        $uploadLogger = Logger::logger(static::class);

        $params['putObjectBody'] = $stream;

        $uploadLogger->debug("Calling putObject, putObjectParams=" . json_encode($params));
        $response = $this->client->putObject($params);
        $uploadLogger->debug("Response from putObject: $response");
    }
}
