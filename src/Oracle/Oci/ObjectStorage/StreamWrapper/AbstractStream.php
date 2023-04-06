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
use Oracle\Oci\Common\Logging\NamedLogAdapterDecorator;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

/**
 * Abstract stream class.
 */
abstract class AbstractStream implements StreamInterface, HasFileHandle
{
    /**
     * @var NamedLogAdapterDecorator
     */
    protected $logger;

    protected $fh;

    public function __construct()
    {
        $this->logger = Logger::logger(static::class);
    }

    abstract protected function openStream();

    /**
     * Return the underlying file handle resource.
     * @return resource file handle resource
     */
    public function getStream()
    {
        return $this->fh;
    }

    /**
    * Reads all data from the stream into a string, from the beginning to end.
    *
    * This method MUST attempt to seek to the beginning of the stream before
    * reading data and read the stream until the end is reached.
    *
    * Warning: This could attempt to load a large amount of data into memory.
    *
    * This method MUST NOT raise an exception in order to conform with PHP's
    * string casting operations.
    *
    * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
    * @return string
    */
    public function __toString()
    {
        if ($this->fh == null) {
            $this->logger->error("Stream has been detached, illegal to call " . __CLASS__ . "::" . __METHOD__);
            return "";
        }
        try {
            $this->seek(0, SEEK_SET);
        } catch (Exception $e) {
            // ignore
        }
        return stream_get_contents($this->fh);
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close()
    {
        if ($this->fh == null) {
            throw new RuntimeException("Stream has been detached, illegal to call " . __CLASS__ . "::" . __METHOD__);
        }
        // For some reason, the single-part UploadManager makes the stream resource not be a valid stream resource anymore
        if (is_resource($this->fh)) {
            fclose($this->fh);
        } else {
            $this->logger->debug("Temp stream was not a valid stream resource anymore: {$this->fh}");
        }
        $this->fh = null;
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        if ($this->fh == null) {
            throw new RuntimeException("Stream has been detached, illegal to call " . __CLASS__ . "::" . __METHOD__);
        }

        $stream = $this->fh;
        $this->fh = null;

        return $stream;
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
        if (is_resource($this->fh)) {
            try {
                $metadata = stream_get_meta_data($this->fh);
                if ($metadata && array_key_exists('wrapper_data', $metadata)) { // @phpstan-ignore-line (left side of && always true)
                    $wrapper_data = $metadata['wrapper_data'];
                    if (is_array($wrapper_data)) {
                        foreach ($wrapper_data as $headerLine) {
                            if (strpos(strtolower($headerLine), 'content-length: ') === 0) {
                                return (int)(substr($headerLine, 16));
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                // ignore, best effort
            }
        }
        return null;
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     * @throws \RuntimeException on error.
     */
    public function tell()
    {
        if ($this->fh == null) {
            throw new RuntimeException("Stream has been detached, illegal to call " . __CLASS__ . "::" . __METHOD__);
        }
        return ftell($this->fh);
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof()
    {
        if ($this->fh == null) {
            throw new RuntimeException("Stream has been detached, illegal to call " . __CLASS__ . "::" . __METHOD__);
        }
        $result = feof($this->fh);
        return $result;
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable()
    {
        if ($this->fh == null) {
            throw new RuntimeException("Stream has been detached, illegal to call " . __CLASS__ . "::" . __METHOD__);
        }
        
        return $this->getMetadata('seekable');
    }

    /**
     * Seek to a position in the stream.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *     based on the seek offset. Valid values are identical to the built-in
     *     PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *     offset bytes SEEK_CUR: Set position to current location plus offset
     *     SEEK_END: Set position to end-of-stream plus offset.
     * @throws \RuntimeException on failure.
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if ($this->fh == null) {
            throw new RuntimeException("Stream has been detached, illegal to call " . __CLASS__ . "::" . __METHOD__);
        }
        if (!$this->isSeekable()) {
            throw new RuntimeException("Stream is not seekable");
        }
        $result = fseek($this->fh, $offset, $whence);
        if ($result == -1) {
            throw new RuntimeException("Failed to seek");
        }
        return $result;
    }

    /**
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform a seek(0).
     *
     * @see seek()
     * @link http://www.php.net/manual/en/function.fseek.php
     * @throws \RuntimeException on failure.
     */
    public function rewind()
    {
        if ($this->fh == null) {
            throw new RuntimeException("Stream has been detached, illegal to call " . __CLASS__ . "::" . __METHOD__);
        }
        return $this->seek(0, SEEK_SET);
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
        return false;
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
        throw new RuntimeException("Stream is not writable");
    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    public function isReadable()
    {
        if ($this->fh == null) {
            throw new RuntimeException("Stream has been detached, illegal to call " . __CLASS__ . "::" . __METHOD__);
        }
        return true;
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     * @throws \RuntimeException if unable to read or an error occurs while
     *     reading.
     */
    public function getContents()
    {
        if ($this->fh == null) {
            throw new RuntimeException("Stream has been detached, illegal to call " . __CLASS__ . "::" . __METHOD__);
        }
        return stream_get_contents($this->fh);
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     * @param string $key Specific metadata to retrieve.
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata($key = null)
    {
        if ($this->fh == null) {
            throw new RuntimeException("Stream has been detached, illegal to call " . __CLASS__ . "::" . __METHOD__);
        }

        $this->logger->debug("getMetadata on {$this->fh}");

        $metadata = stream_get_meta_data($this->fh);
        if ($key == null) {
            return $metadata;
        } else {
            if (!array_key_exists($key, $metadata)) {
                return null;
            }
            return $metadata[$key];
        }
    }
}
