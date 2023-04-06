<?php
/**Copyright (c) 2023, Oracle and/or its affiliates. All rights reserved.
 * This software is dual-licensed to you under the Universal Permissive License
 * (UPL) 1.0 as shown at https://oss.oracle.com/licenses/upl or Apache License
 * 2.0 as shown at http://www.apache.org/licenses/LICENSE-2.0. You may choose
 * either license.
*/
namespace Oracle\Oci\ObjectStorage\Transfer;

use Oracle\Oci\Common\Logging\Logger;
use Oracle\Oci\ObjectStorage\ObjectStorageAsyncClient;

class SinglePartUploader extends AbstractUploader
{
    public function __construct(ObjectStorageAsyncClient $client, UploadManagerRequest &$uploadManagerRequest)
    {
        parent::__construct($client, $uploadManagerRequest);
    }

    protected function prepareUpload()
    {
        $initUploadRequest = $this->initUploadRequest();
        Logger::logger(static::class)->debug("Preparing single-part upload: " . json_encode($initUploadRequest));
        return $this->client->putObjectAsync(array_merge([
            'putObjectBody' => $this->uploadManagerRequest->getSource(),
        ], $initUploadRequest));
    }
}
