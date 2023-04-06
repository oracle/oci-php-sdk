<?php
/**Copyright (c) 2023, Oracle and/or its affiliates. All rights reserved.
 * This software is dual-licensed to you under the Universal Permissive License
 * (UPL) 1.0 as shown at https://oss.oracle.com/licenses/upl or Apache License
 * 2.0 as shown at http://www.apache.org/licenses/LICENSE-2.0. You may choose
 * either license.
*/
namespace Oracle\Oci\ObjectStorage\StreamWrapper;

use Oracle\Oci\Common\Logging\Logger;
use Oracle\Oci\Common\OciException;
use Oracle\Oci\ObjectStorage\ObjectStorageClient;
use RuntimeException;

/**
 * This stream reads using HTTPS, but create pre-authenticated requests to allow reading even from private buckets.
 */
abstract class AbstractHttpStream extends AbstractStream
{
    const TESTS_FAIL_ON_READ_COUNT_PARAM = "tests.stream.failOnReadCount";

    /**
     * @var ObjectStorageClient
     */
    protected $client;
    
    /**
     * @var array
     */
    protected $params;

    /**
     * @var int
     */
    private $readCount = 0;

    /**
     * @var int|null
     */
    private $failOnReadCount = null;

    /**
     * @param array $params
     * @param ObjectStorageClient $client
     */
    public function __construct($params, $client)
    {
        parent::__construct();
        $this->logger = Logger::logger(static::class);
        $this->client = $client;
        $this->params = $params;

        if (array_key_exists(self::TESTS_FAIL_ON_READ_COUNT_PARAM, $params)) {
            $this->failOnReadCount = (int)($params[self::TESTS_FAIL_ON_READ_COUNT_PARAM]);
        }

        $this->fh = $this->openStream();
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

        if ($this->failOnReadCount != null) {
            if ($this->readCount++ == $this->failOnReadCount) {
                throw new OciException(401);
            }
        }

        return fread($this->fh, $length);
    }
}
