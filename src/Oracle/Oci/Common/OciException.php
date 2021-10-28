<?php

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
    private $response;

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
        $this->response = $response;
        $this->statusCode = $response->getStatusCode();
        $this->errorCode = $response->getReasonPhrase();
        $this->message = $response->getBody()->getContents();
        $this->opcRequestId = $response->getHeader('opc-request-id')[0];

        # TODO
        $targetService = "";
        $operationName = "";
        $timestamp = "";
        $requestEndpoint = "";
        $clientVersion = "";
        $operationReferenceLink = "";
        $errorTroubleshootingLink = "";
    }

    public function __toString()
    {
        return "Error returned by $this->targetService Service. Http Status Code: $this->statusCode. Error Code: $this->errorCode. Message: $this->message. Opc request id: $this->opcRequestId.\n";
    }
}
