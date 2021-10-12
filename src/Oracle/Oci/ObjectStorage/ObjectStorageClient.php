<?php

namespace Oracle\Oci\ObjectStorage;

use Oracle\Oci\Common\OciResponse;

use Psr\Http\Message\RequestInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Middleware;

class ObjectStorageClient 
{
    // TODO: Factor out into AuthProvider
    protected $tenancy_id;
    protected $user_id;
    protected $thumbprint;
    protected $region;
    protected $key_filename;
    protected $key_passphrase = null;
    protected $key_id;

    protected $client;
    
    public function __construct(
        string $tenancy_id,
        string $user_id,
        string $thumbprint,
        string $region,
        string $key_filename,
        string $key_passphrase = null)
    {
        $this->tenancy_id = $tenancy_id;
        $this->user_id = $user_id;
        $this->thumbprint = $thumbprint;
        $this->region = $region;
        $this->key_filename = $key_filename;
        $this->key_passphrase = $key_passphrase;

        $this->key_id = "$tenancy_id/$user_id/$thumbprint";

        $handler = new CurlHandler();
        $stack = HandlerStack::create($handler);

        // place signing middleware after prepare-body so it can access Content-Length header
        $stack->after('prepare_body', Middleware::mapRequest(function (RequestInterface $request) {
            // headers required for all HTTP verbs
            $headers = "date (request-target) host";
        
            // example: Thu, 05 Jan 2014 21:31:40 GMT
            $date=gmdate("D, d M Y H:i:s T", time());
            $method = strtolower($request->getMethod());
            $request_target = $request->getRequestTarget();
            $host = $request->getHeader('Host')[0];
        
            $request = $request->withHeader('Date', $date);
        
            $signing_string = "date: $date\n(request-target): $method $request_target\nhost: $host";
        
            // additional required headers for POST and PUT requests
            if ($method == 'post' || $method == 'put') {
                $content_length = $request->getHeader('Content-Length')[0];
        
                // if content length is 0 we still need to explicitly send the Content-Length header
                if (!$content_length){
                    $content_length = 0;
                    $request = $request->withHeader('Content-Length', 0);
                }
        
                $content_type = $request->getHeader('Content-Type')[0];
                $content_sha256 = base64_encode(hex2bin(hash("sha256", $request->getBody())));
        
                $request = $request->withHeader('x-content-sha256', $content_sha256);
        
                $headers = $headers . " content-length content-type x-content-sha256";
                $signing_string = $signing_string . "\ncontent-length: $content_length\ncontent-type: $content_type\nx-content-sha256: $content_sha256";
            }
        
            # echo "Signing string:\n$signing_string".PHP_EOL;
        
            $signature = $this->sign_string($signing_string, $this->key_filename, $this->key_passphrase);
        
            $authorization_header = "Signature version=\"1\",keyId=\"$this->key_id\",algorithm=\"rsa-sha256\",headers=\"$headers\",signature=\"$signature\"";
            $request = $request->withHeader('Authorization', $authorization_header);
            
            # echo "\nRequest headers:".PHP_EOL;
            # foreach ($request->getHeaders() as $name => $values) {
            #     echo $name . ': ' . implode(', ', $values) . "\n";
            # }
        
            return $request;
        }));

        $this->client = new Client([
            'handler' => $stack
        ]);
    }

    protected function sign_string($data, $key_path, $passphrase){
        $pkeyid = openssl_pkey_get_private($key_path, $passphrase);
        if (!$pkeyid) {
            exit('Error reading private key');
        }
    
        openssl_sign($data, $signature, $pkeyid, OPENSSL_ALGO_SHA256);
    
        return base64_encode($signature);
    }

    public function getNamespace()
    {
        $response = $this->client->get("https://objectstorage.{$this->region}.oraclecloud.com/n", [ 'headers' => ['Content-Type' => 'application/json']]);

        $body = $response->getBody();
        if (str_starts_with($body, "\"") && str_ends_with($body, "\"")) 
        {
            $body = substr($body, 1, -1);
        }

        return new OciResponse(
            statusCode: $response->getStatusCode(),
            headers: $response->getHeaders(),
            body: $body);
    }

    // headObject
    // deleteObject
    // listObjects
    // copyObject

    public function putObject(
        string $namespace,
        string $bucket_name,
        string $object_name,
        $body
    )
    {
        $response = $this->client->put("https://objectstorage.{$this->region}.oraclecloud.com/n/$namespace/b/$bucket_name/o/$object_name", [ "body" => $body, 'headers' => ['Content-Type' => 'application/json']]);
        
        return new OciResponse(
            statusCode: $response->getStatusCode(),
            headers: $response->getHeaders(),
            body: $response->getBody());
    }

    public function getObject(
        string $namespace,
        string $bucket_name,
        string $object_name
    )
    {
        $response = $this->client->get("https://objectstorage.{$this->region}.oraclecloud.com/n/$namespace/b/$bucket_name/o/$object_name", [ 'headers' => ['Content-Type' => 'application/json']]);
        
        return new OciResponse(
            statusCode: $response->getStatusCode(),
            headers: $response->getHeaders(),
            body: $response->getBody());
    }

    public function headObject(
        string $namespace,
        string $bucket_name,
        string $object_name
    )
    {
        $response = $this->client->head("https://objectstorage.{$this->region}.oraclecloud.com/n/$namespace/b/$bucket_name/o/$object_name", [ 'headers' => ['Content-Type' => 'application/json']]);
        
        return new OciResponse(
            statusCode: $response->getStatusCode(),
            headers: $response->getHeaders(),
            body: $response->getBody());
    }

    public function listObjects(
        string $namespace,
        string $bucket_name
    )
    {
        $response = $this->client->get("https://objectstorage.{$this->region}.oraclecloud.com/n/$namespace/b/$bucket_name/o", [ 'headers' => ['Content-Type' => 'application/json']]);
        
        return new OciResponse(
            statusCode: $response->getStatusCode(),
            headers: $response->getHeaders(),
            json: json_decode($response->getBody()));
    }
}
?>