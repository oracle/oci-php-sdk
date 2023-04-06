<?php
/**Copyright (c) 2023, Oracle and/or its affiliates. All rights reserved.
 * This software is dual-licensed to you under the Universal Permissive License
 * (UPL) 1.0 as shown at https://oss.oracle.com/licenses/upl or Apache License
 * 2.0 as shown at http://www.apache.org/licenses/LICENSE-2.0. You may choose
 * either license.
*/
namespace Oracle\Oci\ObjectStorage\Transfer;

use Oracle\Oci\Common\OciException;
use Oracle\Oci\Common\Logging\Logger;
use Oracle\Oci\ObjectStorage\ObjectStorageAsyncClient;
use UploadManagerConstants;

class MultipartStreamUploader extends AbstractMultipartUploader
{
    public function __construct(ObjectStorageAsyncClient $client, $uploadId, UploadManagerRequest &$uploadManagerRequest)
    {
        $this->uploadId = $uploadId;
        parent::__construct($client, $uploadManagerRequest);
    }

    protected function prepareSources()
    {
        $handle = $this->uploadManagerRequest->getSource();
        if (!$handle) {
            throw new OciException("Unable to get the stream");
        }
        $partNum = 1;
        $partSize = $this->config[UploadManagerConstants::PART_SIZE_IN_BYTES];
        Logger::logger(static::class)->debug("Preparing multi-part upload: " . json_encode($this->config));
        while (!feof($handle)) {
            $position = ftell($handle);
            $content = fread($handle, $partSize);
            if (strlen($content) == 0) {
                break;
            }
            Logger::logger(static::class)->debug("Yielding data for partNum: $partNum");
            yield [
                'partNum' => $partNum,
                'content' => &$content,
                'length' => strlen($content),
                'position' => $position,
            ];
            $partNum++;
        }
    }
}
