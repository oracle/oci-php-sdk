<?php
/**Copyright (c) 2023, Oracle and/or its affiliates. All rights reserved.
 * This software is dual-licensed to you under the Universal Permissive License
 * (UPL) 1.0 as shown at https://oss.oracle.com/licenses/upl or Apache License
 * 2.0 as shown at http://www.apache.org/licenses/LICENSE-2.0. You may choose
 * either license.
*/
namespace Oracle\Oci\ObjectStorage\StreamWrapper;

use DateInterval;
use DateTime;
use Exception;
use Oracle\Oci\Common\HttpUtils;
use Oracle\Oci\Common\Logging\Logger;
use Oracle\Oci\Common\OciBadResponseException;
use Oracle\Oci\ObjectStorage\ObjectStorageClient;
use RuntimeException;

/**
 * This stream reads using HTTPS, but create pre-authenticated requests to allow reading even from private buckets.
 *
 * This class does allow streaming and only reading parts of the stream.
 */
class ParHttpStream extends AbstractHttpStream
{
    // 30 seconds by default
    const DEFAULT_PAR_LIFETIME = "PT30S";
    const PAR_LIFETIME_PARAM = "parLifetime";
    const DEFAULT_PAR_RETRIES = 4;
    const PAR_RETRIES_PARAM = "parRetries";

    /**
     * @var DateInterval
     */
    private $parLifetime;

    /**
     * @var string|null
     */
    private $parId;

    /**
     * @var string|null
     */
    private $parUri;

    /**
     * @var int
     */
    private $position;

    /**
     * @var int
     */
    private $parRetriesRemaining;

    /**
     * @param array $params
     * @param ObjectStorageClient $client
     */
    public function __construct($params, $client)
    {
        $this->logger = Logger::logger(static::class);
        $this->client = $client;
        $this->params = $params;
        if (array_key_exists(self::PAR_LIFETIME_PARAM, $params)) {
            $this->parLifetime = self::getDateInterval($params[self::PAR_LIFETIME_PARAM]);
        } else {
            $this->parLifetime = self::getDateInterval(self::DEFAULT_PAR_LIFETIME);
        }
        if (array_key_exists(self::PAR_RETRIES_PARAM, $params)) {
            $this->parRetriesRemaining = (int)($params[self::PAR_RETRIES_PARAM]);
        } else {
            $this->parRetriesRemaining = self::DEFAULT_PAR_RETRIES;
        }
        $this->createPar();
        parent::__construct($params, $client);
        $this->position = 0;
    }

    private static function getDateInterval($di)
    {
        if (is_string($di)) {
            return new DateInterval($di);
        }
        return $di;
    }

    protected function openStream()
    {
        $this->fh = fopen($this->client->getEndpoint() . $this->parUri, "r");
        if ($this->fh === false) {
            throw new RuntimeException("Could not open stream");
        }
        return $this->fh;
    }

    private function createPar()
    {
        $parLogger = $this->logger->scope("par");

        $dt = new DateTime();
        $dt->add($this->parLifetime);
        $body = [
            'name' => 'par-php-' . uniqid(),
            'objectName' => $this->params[StreamWrapper::OBJECT_NAME_PARAM],
            'accessType' => 'ObjectRead',
            'timeExpires' => HttpUtils::encodeDateTime($dt)
        ];
        $parLogger->debug("creating PAR, request details: " . json_encode($body));
        $response = $this->client->createPreauthenticatedRequest_Helper(
            $this->params[StreamWrapper::NAMESPACE_NAME_PARAM],
            $this->params[StreamWrapper::BUCKET_NAME_PARAM],
            $body
        );
        $this->parId = $response->getJson()->id;
        $this->parUri = $response->getJson()->accessUri;
        $this->logger->debug("Created new PAR, OCID {$this->parId}, URI: <elided>");
        $this->logger->scope("sensitive")->debug("Created new PAR, id {$this->parId}, URI: {$this->parUri}");
    }

    private function deletePar()
    {
        $parLogger = $this->logger->scope("par");
        try {
            $parLogger->debug("Deleting PAR, OCID {$this->parId}...");
            $this->client->deletePreauthenticatedRequest_Helper(
                $this->params[StreamWrapper::NAMESPACE_NAME_PARAM],
                $this->params[StreamWrapper::BUCKET_NAME_PARAM],
                $this->parId
            );
            $parLogger->debug("Successfully deleted PAR, id {$this->parId}.");
        } catch (OciBadResponseException $obre) {
            $parLogger->debug("Failed to delete PAR, id {$this->parId}: $obre");
            // ignoring
        }
        $this->parId = null;
        $this->parUri = null;
    }

    private function tryDeletePar()
    {
        try {
            $this->deletePar();
        } catch (OciBadResponseException $obre) {
            // ignore
        }
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close()
    {
        parent::close();
        $this->deletePar();
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
        $stream = parent::detach();

        // we'll leave the PAR there, but it won't get refreshed anymore
        $this->parUri = null;
        $this->parId = null;

        return $stream;
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     * @throws \RuntimeException on error.
     */
    public function tell()
    {
        $this->position = parent::tell();
        return $this->position;
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
        $this->logger->debug("Calling seek, offset $offset, whence $whence");
        $result = parent::seek($offset, $whence);
        if ($result) {
            // successful seek
            if ($whence == SEEK_SET) {
                $this->position = $offset;
            } else {
                $this->tell();
            }
            $this->logger->debug("Successful seek, new position is {$this->position}");
        }
        return $result;
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
        $result = "";
        do {
            try {
                $result = parent::read($length);
                break;
            } catch (Exception $e) {
                $this->logger->info("Exception occurred while reading stream: " . $e);
                $this->tryDeletePar();
                if ($this->parRetriesRemaining == 0) {
                    $this->logger->info("Exhausted retries, giving up");
                    throw $e;
                }
                $this->logger->debug("Creating new PAR and re-opening stream...");
                $this->createPar();
                $this->fh = $this->openStream();
                if ($this->isSeekable()) {
                    $this->logger->debug("Seeking to position {$this->position}...");
                    fseek($this->fh, $this->position);
                } else {
                    $this->logger->debug("Seeking not supported, reading to position {$this->position}...");
                    $remaining = $this->position;
                    while ($remaining > 0) {
                        $s = fread($this->fh, $remaining);
                        $remaining -= strlen($s);
                    }
                }
                $actualPos = ftell($this->fh);
                $this->logger->debug("Position in stream: " . $actualPos . ", desired position: {$this->position}");
                if ($actualPos != $this->position) {
                    $this->tryDeletePar();
                    throw new RuntimeException("Failed to seek to the correct position");
                }
                $this->logger->info("Attempting to read again...");
            }
        } while ($this->parRetriesRemaining-- > 0);
        if ($result) {
            $this->position += strlen($result);
        }
        return $result;
    }
}
