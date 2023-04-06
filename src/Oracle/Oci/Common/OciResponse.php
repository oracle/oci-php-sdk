<?php
/**Copyright (c) 2023, Oracle and/or its affiliates. All rights reserved.
 * This software is dual-licensed to you under the Universal Permissive License
 * (UPL) 1.0 as shown at https://oss.oracle.com/licenses/upl or Apache License
 * 2.0 as shown at http://www.apache.org/licenses/LICENSE-2.0. You may choose
 * either license.
*/
namespace Oracle\Oci\Common;

use Oracle\Oci\Common\Logging\Logger;

class OciResponse
{
    /*LogAdapterInterface*/ protected static $logger;

    /*int*/ protected $statusCode;
    protected $headers;
    protected $body;
    /*mixed*/ protected $json;

    public function __construct(
        /*int*/
        $statusCode,
        $headers,
        $body = null,
        /*mixed*/
        $json = null
    ) {
        if (OciResponse::$logger == null) {
            OciResponse::$logger = Logger::logger(static::class);
        }
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = $body;
        $this->json = $json;
    }

    public function getStatusCode() // : int
    {
        return $this->statusCode;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getJson()
    {
        return $this->json;
    }

    public function humanReadable($printBinaryBody = false)
    {
        $s = "Status code: " . $this->getStatusCode() . PHP_EOL;
        $s .= "Headers    : " . PHP_EOL;
        foreach ($this->getHeaders() as $name => $values) {
            $s .= $name . ': ' . implode(', ', $values) . PHP_EOL;
        }
        if ($this->json == null) {
            if ($printBinaryBody) {
                $s .= "Body       : " . $this->getBody() . PHP_EOL;
            } else {
                $s .= "Body       : <binary body elided>" . PHP_EOL;
            }
        } else {
            $s .= "JSON Body  : " . json_encode($this->getJson(), JSON_PRETTY_PRINT) . PHP_EOL;
        }
        return $s;
    }

    public function __toString()
    {
        return $this->humanReadable();
    }
}
