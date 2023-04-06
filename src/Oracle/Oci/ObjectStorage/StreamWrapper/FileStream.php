<?php
/**Copyright (c) 2023, Oracle and/or its affiliates. All rights reserved.
 * This software is dual-licensed to you under the Universal Permissive License
 * (UPL) 1.0 as shown at https://oss.oracle.com/licenses/upl or Apache License
 * 2.0 as shown at http://www.apache.org/licenses/LICENSE-2.0. You may choose
 * either license.
*/
namespace Oracle\Oci\ObjectStorage\StreamWrapper;

use InvalidArgumentException;
use Oracle\Oci\ObjectStorage\ObjectStorageClient;

/**
 * This stream reads from a file, either by name (set FILE_NAME_PARAM) or from an already open file handle (set FILE_HANDLE_PARAM).
 */
class FileStream extends AbstractHttpStream
{
    const FILE_HANDLE_PARAM = 'fileHandle';
    const FILE_NAME_PARAM = 'fileName';
    private $fileName;

    /**
     * @param array $params
     * @param ObjectStorageClient $client
     */
    public function __construct($params, $client)
    {
        if ((!array_key_exists(self::FILE_HANDLE_PARAM, $params) && !array_key_exists(self::FILE_NAME_PARAM, $params)) ||
            (array_key_exists(self::FILE_HANDLE_PARAM, $params) && array_key_exists(self::FILE_NAME_PARAM, $params))) {
            throw new InvalidArgumentException("Exactly one of " . self::FILE_HANDLE_PARAM . " or " . self::FILE_NAME_PARAM . " must be provided");
        }
        if (array_key_exists(self::FILE_HANDLE_PARAM, $params)) {
            $this->fh = $params[self::FILE_HANDLE_PARAM];
            $this->fileName = null;
        } else {
            $this->fileName = $params[self::FILE_NAME_PARAM];
        }
        parent::__construct($params, $client);
    }

    protected function openStream()
    {
        if ($this->fileName) {
            $this->fh = fopen($this->fileName, "r");
        }
        return $this->fh;
    }
}
