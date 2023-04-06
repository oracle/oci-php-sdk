<?php
/**Copyright (c) 2023, Oracle and/or its affiliates. All rights reserved.
 * This software is dual-licensed to you under the Universal Permissive License
 * (UPL) 1.0 as shown at https://oss.oracle.com/licenses/upl or Apache License
 * 2.0 as shown at http://www.apache.org/licenses/LICENSE-2.0. You may choose
 * either license.
*/
namespace Oracle\Oci\Common;

use InvalidArgumentException;
use Oracle\Oci\Common\Logging\Logger;

/**
 * Representation of a byte range. start, end, or both may be set.
 */
class Range
{
    /**
     * @var int|null
     */
    protected $start;

    /**
     * @var int|null
     */
    protected $end;

    /**
     * @var int|null
     */
    protected $contentLength;

    public function __construct($start, $end, $contentLength=null)
    {
        $this->start = $start;
        $this->end = $end;
        $this->contentLength = $contentLength;
    }

    public function __toString()
    {
        if ($this->start !== null && $this->end !== null) {
            return "bytes={$this->start}-{$this->end}";
        } elseif ($this->start !== null) {
            return "bytes={$this->start}-";
        } elseif ($this->end !== null) {
            return "bytes=-{$this->end}";
        } else {
            Logger::logger(static::class)->error("Must provide start/end byte for range request");
            return "";
        }
    }

    /**
     * Return the start byte.
     * @return int|null start byte
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Return the end byte.
     * @return int|null end byte
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Return the content length.
     * @return int|null content length
     */
    public function getContentLength()
    {
        return $this->contentLength;
    }



    /**
     * Parse a byte range.
     * @param string $value the byte range as string
     * @return Range the parsed range
     */
    public static function parse($value)
    {
        $logger = Logger::logger(static::class);
        $logger->debug("Attempting to parse range: $value");
        $value = trim(str_replace("bytes", "", $value));
        $parts = explode('/', $value);
        if (count($parts) != 2) {
            throw new InvalidArgumentException("Must provide <range>/<length> format for range request: $value");
        } else {
            $byteRangePart = $parts[0];
            $contentLengthPart = $parts[1];
            $contentLength = $contentLengthPart == "*" ? null : (int)($contentLengthPart);
            $byteValues = explode('-', $byteRangePart);
            if (count($byteValues) != 2) {
                throw new InvalidArgumentException("Must provide <start>-<end> format for range request: $value");
            } else {
                $startByte = (trim($byteValues[0]) == "") ? null : (int)($byteValues[0]);
                $endByte = (trim($byteValues[1]) == "") ? null : (int)($byteValues[1]);
                if ($startByte == null && $endByte == null) {
                    throw new InvalidArgumentException("Must provide start/end byte for range request: $value");
                } else {
                    $range = new Range($startByte, $endByte, $contentLength);
                    return $range;
                }
            }
        }
    }
}
