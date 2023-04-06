<?php
/**Copyright (c) 2023, Oracle and/or its affiliates. All rights reserved.
 * This software is dual-licensed to you under the Universal Permissive License
 * (UPL) 1.0 as shown at https://oss.oracle.com/licenses/upl or Apache License
 * 2.0 as shown at http://www.apache.org/licenses/LICENSE-2.0. You may choose
 * either license.
*/
namespace Oracle\Oci\ObjectStorage\StreamWrapper;

use Oracle\Oci\Common\Logging\Logger;
use RuntimeException;

/**
 * This stream writes to a temporary stream ('php://temp').
 */
class WriteStream extends AbstractStream
{
    public function __construct()
    {
        parent::__construct();
        $this->logger = Logger::logger(static::class);

        $this->fh = $this->openStream();
    }

    protected function openStream()
    {
        $this->fh = fopen('php://temp', 'r+');
        return $this->fh;
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize()
    {
        if ($this->fh == null) {
            throw new RuntimeException("Stream has been detached, illegal to call " . __CLASS__ . "::" . __METHOD__);
        }
        return fstat($this->fh)['size'];
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable()
    {
        if ($this->fh == null) {
            throw new RuntimeException("Stream has been detached, illegal to call " . __CLASS__ . "::" . __METHOD__);
        }
        return true;
    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     * @return int Returns the number of bytes written to the stream.
     * @throws \RuntimeException on failure.
     */
    public function write($string)
    {
        if ($this->fh == null) {
            throw new RuntimeException("Stream has been detached, illegal to call " . __CLASS__ . "::" . __METHOD__);
        }
        return fwrite($this->fh, $string);
    }

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return
     *     them. Fewer than $length bytes may be returned if underlying stream
     *     call returns fewer bytes.
     * @return string Returns the data read from the stream, or an empty string
     *     if no bytes are available.
     * @throws \RuntimeException if an error occurs.
     */
    public function read($length)
    {
        if ($this->fh == null) {
            throw new RuntimeException("Stream has been detached, illegal to call " . __CLASS__ . "::" . __METHOD__);
        }

        return fread($this->fh, $length);
    }
}
