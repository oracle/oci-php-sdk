<?php
/**Copyright (c) 2023, Oracle and/or its affiliates. All rights reserved.
 * This software is dual-licensed to you under the Universal Permissive License
 * (UPL) 1.0 as shown at https://oss.oracle.com/licenses/upl or Apache License
 * 2.0 as shown at http://www.apache.org/licenses/LICENSE-2.0. You may choose
 * either license.
*/
namespace Oracle\Oci\Common;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;

class OciException extends RuntimeException
{
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, Throwable $previous = null)
    {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

    // custom string representation of object
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

class OciBadResponseException extends OciException
{
    protected $statusCode;
    protected $errorCode;
    protected $message;
    protected $opcRequestId;
    protected $targetService;

    protected $operationName;
    protected $timestamp;
    protected $requestEndpoint;
    protected $clientVersion;

    protected $operationReferenceLink;
    protected $errorTroubleshootingLink;

    public function __construct(ResponseInterface &$response)
    {
        $this->statusCode = $response->getStatusCode();
        $bodyContents = $response->getBody()->getContents();
        if ($bodyContents) {
            $json = json_decode($response->getBody());
            $this->errorCode = $json->code;
            $this->message = $json->message;
        } else {
            $this->errorCode = null;
            $this->message = "The service returned HTTP status code {$this->statusCode}.";
        }
        if ($response->hasHeader('opc-request-id')) {
            $this->opcRequestId = $response->getHeader('opc-request-id')[0];
        }

        parent::__construct($this->message, $this->statusCode);

        # TODO
        $targetService = "";
        $operationName = "";
        $timestamp = "";
        $requestEndpoint = "";
        $clientVersion = "";
        $operationReferenceLink = "";
        $errorTroubleshootingLink = "";
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }

    public function getOpcRequestId()
    {
        return $this->opcRequestId;
    }

    public function __toString()
    {
        $service = $this->targetService != null ? $this->targetService . " Service" : "Service";
        return "Error returned by {$service}. Http Status Code: '{$this->statusCode}'. Error Code: '{$this->errorCode}'. Message: '{$this->message}'. Opc request id: '{$this->opcRequestId}'.";
    }
}
