<?php
/**Copyright (c) 2023, Oracle and/or its affiliates. All rights reserved.
 * This software is dual-licensed to you under the Universal Permissive License
 * (UPL) 1.0 as shown at https://oss.oracle.com/licenses/upl or Apache License
 * 2.0 as shown at http://www.apache.org/licenses/LICENSE-2.0. You may choose
 * either license.
*/
namespace Oracle\Oci\ObjectStorage\Transfer;

use Oracle\Oci\ObjectStorage\ObjectStorageAsyncClient;
use GuzzleHttp\Promise\PromisorInterface;

abstract class AbstractUploader implements PromisorInterface
{
    protected $client;

    protected $uploadManagerRequest;

    protected $promise;

    public function __construct(ObjectStorageAsyncClient $client, UploadManagerRequest &$uploadManagerRequest)
    {
        $this->client = $client;
        $this->uploadManagerRequest = $uploadManagerRequest;
    }

    public function promise()
    {
        if ($this->promise) {
            return $this->promise;
        }
        return $this->promise = $this->prepareUpload();
    }

    protected function initUploadRequest()
    {
        return array_merge($this->uploadManagerRequest->getExtras(), [
            'namespaceName' => $this->uploadManagerRequest->getNamespace(),
            'bucketName' => $this->uploadManagerRequest->getBucketName(),
            'objectName' => $this->uploadManagerRequest->getObjectName(),
        ]);
    }

    abstract protected function prepareUpload();
}
