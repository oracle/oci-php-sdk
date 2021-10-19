<?php

namespace Oracle\Oci\Common;

class OciResponse 
{
    protected /*int*/ $statusCode;
    protected $headers;
    protected $body;
    protected /*mixed*/ $json;
    
    public function __construct(
        /*int*/ $statusCode,
        $headers,
        $body = null,
        /*mixed*/ $json = null)
    {
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

    public function echoResponse()
    {
        echo "Status code: " . $this->getStatusCode() . PHP_EOL;
        echo "Headers    : " . PHP_EOL;
        foreach ($this->getHeaders() as $name => $values) {
            echo $name . ': ' . implode(', ', $values) . "\r\n";
        }
        if ($this->json == null)
        {
            echo "Body       : " . $this->getBody() . PHP_EOL;

        } else {
            echo "JSON Body  : " . json_encode($this->getJson(), JSON_PRETTY_PRINT) . PHP_EOL;
        }
    }
}
?>