<?php

// Generated using OracleSDKGenerator, API Version: 20160918

namespace Oracle\Oci\ObjectStorage;

use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use Oracle\Oci\Common\OciResponse;
use Oracle\Oci\Common\AuthProviderInterface;
use Oracle\Oci\Common\Realm;
use Oracle\Oci\Common\Region;
use Oracle\Oci\Common\RegionProvider;

class ObjectStorageClient
{
    protected AuthProviderInterface $auth_provider;
    protected ?Region $region;

    protected const endpointTemplate = "https://objectstorage.{region}.{secondLevelDomain}";
    protected string $endpoint;
    protected $client;

    public function __construct(
        AuthProviderInterface $auth_provider,
        $region=null,
        $endpoint=null)
    {
        $this->auth_provider = $auth_provider;

        if ($auth_provider instanceof RegionProvider)
        {
            $this->region = $auth_provider->getRegion();
        }
        if ($region != null)
        {
            if ($region instanceof Region)
            {
                $this->region = $region;
            }
            else {
                $knownRegion = Region::getRegion($region);
                if ($knownRegion == null)
                {
                    // forward-compatibility for unknown regions
                    $realm = Realm::getRealmForUnknownRegion();
                    $endpoint = str_replace('{region}', $region, ObjectStorageClient::endpointTemplate);
                    $endpoint = str_replace('{secondLevelDomain}', $realm->getRealmDomainComponent(), $endpoint);
                    $this->region = null;
                    // echo "Region $region is unknown, assuming it to be in realm $realm. Setting endpoint to $endpoint".PHP_EOL;
                }
                else
                {
                    $this->region = $knownRegion;
                }
            }
        }
        if ($this->region == null && $endpoint == null)
        {
            throw new InvalidArgumentException('Neither region nor endpoint is set.');
        }

        if ($endpoint != null) {
            $this->endpoint = $endpoint;
        }
        else {
            $this->endpoint = str_replace('{region}', $this->region->getRegionId(), ObjectStorageClient::endpointTemplate);
            $this->endpoint = str_replace('{secondLevelDomain}', $this->region->getRealm()->getRealmDomainComponent(), $this->endpoint);
        }
        // echo "Final endpoint: {$this->endpoint}".PHP_EOL;

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

            $signature = $this->sign_string($signing_string, $this->auth_provider->getKeyFilename(), $this->auth_provider->getKeyPassphrase());

            $authorization_header = "Signature version=\"1\",keyId=\"{$this->auth_provider->getKeyId()}\",algorithm=\"rsa-sha256\",headers=\"$headers\",signature=\"$signature\"";
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


    // Should have waiters.

    // Should have paginators.

    // Operation 'abortMultipartUpload':
    // path /n/{namespaceName}/b/{bucketName}/u/{objectName}
    public function abortMultipartUpload(
        $namespaceName,
        $bucketName,
        $objectName,
        $uploadId,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($uploadId != null)
        {
            $__query['uploadId'] = $uploadId;
        }

        $__path = "/n/{namespaceName}/b/{bucketName}/u/{objectName}";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);
        $__path = str_replace('{objectName}', $objectName, $__path);

        $__response = $this->client->delete(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'cancelWorkRequest':
    // path /workRequests/{workRequestId}
    public function cancelWorkRequest(
        $workRequestId,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];

        $__path = "/workRequests/{workRequestId}";
        $__path = str_replace('{workRequestId}', $workRequestId, $__path);

        $__response = $this->client->delete(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'commitMultipartUpload':
    // path /n/{namespaceName}/b/{bucketName}/u/{objectName}
    public function commitMultipartUpload(
        $namespaceName,
        $bucketName,
        $objectName,
        $uploadId,
        $commitMultipartUploadDetails,
        $ifMatch = null,
        $ifNoneMatch = null,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($ifMatch != null)
        {
            $__headers['ifMatch'] = $ifMatch;
        }
        if ($ifNoneMatch != null)
        {
            $__headers['ifNoneMatch'] = $ifNoneMatch;
        }
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($uploadId != null)
        {
            $__query['uploadId'] = $uploadId;
        }

        $__path = "/n/{namespaceName}/b/{bucketName}/u/{objectName}";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);
        $__path = str_replace('{objectName}', $objectName, $__path);

        $__body = json_encode($commitMultipartUploadDetails);

        $__response = $this->client->post(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query, 'body' => $__body ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'copyObject':
    // path /n/{namespaceName}/b/{bucketName}/actions/copyObject
    public function copyObject(
        $namespaceName,
        $bucketName,
        $copyObjectDetails,
        $opcClientRequestId = null,
        $opcSseCustomerAlgorithm = null,
        $opcSseCustomerKey = null,
        $opcSseCustomerKeySha256 = null,
        $opcSourceSseCustomerAlgorithm = null,
        $opcSourceSseCustomerKey = null,
        $opcSourceSseCustomerKeySha256 = null,
        $opcSseKmsKeyId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }
        if ($opcSseCustomerAlgorithm != null)
        {
            $__headers['opcSseCustomerAlgorithm'] = $opcSseCustomerAlgorithm;
        }
        if ($opcSseCustomerKey != null)
        {
            $__headers['opcSseCustomerKey'] = $opcSseCustomerKey;
        }
        if ($opcSseCustomerKeySha256 != null)
        {
            $__headers['opcSseCustomerKeySha256'] = $opcSseCustomerKeySha256;
        }
        if ($opcSourceSseCustomerAlgorithm != null)
        {
            $__headers['opcSourceSseCustomerAlgorithm'] = $opcSourceSseCustomerAlgorithm;
        }
        if ($opcSourceSseCustomerKey != null)
        {
            $__headers['opcSourceSseCustomerKey'] = $opcSourceSseCustomerKey;
        }
        if ($opcSourceSseCustomerKeySha256 != null)
        {
            $__headers['opcSourceSseCustomerKeySha256'] = $opcSourceSseCustomerKeySha256;
        }
        if ($opcSseKmsKeyId != null)
        {
            $__headers['opcSseKmsKeyId'] = $opcSseKmsKeyId;
        }

        $__query = [];

        $__path = "/n/{namespaceName}/b/{bucketName}/actions/copyObject";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__body = json_encode($copyObjectDetails);

        $__response = $this->client->post(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query, 'body' => $__body ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'createBucket':
    // path /n/{namespaceName}/b/
    public function createBucket(
        $namespaceName,
        $createBucketDetails,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];

        $__path = "/n/{namespaceName}/b/";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);

        $__body = json_encode($createBucketDetails);

        $__response = $this->client->post(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query, 'body' => $__body ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'createMultipartUpload':
    // path /n/{namespaceName}/b/{bucketName}/u
    public function createMultipartUpload(
        $namespaceName,
        $bucketName,
        $createMultipartUploadDetails,
        $ifMatch = null,
        $ifNoneMatch = null,
        $opcClientRequestId = null,
        $opcSseCustomerAlgorithm = null,
        $opcSseCustomerKey = null,
        $opcSseCustomerKeySha256 = null,
        $opcSseKmsKeyId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($ifMatch != null)
        {
            $__headers['ifMatch'] = $ifMatch;
        }
        if ($ifNoneMatch != null)
        {
            $__headers['ifNoneMatch'] = $ifNoneMatch;
        }
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }
        if ($opcSseCustomerAlgorithm != null)
        {
            $__headers['opcSseCustomerAlgorithm'] = $opcSseCustomerAlgorithm;
        }
        if ($opcSseCustomerKey != null)
        {
            $__headers['opcSseCustomerKey'] = $opcSseCustomerKey;
        }
        if ($opcSseCustomerKeySha256 != null)
        {
            $__headers['opcSseCustomerKeySha256'] = $opcSseCustomerKeySha256;
        }
        if ($opcSseKmsKeyId != null)
        {
            $__headers['opcSseKmsKeyId'] = $opcSseKmsKeyId;
        }

        $__query = [];

        $__path = "/n/{namespaceName}/b/{bucketName}/u";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__body = json_encode($createMultipartUploadDetails);

        $__response = $this->client->post(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query, 'body' => $__body ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'createPreauthenticatedRequest':
    // path /n/{namespaceName}/b/{bucketName}/p/
    public function createPreauthenticatedRequest(
        $namespaceName,
        $bucketName,
        $createPreauthenticatedRequestDetails,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];

        $__path = "/n/{namespaceName}/b/{bucketName}/p/";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__body = json_encode($createPreauthenticatedRequestDetails);

        $__response = $this->client->post(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query, 'body' => $__body ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'createReplicationPolicy':
    // path /n/{namespaceName}/b/{bucketName}/replicationPolicies
    public function createReplicationPolicy(
        $namespaceName,
        $bucketName,
        $createReplicationPolicyDetails,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];

        $__path = "/n/{namespaceName}/b/{bucketName}/replicationPolicies";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__body = json_encode($createReplicationPolicyDetails);

        $__response = $this->client->post(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query, 'body' => $__body ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'createRetentionRule':
    // path /n/{namespaceName}/b/{bucketName}/retentionRules
    public function createRetentionRule(
        $namespaceName,
        $bucketName,
        $createRetentionRuleDetails,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];

        $__path = "/n/{namespaceName}/b/{bucketName}/retentionRules";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__body = json_encode($createRetentionRuleDetails);

        $__response = $this->client->post(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query, 'body' => $__body ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'deleteBucket':
    // path /n/{namespaceName}/b/{bucketName}/
    public function deleteBucket(
        $namespaceName,
        $bucketName,
        $ifMatch = null,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($ifMatch != null)
        {
            $__headers['ifMatch'] = $ifMatch;
        }
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];

        $__path = "/n/{namespaceName}/b/{bucketName}/";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__response = $this->client->delete(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'deleteObject':
    // path /n/{namespaceName}/b/{bucketName}/o/{objectName}
    public function deleteObject(
        $namespaceName,
        $bucketName,
        $objectName,
        $ifMatch = null,
        $opcClientRequestId = null,
        $versionId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($ifMatch != null)
        {
            $__headers['ifMatch'] = $ifMatch;
        }
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($versionId != null)
        {
            $__query['versionId'] = $versionId;
        }

        $__path = "/n/{namespaceName}/b/{bucketName}/o/{objectName}";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);
        $__path = str_replace('{objectName}', $objectName, $__path);

        $__response = $this->client->delete(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'deleteObjectLifecyclePolicy':
    // path /n/{namespaceName}/b/{bucketName}/l
    public function deleteObjectLifecyclePolicy(
        $namespaceName,
        $bucketName,
        $opcClientRequestId = null,
        $ifMatch = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }
        if ($ifMatch != null)
        {
            $__headers['ifMatch'] = $ifMatch;
        }

        $__query = [];

        $__path = "/n/{namespaceName}/b/{bucketName}/l";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__response = $this->client->delete(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'deletePreauthenticatedRequest':
    // path /n/{namespaceName}/b/{bucketName}/p/{parId}
    public function deletePreauthenticatedRequest(
        $namespaceName,
        $bucketName,
        $parId,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];

        $__path = "/n/{namespaceName}/b/{bucketName}/p/{parId}";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);
        $__path = str_replace('{parId}', $parId, $__path);

        $__response = $this->client->delete(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'deleteReplicationPolicy':
    // path /n/{namespaceName}/b/{bucketName}/replicationPolicies/{replicationId}
    public function deleteReplicationPolicy(
        $namespaceName,
        $bucketName,
        $replicationId,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];

        $__path = "/n/{namespaceName}/b/{bucketName}/replicationPolicies/{replicationId}";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);
        $__path = str_replace('{replicationId}', $replicationId, $__path);

        $__response = $this->client->delete(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'deleteRetentionRule':
    // path /n/{namespaceName}/b/{bucketName}/retentionRules/{retentionRuleId}
    public function deleteRetentionRule(
        $namespaceName,
        $bucketName,
        $retentionRuleId,
        $ifMatch = null,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($ifMatch != null)
        {
            $__headers['ifMatch'] = $ifMatch;
        }
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];

        $__path = "/n/{namespaceName}/b/{bucketName}/retentionRules/{retentionRuleId}";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);
        $__path = str_replace('{retentionRuleId}', $retentionRuleId, $__path);

        $__response = $this->client->delete(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'getBucket':
    // path /n/{namespaceName}/b/{bucketName}/
    public function getBucket(
        $namespaceName,
        $bucketName,
        $ifMatch = null,
        $ifNoneMatch = null,
        $opcClientRequestId = null,
        $fields = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($ifMatch != null)
        {
            $__headers['ifMatch'] = $ifMatch;
        }
        if ($ifNoneMatch != null)
        {
            $__headers['ifNoneMatch'] = $ifNoneMatch;
        }
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($fields != null)
        {
            $__query['fields'] = $fields;
        }

        $__path = "/n/{namespaceName}/b/{bucketName}/";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'getNamespace':
    // path /n/
    public function getNamespace(
        $opcClientRequestId = null,
        $compartmentId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($compartmentId != null)
        {
            $__query['compartmentId'] = $compartmentId;
        }

        $__path = "/n/";

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'getNamespaceMetadata':
    // path /n/{namespaceName}
    public function getNamespaceMetadata(
        $namespaceName,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];

        $__path = "/n/{namespaceName}";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'getObject':
    // path /n/{namespaceName}/b/{bucketName}/o/{objectName}
    public function getObject(
        $namespaceName,
        $bucketName,
        $objectName,
        $versionId = null,
        $ifMatch = null,
        $ifNoneMatch = null,
        $opcClientRequestId = null,
        $range = null,
        $opcSseCustomerAlgorithm = null,
        $opcSseCustomerKey = null,
        $opcSseCustomerKeySha256 = null,
        $httpResponseContentDisposition = null,
        $httpResponseCacheControl = null,
        $httpResponseContentType = null,
        $httpResponseContentLanguage = null,
        $httpResponseContentEncoding = null,
        $httpResponseExpires = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($ifMatch != null)
        {
            $__headers['ifMatch'] = $ifMatch;
        }
        if ($ifNoneMatch != null)
        {
            $__headers['ifNoneMatch'] = $ifNoneMatch;
        }
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }
        if ($range != null)
        {
            $__headers['range'] = $range;
        }
        if ($opcSseCustomerAlgorithm != null)
        {
            $__headers['opcSseCustomerAlgorithm'] = $opcSseCustomerAlgorithm;
        }
        if ($opcSseCustomerKey != null)
        {
            $__headers['opcSseCustomerKey'] = $opcSseCustomerKey;
        }
        if ($opcSseCustomerKeySha256 != null)
        {
            $__headers['opcSseCustomerKeySha256'] = $opcSseCustomerKeySha256;
        }

        $__query = [];
        if ($versionId != null)
        {
            $__query['versionId'] = $versionId;
        }
        if ($httpResponseContentDisposition != null)
        {
            $__query['httpResponseContentDisposition'] = $httpResponseContentDisposition;
        }
        if ($httpResponseCacheControl != null)
        {
            $__query['httpResponseCacheControl'] = $httpResponseCacheControl;
        }
        if ($httpResponseContentType != null)
        {
            $__query['httpResponseContentType'] = $httpResponseContentType;
        }
        if ($httpResponseContentLanguage != null)
        {
            $__query['httpResponseContentLanguage'] = $httpResponseContentLanguage;
        }
        if ($httpResponseContentEncoding != null)
        {
            $__query['httpResponseContentEncoding'] = $httpResponseContentEncoding;
        }
        if ($httpResponseExpires != null)
        {
            $__query['httpResponseExpires'] = $httpResponseExpires;
        }

        $__path = "/n/{namespaceName}/b/{bucketName}/o/{objectName}";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);
        $__path = str_replace('{objectName}', $objectName, $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            body: $__response->getBody());
    }

    // Operation 'getObjectLifecyclePolicy':
    // path /n/{namespaceName}/b/{bucketName}/l
    public function getObjectLifecyclePolicy(
        $namespaceName,
        $bucketName,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];

        $__path = "/n/{namespaceName}/b/{bucketName}/l";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'getPreauthenticatedRequest':
    // path /n/{namespaceName}/b/{bucketName}/p/{parId}
    public function getPreauthenticatedRequest(
        $namespaceName,
        $bucketName,
        $parId,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];

        $__path = "/n/{namespaceName}/b/{bucketName}/p/{parId}";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);
        $__path = str_replace('{parId}', $parId, $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'getReplicationPolicy':
    // path /n/{namespaceName}/b/{bucketName}/replicationPolicies/{replicationId}
    public function getReplicationPolicy(
        $namespaceName,
        $bucketName,
        $replicationId,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];

        $__path = "/n/{namespaceName}/b/{bucketName}/replicationPolicies/{replicationId}";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);
        $__path = str_replace('{replicationId}', $replicationId, $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'getRetentionRule':
    // path /n/{namespaceName}/b/{bucketName}/retentionRules/{retentionRuleId}
    public function getRetentionRule(
        $namespaceName,
        $bucketName,
        $retentionRuleId,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];

        $__path = "/n/{namespaceName}/b/{bucketName}/retentionRules/{retentionRuleId}";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);
        $__path = str_replace('{retentionRuleId}', $retentionRuleId, $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'getWorkRequest':
    // path /workRequests/{workRequestId}
    public function getWorkRequest(
        $workRequestId,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];

        $__path = "/workRequests/{workRequestId}";
        $__path = str_replace('{workRequestId}', $workRequestId, $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'headBucket':
    // path /n/{namespaceName}/b/{bucketName}/
    public function headBucket(
        $namespaceName,
        $bucketName,
        $ifMatch = null,
        $ifNoneMatch = null,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($ifMatch != null)
        {
            $__headers['ifMatch'] = $ifMatch;
        }
        if ($ifNoneMatch != null)
        {
            $__headers['ifNoneMatch'] = $ifNoneMatch;
        }
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];

        $__path = "/n/{namespaceName}/b/{bucketName}/";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__response = $this->client->head(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'headObject':
    // path /n/{namespaceName}/b/{bucketName}/o/{objectName}
    public function headObject(
        $namespaceName,
        $bucketName,
        $objectName,
        $versionId = null,
        $ifMatch = null,
        $ifNoneMatch = null,
        $opcClientRequestId = null,
        $opcSseCustomerAlgorithm = null,
        $opcSseCustomerKey = null,
        $opcSseCustomerKeySha256 = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($ifMatch != null)
        {
            $__headers['ifMatch'] = $ifMatch;
        }
        if ($ifNoneMatch != null)
        {
            $__headers['ifNoneMatch'] = $ifNoneMatch;
        }
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }
        if ($opcSseCustomerAlgorithm != null)
        {
            $__headers['opcSseCustomerAlgorithm'] = $opcSseCustomerAlgorithm;
        }
        if ($opcSseCustomerKey != null)
        {
            $__headers['opcSseCustomerKey'] = $opcSseCustomerKey;
        }
        if ($opcSseCustomerKeySha256 != null)
        {
            $__headers['opcSseCustomerKeySha256'] = $opcSseCustomerKeySha256;
        }

        $__query = [];
        if ($versionId != null)
        {
            $__query['versionId'] = $versionId;
        }

        $__path = "/n/{namespaceName}/b/{bucketName}/o/{objectName}";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);
        $__path = str_replace('{objectName}', $objectName, $__path);

        $__response = $this->client->head(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'listBuckets':
    // path /n/{namespaceName}/b/
    public function listBuckets(
        $namespaceName,
        $compartmentId,
        $limit = null,
        $page = null,
        $fields = null,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($compartmentId != null)
        {
            $__query['compartmentId'] = $compartmentId;
        }
        if ($limit != null)
        {
            $__query['limit'] = $limit;
        }
        if ($page != null)
        {
            $__query['page'] = $page;
        }
        if ($fields != null)
        {
            $__query['fields'] = $fields;
        }

        $__path = "/n/{namespaceName}/b/";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'listMultipartUploadParts':
    // path /n/{namespaceName}/b/{bucketName}/u/{objectName}
    public function listMultipartUploadParts(
        $namespaceName,
        $bucketName,
        $objectName,
        $uploadId,
        $limit = null,
        $page = null,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($uploadId != null)
        {
            $__query['uploadId'] = $uploadId;
        }
        if ($limit != null)
        {
            $__query['limit'] = $limit;
        }
        if ($page != null)
        {
            $__query['page'] = $page;
        }

        $__path = "/n/{namespaceName}/b/{bucketName}/u/{objectName}";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);
        $__path = str_replace('{objectName}', $objectName, $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'listMultipartUploads':
    // path /n/{namespaceName}/b/{bucketName}/u
    public function listMultipartUploads(
        $namespaceName,
        $bucketName,
        $limit = null,
        $page = null,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($limit != null)
        {
            $__query['limit'] = $limit;
        }
        if ($page != null)
        {
            $__query['page'] = $page;
        }

        $__path = "/n/{namespaceName}/b/{bucketName}/u";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'listObjectVersions':
    // path /n/{namespaceName}/b/{bucketName}/objectversions
    public function listObjectVersions(
        $namespaceName,
        $bucketName,
        $prefix = null,
        $start = null,
        $end = null,
        $limit = null,
        $delimiter = null,
        $fields = null,
        $opcClientRequestId = null,
        $startAfter = null,
        $page = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($prefix != null)
        {
            $__query['prefix'] = $prefix;
        }
        if ($start != null)
        {
            $__query['start'] = $start;
        }
        if ($end != null)
        {
            $__query['end'] = $end;
        }
        if ($limit != null)
        {
            $__query['limit'] = $limit;
        }
        if ($delimiter != null)
        {
            $__query['delimiter'] = $delimiter;
        }
        if ($fields != null)
        {
            $__query['fields'] = $fields;
        }
        if ($startAfter != null)
        {
            $__query['startAfter'] = $startAfter;
        }
        if ($page != null)
        {
            $__query['page'] = $page;
        }

        $__path = "/n/{namespaceName}/b/{bucketName}/objectversions";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'listObjects':
    // path /n/{namespaceName}/b/{bucketName}/o
    public function listObjects(
        $namespaceName,
        $bucketName,
        $prefix = null,
        $start = null,
        $end = null,
        $limit = null,
        $delimiter = null,
        $fields = null,
        $opcClientRequestId = null,
        $startAfter = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($prefix != null)
        {
            $__query['prefix'] = $prefix;
        }
        if ($start != null)
        {
            $__query['start'] = $start;
        }
        if ($end != null)
        {
            $__query['end'] = $end;
        }
        if ($limit != null)
        {
            $__query['limit'] = $limit;
        }
        if ($delimiter != null)
        {
            $__query['delimiter'] = $delimiter;
        }
        if ($fields != null)
        {
            $__query['fields'] = $fields;
        }
        if ($startAfter != null)
        {
            $__query['startAfter'] = $startAfter;
        }

        $__path = "/n/{namespaceName}/b/{bucketName}/o";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'listPreauthenticatedRequests':
    // path /n/{namespaceName}/b/{bucketName}/p/
    public function listPreauthenticatedRequests(
        $namespaceName,
        $bucketName,
        $objectNamePrefix = null,
        $limit = null,
        $page = null,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($objectNamePrefix != null)
        {
            $__query['objectNamePrefix'] = $objectNamePrefix;
        }
        if ($limit != null)
        {
            $__query['limit'] = $limit;
        }
        if ($page != null)
        {
            $__query['page'] = $page;
        }

        $__path = "/n/{namespaceName}/b/{bucketName}/p/";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'listReplicationPolicies':
    // path /n/{namespaceName}/b/{bucketName}/replicationPolicies
    public function listReplicationPolicies(
        $namespaceName,
        $bucketName,
        $opcClientRequestId = null,
        $page = null,
        $limit = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($page != null)
        {
            $__query['page'] = $page;
        }
        if ($limit != null)
        {
            $__query['limit'] = $limit;
        }

        $__path = "/n/{namespaceName}/b/{bucketName}/replicationPolicies";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'listReplicationSources':
    // path /n/{namespaceName}/b/{bucketName}/replicationSources
    public function listReplicationSources(
        $namespaceName,
        $bucketName,
        $opcClientRequestId = null,
        $page = null,
        $limit = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($page != null)
        {
            $__query['page'] = $page;
        }
        if ($limit != null)
        {
            $__query['limit'] = $limit;
        }

        $__path = "/n/{namespaceName}/b/{bucketName}/replicationSources";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'listRetentionRules':
    // path /n/{namespaceName}/b/{bucketName}/retentionRules
    public function listRetentionRules(
        $namespaceName,
        $bucketName,
        $page = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];

        $__query = [];
        if ($page != null)
        {
            $__query['page'] = $page;
        }

        $__path = "/n/{namespaceName}/b/{bucketName}/retentionRules";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'listWorkRequestErrors':
    // path /workRequests/{workRequestId}/errors
    public function listWorkRequestErrors(
        $workRequestId,
        $page = null,
        $limit = null,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($page != null)
        {
            $__query['page'] = $page;
        }
        if ($limit != null)
        {
            $__query['limit'] = $limit;
        }

        $__path = "/workRequests/{workRequestId}/errors";
        $__path = str_replace('{workRequestId}', $workRequestId, $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'listWorkRequestLogs':
    // path /workRequests/{workRequestId}/logs
    public function listWorkRequestLogs(
        $workRequestId,
        $page = null,
        $limit = null,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($page != null)
        {
            $__query['page'] = $page;
        }
        if ($limit != null)
        {
            $__query['limit'] = $limit;
        }

        $__path = "/workRequests/{workRequestId}/logs";
        $__path = str_replace('{workRequestId}', $workRequestId, $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'listWorkRequests':
    // path /workRequests
    public function listWorkRequests(
        $compartmentId,
        $opcClientRequestId = null,
        $page = null,
        $limit = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($compartmentId != null)
        {
            $__query['compartmentId'] = $compartmentId;
        }
        if ($page != null)
        {
            $__query['page'] = $page;
        }
        if ($limit != null)
        {
            $__query['limit'] = $limit;
        }

        $__path = "/workRequests";

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'makeBucketWritable':
    // path /n/{namespaceName}/b/{bucketName}/actions/makeBucketWritable
    public function makeBucketWritable(
        $namespaceName,
        $bucketName,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];

        $__path = "/n/{namespaceName}/b/{bucketName}/actions/makeBucketWritable";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__response = $this->client->post(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'putObject':
    // path /n/{namespaceName}/b/{bucketName}/o/{objectName}
    public function putObject(
        $namespaceName,
        $bucketName,
        $objectName,
        $putObjectBody,
        $contentLength = null,
        $ifMatch = null,
        $ifNoneMatch = null,
        $opcClientRequestId = null,
        $expect = null,
        $contentMD5 = null,
        $contentType = null,
        $contentLanguage = null,
        $contentEncoding = null,
        $contentDisposition = null,
        $cacheControl = null,
        $opcSseCustomerAlgorithm = null,
        $opcSseCustomerKey = null,
        $opcSseCustomerKeySha256 = null,
        $opcSseKmsKeyId = null,
        $storageTier = null,
        $opcMeta = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($ifMatch != null)
        {
            $__headers['ifMatch'] = $ifMatch;
        }
        if ($ifNoneMatch != null)
        {
            $__headers['ifNoneMatch'] = $ifNoneMatch;
        }
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }
        if ($expect != null)
        {
            $__headers['expect'] = $expect;
        }
        if ($contentLength != null)
        {
            $__headers['contentLength'] = $contentLength;
        }
        if ($contentMD5 != null)
        {
            $__headers['contentMD5'] = $contentMD5;
        }
        if ($contentType != null)
        {
            $__headers['contentType'] = $contentType;
        }
        if ($contentLanguage != null)
        {
            $__headers['contentLanguage'] = $contentLanguage;
        }
        if ($contentEncoding != null)
        {
            $__headers['contentEncoding'] = $contentEncoding;
        }
        if ($contentDisposition != null)
        {
            $__headers['contentDisposition'] = $contentDisposition;
        }
        if ($cacheControl != null)
        {
            $__headers['cacheControl'] = $cacheControl;
        }
        if ($opcSseCustomerAlgorithm != null)
        {
            $__headers['opcSseCustomerAlgorithm'] = $opcSseCustomerAlgorithm;
        }
        if ($opcSseCustomerKey != null)
        {
            $__headers['opcSseCustomerKey'] = $opcSseCustomerKey;
        }
        if ($opcSseCustomerKeySha256 != null)
        {
            $__headers['opcSseCustomerKeySha256'] = $opcSseCustomerKeySha256;
        }
        if ($opcSseKmsKeyId != null)
        {
            $__headers['opcSseKmsKeyId'] = $opcSseKmsKeyId;
        }
        if ($storageTier != null)
        {
            $__headers['storageTier'] = $storageTier;
        }
        if ($opcMeta != null)
        {
            $__headers['opcMeta'] = $opcMeta;
        }

        $__query = [];

        $__path = "/n/{namespaceName}/b/{bucketName}/o/{objectName}";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);
        $__path = str_replace('{objectName}', $objectName, $__path);

        $__body = $putObjectBody;

        $__response = $this->client->put(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query, 'body' => $__body ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'putObjectLifecyclePolicy':
    // path /n/{namespaceName}/b/{bucketName}/l
    public function putObjectLifecyclePolicy(
        $namespaceName,
        $bucketName,
        $putObjectLifecyclePolicyDetails,
        $opcClientRequestId = null,
        $ifMatch = null,
        $ifNoneMatch = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }
        if ($ifMatch != null)
        {
            $__headers['ifMatch'] = $ifMatch;
        }
        if ($ifNoneMatch != null)
        {
            $__headers['ifNoneMatch'] = $ifNoneMatch;
        }

        $__query = [];

        $__path = "/n/{namespaceName}/b/{bucketName}/l";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__body = json_encode($putObjectLifecyclePolicyDetails);

        $__response = $this->client->put(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query, 'body' => $__body ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'reencryptBucket':
    // path /n/{namespaceName}/b/{bucketName}/actions/reencrypt
    public function reencryptBucket(
        $namespaceName,
        $bucketName,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];

        $__path = "/n/{namespaceName}/b/{bucketName}/actions/reencrypt";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__response = $this->client->post(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'reencryptObject':
    // path /n/{namespaceName}/b/{bucketName}/actions/reencrypt/{objectName}
    public function reencryptObject(
        $namespaceName,
        $bucketName,
        $objectName,
        $reencryptObjectDetails,
        $versionId = null,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($versionId != null)
        {
            $__query['versionId'] = $versionId;
        }

        $__path = "/n/{namespaceName}/b/{bucketName}/actions/reencrypt/{objectName}";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);
        $__path = str_replace('{objectName}', $objectName, $__path);

        $__body = json_encode($reencryptObjectDetails);

        $__response = $this->client->post(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query, 'body' => $__body ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'renameObject':
    // path /n/{namespaceName}/b/{bucketName}/actions/renameObject
    public function renameObject(
        $namespaceName,
        $bucketName,
        $renameObjectDetails,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];

        $__path = "/n/{namespaceName}/b/{bucketName}/actions/renameObject";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__body = json_encode($renameObjectDetails);

        $__response = $this->client->post(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query, 'body' => $__body ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'restoreObjects':
    // path /n/{namespaceName}/b/{bucketName}/actions/restoreObjects
    public function restoreObjects(
        $namespaceName,
        $bucketName,
        $restoreObjectsDetails,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];

        $__path = "/n/{namespaceName}/b/{bucketName}/actions/restoreObjects";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__body = json_encode($restoreObjectsDetails);

        $__response = $this->client->post(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query, 'body' => $__body ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'updateBucket':
    // path /n/{namespaceName}/b/{bucketName}/
    public function updateBucket(
        $namespaceName,
        $bucketName,
        $updateBucketDetails,
        $ifMatch = null,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($ifMatch != null)
        {
            $__headers['ifMatch'] = $ifMatch;
        }
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];

        $__path = "/n/{namespaceName}/b/{bucketName}/";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__body = json_encode($updateBucketDetails);

        $__response = $this->client->post(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query, 'body' => $__body ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'updateNamespaceMetadata':
    // path /n/{namespaceName}
    public function updateNamespaceMetadata(
        $namespaceName,
        $updateNamespaceMetadataDetails,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];

        $__path = "/n/{namespaceName}";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);

        $__body = json_encode($updateNamespaceMetadataDetails);

        $__response = $this->client->put(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query, 'body' => $__body ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'updateObjectStorageTier':
    // path /n/{namespaceName}/b/{bucketName}/actions/updateObjectStorageTier
    public function updateObjectStorageTier(
        $namespaceName,
        $bucketName,
        $updateObjectStorageTierDetails,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];

        $__path = "/n/{namespaceName}/b/{bucketName}/actions/updateObjectStorageTier";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__body = json_encode($updateObjectStorageTierDetails);

        $__response = $this->client->post(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query, 'body' => $__body ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'updateRetentionRule':
    // path /n/{namespaceName}/b/{bucketName}/retentionRules/{retentionRuleId}
    public function updateRetentionRule(
        $namespaceName,
        $bucketName,
        $retentionRuleId,
        $updateRetentionRuleDetails,
        $ifMatch = null,
        $opcClientRequestId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($ifMatch != null)
        {
            $__headers['ifMatch'] = $ifMatch;
        }
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];

        $__path = "/n/{namespaceName}/b/{bucketName}/retentionRules/{retentionRuleId}";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);
        $__path = str_replace('{retentionRuleId}', $retentionRuleId, $__path);

        $__body = json_encode($updateRetentionRuleDetails);

        $__response = $this->client->put(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query, 'body' => $__body ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

    // Operation 'uploadPart':
    // path /n/{namespaceName}/b/{bucketName}/u/{objectName}
    public function uploadPart(
        $namespaceName,
        $bucketName,
        $objectName,
        $uploadId,
        $uploadPartNum,
        $uploadPartBody,
        $contentLength = null,
        $opcClientRequestId = null,
        $ifMatch = null,
        $ifNoneMatch = null,
        $expect = null,
        $contentMD5 = null,
        $opcSseCustomerAlgorithm = null,
        $opcSseCustomerKey = null,
        $opcSseCustomerKeySha256 = null,
        $opcSseKmsKeyId = null,
    )
    {
        $__headers = ['Content-Type' => 'application/json'];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }
        if ($ifMatch != null)
        {
            $__headers['ifMatch'] = $ifMatch;
        }
        if ($ifNoneMatch != null)
        {
            $__headers['ifNoneMatch'] = $ifNoneMatch;
        }
        if ($expect != null)
        {
            $__headers['expect'] = $expect;
        }
        if ($contentLength != null)
        {
            $__headers['contentLength'] = $contentLength;
        }
        if ($contentMD5 != null)
        {
            $__headers['contentMD5'] = $contentMD5;
        }
        if ($opcSseCustomerAlgorithm != null)
        {
            $__headers['opcSseCustomerAlgorithm'] = $opcSseCustomerAlgorithm;
        }
        if ($opcSseCustomerKey != null)
        {
            $__headers['opcSseCustomerKey'] = $opcSseCustomerKey;
        }
        if ($opcSseCustomerKeySha256 != null)
        {
            $__headers['opcSseCustomerKeySha256'] = $opcSseCustomerKeySha256;
        }
        if ($opcSseKmsKeyId != null)
        {
            $__headers['opcSseKmsKeyId'] = $opcSseKmsKeyId;
        }

        $__query = [];
        if ($uploadId != null)
        {
            $__query['uploadId'] = $uploadId;
        }
        if ($uploadPartNum != null)
        {
            $__query['uploadPartNum'] = $uploadPartNum;
        }

        $__path = "/n/{namespaceName}/b/{bucketName}/u/{objectName}";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);
        $__path = str_replace('{objectName}', $objectName, $__path);

        $__body = $uploadPartBody;

        $__response = $this->client->put(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query, 'body' => $__body ]
        );
        return new OciResponse(
            statusCode: $__response->getStatusCode(),
            headers: $__response->getHeaders(),
            json: json_decode($__response->getBody()));
    }

}
?>