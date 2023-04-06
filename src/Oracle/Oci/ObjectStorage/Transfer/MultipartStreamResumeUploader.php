<?php
/**Copyright (c) 2023, Oracle and/or its affiliates. All rights reserved.
 * This software is dual-licensed to you under the Universal Permissive License
 * (UPL) 1.0 as shown at https://oss.oracle.com/licenses/upl or Apache License
 * 2.0 as shown at http://www.apache.org/licenses/LICENSE-2.0. You may choose
 * either license.
*/
namespace Oracle\Oci\ObjectStorage\Transfer;

use InvalidArgumentException;
use Oracle\Oci\Common\OciException;
use Oracle\Oci\Common\Logging\Logger;
use Oracle\Oci\ObjectStorage\ObjectStorageAsyncClient;

class MultipartStreamResumeUploader extends AbstractMultipartUploader
{
    protected $pendingPartsToRetry = [];
    public function __construct(ObjectStorageAsyncClient $client, $uploadId, UploadManagerRequest &$uploadManagerRequest, $partsToCommit = [], $partsToRetry = [])
    {
        if (empty($partsToRetry)) {
            throw new InvalidArgumentException("Resume Upload requires non-empty partsToRetry");
        }
        $this->partsToCommit = $partsToCommit;
        $this->pendingPartsToRetry = $partsToRetry;
        $this->uploadId = $uploadId;
        parent::__construct($client, $uploadManagerRequest);
    }


    protected function prepareSources()
    {
        foreach ($this->pendingPartsToRetry as $retryPart) {
            $content = stream_get_contents($this->uploadManagerRequest->getSource(), $retryPart['length'], $retryPart['position']);
            if (!$content) {
                throw new OciException("Unable to read file content");
            }
            Logger::logger(static::class)->debug("Yielding data for partNum: ".$retryPart['partNum']);
            yield [
                'partNum' => $retryPart['partNum'],
                'content' => &$content,
                'length' => $retryPart['length'],
                'position' => $retryPart['position']
            ];
        }
    }
}
