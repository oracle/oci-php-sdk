<?php

// Generated using OracleSDKGenerator, API Version: 20160918

namespace Oracle\Oci\ObjectStorage;

use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use Oracle\Oci\Common\AuthProviderInterface;
use Oracle\Oci\Common\HttpUtils;
use Oracle\Oci\Common\OciResponse;
use Oracle\Oci\Common\Realm;
use Oracle\Oci\Common\Region;
use Oracle\Oci\Common\RegionProvider;
use Oracle\Oci\Common\UserAgent;

class ObjectStorageClient
{
    private /*AuthProviderInterface*/ $auth_provider;
    protected /*?Region*/ $region;

    static protected /*const*/ $endpointTemplate = "https://objectstorage.{region}.{secondLevelDomain}";
    protected /*string*/ $endpoint;
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
                    $endpoint = str_replace('{region}', $region, ObjectStorageClient::$endpointTemplate);
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
            $this->endpoint = str_replace('{region}', $this->region->getRegionId(), ObjectStorageClient::$endpointTemplate);
            $this->endpoint = str_replace('{secondLevelDomain}', $this->region->getRealm()->getRealmDomainComponent(), $this->endpoint);
        }
        // echo "Final endpoint: {$this->endpoint}".PHP_EOL;

        $handler = new CurlHandler();
        $stack = HandlerStack::create($handler);

        // place signing middleware after prepare-body so it can access Content-Length header
        $stack->after('prepare_body', Middleware::mapRequest(function (RequestInterface $request) {
            // echo "Request URI: " . $request->getUri() . PHP_EOL;

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
                $clHeaders = $request->getHeader('Content-Length');
                if ($clHeaders != null && count($clHeaders) > 0) {
                    $content_length = $clHeaders[0];
                } else {
                    // if content length is 0 we still need to explicitly send the Content-Length header
                    $content_length = 0;
                    $request = $request->withHeader('Content-Length', 0);
                }

                $content_type = $request->getHeader('Content-Type')[0];
                $content_sha256 = base64_encode(hex2bin(hash("sha256", $request->getBody())));

                $request = $request->withHeader('x-content-sha256', $content_sha256);

                $headers = $headers . " content-length content-type x-content-sha256";
                $signing_string = $signing_string . "\ncontent-length: $content_length\ncontent-type: $content_type\nx-content-sha256: $content_sha256";
            }

            // echo "Signing string:\n$signing_string".PHP_EOL;

            $signature = $this->sign_string($signing_string, $this->auth_provider->getKeyFilename(), $this->auth_provider->getKeyPassphrase());

            $authorization_header = "Signature version=\"1\",keyId=\"{$this->auth_provider->getKeyId()}\",algorithm=\"rsa-sha256\",headers=\"$headers\",signature=\"$signature\"";
            $request = $request->withHeader('Authorization', $authorization_header);

            // echo "\nRequest headers:".PHP_EOL;
            // foreach ($request->getHeaders() as $name => $values) {
            //     echo $name . ': ' . implode(', ', $values) . "\n";
            // }

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
    public function abortMultipartUpload($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->abortMultipartUpload_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "objectName", true),
            HttpUtils::orNull($params, "uploadId", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function abortMultipartUpload_Helper(
        $namespaceName,
        $bucketName,
        $objectName,
        $uploadId,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($uploadId != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "uploadId", HttpUtils::attemptEncodeQueryParam($uploadId));
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'cancelWorkRequest':
    // path /workRequests/{workRequestId}
    public function cancelWorkRequest($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->cancelWorkRequest_Helper(
            HttpUtils::orNull($params, "workRequestId", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function cancelWorkRequest_Helper(
        $workRequestId,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'commitMultipartUpload':
    // path /n/{namespaceName}/b/{bucketName}/u/{objectName}
    public function commitMultipartUpload($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->commitMultipartUpload_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "objectName", true),
            HttpUtils::orNull($params, "uploadId", true),
            HttpUtils::orNull($params, "commitMultipartUploadDetails", true),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "ifNoneMatch"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function commitMultipartUpload_Helper(
        $namespaceName,
        $bucketName,
        $objectName,
        $uploadId,
        $commitMultipartUploadDetails,
        $ifMatch = null,
        $ifNoneMatch = null,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            // isMap? false
            HttpUtils::addToArray($__query, "uploadId", HttpUtils::attemptEncodeQueryParam($uploadId));
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'copyObject':
    // path /n/{namespaceName}/b/{bucketName}/actions/copyObject
    public function copyObject($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->copyObject_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "copyObjectDetails", true),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "opcSseCustomerAlgorithm"),
            HttpUtils::orNull($params, "opcSseCustomerKey"),
            HttpUtils::orNull($params, "opcSseCustomerKeySha256"),
            HttpUtils::orNull($params, "opcSourceSseCustomerAlgorithm"),
            HttpUtils::orNull($params, "opcSourceSseCustomerKey"),
            HttpUtils::orNull($params, "opcSourceSseCustomerKeySha256"),
            HttpUtils::orNull($params, "opcSseKmsKeyId")
        );
    }

    public function copyObject_Helper(
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
        $opcSseKmsKeyId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'createBucket':
    // path /n/{namespaceName}/b/
    public function createBucket($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->createBucket_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "createBucketDetails", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function createBucket_Helper(
        $namespaceName,
        $createBucketDetails,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'createMultipartUpload':
    // path /n/{namespaceName}/b/{bucketName}/u
    public function createMultipartUpload($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->createMultipartUpload_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "createMultipartUploadDetails", true),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "ifNoneMatch"),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "opcSseCustomerAlgorithm"),
            HttpUtils::orNull($params, "opcSseCustomerKey"),
            HttpUtils::orNull($params, "opcSseCustomerKeySha256"),
            HttpUtils::orNull($params, "opcSseKmsKeyId")
        );
    }

    public function createMultipartUpload_Helper(
        $namespaceName,
        $bucketName,
        $createMultipartUploadDetails,
        $ifMatch = null,
        $ifNoneMatch = null,
        $opcClientRequestId = null,
        $opcSseCustomerAlgorithm = null,
        $opcSseCustomerKey = null,
        $opcSseCustomerKeySha256 = null,
        $opcSseKmsKeyId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'createPreauthenticatedRequest':
    // path /n/{namespaceName}/b/{bucketName}/p/
    public function createPreauthenticatedRequest($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->createPreauthenticatedRequest_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "createPreauthenticatedRequestDetails", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function createPreauthenticatedRequest_Helper(
        $namespaceName,
        $bucketName,
        $createPreauthenticatedRequestDetails,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'createReplicationPolicy':
    // path /n/{namespaceName}/b/{bucketName}/replicationPolicies
    public function createReplicationPolicy($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->createReplicationPolicy_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "createReplicationPolicyDetails", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function createReplicationPolicy_Helper(
        $namespaceName,
        $bucketName,
        $createReplicationPolicyDetails,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'createRetentionRule':
    // path /n/{namespaceName}/b/{bucketName}/retentionRules
    public function createRetentionRule($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->createRetentionRule_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "createRetentionRuleDetails", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function createRetentionRule_Helper(
        $namespaceName,
        $bucketName,
        $createRetentionRuleDetails,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'deleteBucket':
    // path /n/{namespaceName}/b/{bucketName}/
    public function deleteBucket($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->deleteBucket_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function deleteBucket_Helper(
        $namespaceName,
        $bucketName,
        $ifMatch = null,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'deleteObject':
    // path /n/{namespaceName}/b/{bucketName}/o/{objectName}
    public function deleteObject($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->deleteObject_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "objectName", true),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "versionId")
        );
    }

    public function deleteObject_Helper(
        $namespaceName,
        $bucketName,
        $objectName,
        $ifMatch = null,
        $opcClientRequestId = null,
        $versionId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            // isMap? false
            HttpUtils::addToArray($__query, "versionId", HttpUtils::attemptEncodeQueryParam($versionId));
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'deleteObjectLifecyclePolicy':
    // path /n/{namespaceName}/b/{bucketName}/l
    public function deleteObjectLifecyclePolicy($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->deleteObjectLifecyclePolicy_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "ifMatch")
        );
    }

    public function deleteObjectLifecyclePolicy_Helper(
        $namespaceName,
        $bucketName,
        $opcClientRequestId = null,
        $ifMatch = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'deletePreauthenticatedRequest':
    // path /n/{namespaceName}/b/{bucketName}/p/{parId}
    public function deletePreauthenticatedRequest($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->deletePreauthenticatedRequest_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "parId", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function deletePreauthenticatedRequest_Helper(
        $namespaceName,
        $bucketName,
        $parId,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'deleteReplicationPolicy':
    // path /n/{namespaceName}/b/{bucketName}/replicationPolicies/{replicationId}
    public function deleteReplicationPolicy($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->deleteReplicationPolicy_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "replicationId", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function deleteReplicationPolicy_Helper(
        $namespaceName,
        $bucketName,
        $replicationId,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'deleteRetentionRule':
    // path /n/{namespaceName}/b/{bucketName}/retentionRules/{retentionRuleId}
    public function deleteRetentionRule($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->deleteRetentionRule_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "retentionRuleId", true),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function deleteRetentionRule_Helper(
        $namespaceName,
        $bucketName,
        $retentionRuleId,
        $ifMatch = null,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'getBucket':
    // path /n/{namespaceName}/b/{bucketName}/
    public function getBucket($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->getBucket_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "ifNoneMatch"),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "fields")
        );
    }

    public function getBucket_Helper(
        $namespaceName,
        $bucketName,
        $ifMatch = null,
        $ifNoneMatch = null,
        $opcClientRequestId = null,
        $fields = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            // isMap? false
            HttpUtils::encodeArray($__query, "fields", $fields, "csv");
        }

        $__path = "/n/{namespaceName}/b/{bucketName}/";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'getNamespace':
    // path /n/
    public function getNamespace($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->getNamespace_Helper(
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "compartmentId")
        );
    }

    public function getNamespace_Helper(
        $opcClientRequestId = null,
        $compartmentId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($compartmentId != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "compartmentId", HttpUtils::attemptEncodeQueryParam($compartmentId));
        }

        $__path = "/n/";

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'getNamespaceMetadata':
    // path /n/{namespaceName}
    public function getNamespaceMetadata($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->getNamespaceMetadata_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function getNamespaceMetadata_Helper(
        $namespaceName,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'getObject':
    // path /n/{namespaceName}/b/{bucketName}/o/{objectName}
    public function getObject($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->getObject_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "objectName", true),
            HttpUtils::orNull($params, "versionId"),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "ifNoneMatch"),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "range"),
            HttpUtils::orNull($params, "opcSseCustomerAlgorithm"),
            HttpUtils::orNull($params, "opcSseCustomerKey"),
            HttpUtils::orNull($params, "opcSseCustomerKeySha256"),
            HttpUtils::orNull($params, "httpResponseContentDisposition"),
            HttpUtils::orNull($params, "httpResponseCacheControl"),
            HttpUtils::orNull($params, "httpResponseContentType"),
            HttpUtils::orNull($params, "httpResponseContentLanguage"),
            HttpUtils::orNull($params, "httpResponseContentEncoding"),
            HttpUtils::orNull($params, "httpResponseExpires")
        );
    }

    public function getObject_Helper(
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
        $httpResponseExpires = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            // isMap? false
            HttpUtils::addToArray($__query, "versionId", HttpUtils::attemptEncodeQueryParam($versionId));
        }
        if ($httpResponseContentDisposition != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "httpResponseContentDisposition", HttpUtils::attemptEncodeQueryParam($httpResponseContentDisposition));
        }
        if ($httpResponseCacheControl != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "httpResponseCacheControl", HttpUtils::attemptEncodeQueryParam($httpResponseCacheControl));
        }
        if ($httpResponseContentType != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "httpResponseContentType", HttpUtils::attemptEncodeQueryParam($httpResponseContentType));
        }
        if ($httpResponseContentLanguage != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "httpResponseContentLanguage", HttpUtils::attemptEncodeQueryParam($httpResponseContentLanguage));
        }
        if ($httpResponseContentEncoding != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "httpResponseContentEncoding", HttpUtils::attemptEncodeQueryParam($httpResponseContentEncoding));
        }
        if ($httpResponseExpires != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "httpResponseExpires", HttpUtils::attemptEncodeQueryParam($httpResponseExpires));
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            $__response->getBody(),
            null);
    }

    // Operation 'getObjectLifecyclePolicy':
    // path /n/{namespaceName}/b/{bucketName}/l
    public function getObjectLifecyclePolicy($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->getObjectLifecyclePolicy_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function getObjectLifecyclePolicy_Helper(
        $namespaceName,
        $bucketName,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'getPreauthenticatedRequest':
    // path /n/{namespaceName}/b/{bucketName}/p/{parId}
    public function getPreauthenticatedRequest($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->getPreauthenticatedRequest_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "parId", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function getPreauthenticatedRequest_Helper(
        $namespaceName,
        $bucketName,
        $parId,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'getReplicationPolicy':
    // path /n/{namespaceName}/b/{bucketName}/replicationPolicies/{replicationId}
    public function getReplicationPolicy($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->getReplicationPolicy_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "replicationId", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function getReplicationPolicy_Helper(
        $namespaceName,
        $bucketName,
        $replicationId,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'getRetentionRule':
    // path /n/{namespaceName}/b/{bucketName}/retentionRules/{retentionRuleId}
    public function getRetentionRule($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->getRetentionRule_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "retentionRuleId", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function getRetentionRule_Helper(
        $namespaceName,
        $bucketName,
        $retentionRuleId,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'getWorkRequest':
    // path /workRequests/{workRequestId}
    public function getWorkRequest($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->getWorkRequest_Helper(
            HttpUtils::orNull($params, "workRequestId", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function getWorkRequest_Helper(
        $workRequestId,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'headBucket':
    // path /n/{namespaceName}/b/{bucketName}/
    public function headBucket($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->headBucket_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "ifNoneMatch"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function headBucket_Helper(
        $namespaceName,
        $bucketName,
        $ifMatch = null,
        $ifNoneMatch = null,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'headObject':
    // path /n/{namespaceName}/b/{bucketName}/o/{objectName}
    public function headObject($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->headObject_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "objectName", true),
            HttpUtils::orNull($params, "versionId"),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "ifNoneMatch"),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "opcSseCustomerAlgorithm"),
            HttpUtils::orNull($params, "opcSseCustomerKey"),
            HttpUtils::orNull($params, "opcSseCustomerKeySha256")
        );
    }

    public function headObject_Helper(
        $namespaceName,
        $bucketName,
        $objectName,
        $versionId = null,
        $ifMatch = null,
        $ifNoneMatch = null,
        $opcClientRequestId = null,
        $opcSseCustomerAlgorithm = null,
        $opcSseCustomerKey = null,
        $opcSseCustomerKeySha256 = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            // isMap? false
            HttpUtils::addToArray($__query, "versionId", HttpUtils::attemptEncodeQueryParam($versionId));
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'listBuckets':
    // path /n/{namespaceName}/b/
    public function listBuckets($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listBuckets_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "compartmentId", true),
            HttpUtils::orNull($params, "limit"),
            HttpUtils::orNull($params, "page"),
            HttpUtils::orNull($params, "fields"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function listBuckets_Helper(
        $namespaceName,
        $compartmentId,
        $limit = null,
        $page = null,
        $fields = null,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($compartmentId != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "compartmentId", HttpUtils::attemptEncodeQueryParam($compartmentId));
        }
        if ($limit != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeQueryParam($limit));
        }
        if ($page != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeQueryParam($page));
        }
        if ($fields != null)
        {
            // isMap? false
            HttpUtils::encodeArray($__query, "fields", $fields, "csv");
        }

        $__path = "/n/{namespaceName}/b/";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'listMultipartUploadParts':
    // path /n/{namespaceName}/b/{bucketName}/u/{objectName}
    public function listMultipartUploadParts($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listMultipartUploadParts_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "objectName", true),
            HttpUtils::orNull($params, "uploadId", true),
            HttpUtils::orNull($params, "limit"),
            HttpUtils::orNull($params, "page"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function listMultipartUploadParts_Helper(
        $namespaceName,
        $bucketName,
        $objectName,
        $uploadId,
        $limit = null,
        $page = null,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($uploadId != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "uploadId", HttpUtils::attemptEncodeQueryParam($uploadId));
        }
        if ($limit != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeQueryParam($limit));
        }
        if ($page != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeQueryParam($page));
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'listMultipartUploads':
    // path /n/{namespaceName}/b/{bucketName}/u
    public function listMultipartUploads($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listMultipartUploads_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "limit"),
            HttpUtils::orNull($params, "page"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function listMultipartUploads_Helper(
        $namespaceName,
        $bucketName,
        $limit = null,
        $page = null,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($limit != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeQueryParam($limit));
        }
        if ($page != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeQueryParam($page));
        }

        $__path = "/n/{namespaceName}/b/{bucketName}/u";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'listObjectVersions':
    // path /n/{namespaceName}/b/{bucketName}/objectversions
    public function listObjectVersions($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listObjectVersions_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "prefix"),
            HttpUtils::orNull($params, "start"),
            HttpUtils::orNull($params, "end"),
            HttpUtils::orNull($params, "limit"),
            HttpUtils::orNull($params, "delimiter"),
            HttpUtils::orNull($params, "fields"),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "startAfter"),
            HttpUtils::orNull($params, "page")
        );
    }

    public function listObjectVersions_Helper(
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
        $page = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($prefix != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "prefix", HttpUtils::attemptEncodeQueryParam($prefix));
        }
        if ($start != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "start", HttpUtils::attemptEncodeQueryParam($start));
        }
        if ($end != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "end", HttpUtils::attemptEncodeQueryParam($end));
        }
        if ($limit != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeQueryParam($limit));
        }
        if ($delimiter != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "delimiter", HttpUtils::attemptEncodeQueryParam($delimiter));
        }
        if ($fields != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "fields", HttpUtils::attemptEncodeQueryParam($fields));
        }
        if ($startAfter != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "startAfter", HttpUtils::attemptEncodeQueryParam($startAfter));
        }
        if ($page != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeQueryParam($page));
        }

        $__path = "/n/{namespaceName}/b/{bucketName}/objectversions";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'listObjects':
    // path /n/{namespaceName}/b/{bucketName}/o
    public function listObjects($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listObjects_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "prefix"),
            HttpUtils::orNull($params, "start"),
            HttpUtils::orNull($params, "end"),
            HttpUtils::orNull($params, "limit"),
            HttpUtils::orNull($params, "delimiter"),
            HttpUtils::orNull($params, "fields"),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "startAfter")
        );
    }

    public function listObjects_Helper(
        $namespaceName,
        $bucketName,
        $prefix = null,
        $start = null,
        $end = null,
        $limit = null,
        $delimiter = null,
        $fields = null,
        $opcClientRequestId = null,
        $startAfter = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($prefix != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "prefix", HttpUtils::attemptEncodeQueryParam($prefix));
        }
        if ($start != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "start", HttpUtils::attemptEncodeQueryParam($start));
        }
        if ($end != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "end", HttpUtils::attemptEncodeQueryParam($end));
        }
        if ($limit != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeQueryParam($limit));
        }
        if ($delimiter != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "delimiter", HttpUtils::attemptEncodeQueryParam($delimiter));
        }
        if ($fields != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "fields", HttpUtils::attemptEncodeQueryParam($fields));
        }
        if ($startAfter != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "startAfter", HttpUtils::attemptEncodeQueryParam($startAfter));
        }

        $__path = "/n/{namespaceName}/b/{bucketName}/o";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'listPreauthenticatedRequests':
    // path /n/{namespaceName}/b/{bucketName}/p/
    public function listPreauthenticatedRequests($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listPreauthenticatedRequests_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "objectNamePrefix"),
            HttpUtils::orNull($params, "limit"),
            HttpUtils::orNull($params, "page"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function listPreauthenticatedRequests_Helper(
        $namespaceName,
        $bucketName,
        $objectNamePrefix = null,
        $limit = null,
        $page = null,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($objectNamePrefix != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "objectNamePrefix", HttpUtils::attemptEncodeQueryParam($objectNamePrefix));
        }
        if ($limit != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeQueryParam($limit));
        }
        if ($page != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeQueryParam($page));
        }

        $__path = "/n/{namespaceName}/b/{bucketName}/p/";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'listReplicationPolicies':
    // path /n/{namespaceName}/b/{bucketName}/replicationPolicies
    public function listReplicationPolicies($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listReplicationPolicies_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "page"),
            HttpUtils::orNull($params, "limit")
        );
    }

    public function listReplicationPolicies_Helper(
        $namespaceName,
        $bucketName,
        $opcClientRequestId = null,
        $page = null,
        $limit = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($page != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeQueryParam($page));
        }
        if ($limit != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeQueryParam($limit));
        }

        $__path = "/n/{namespaceName}/b/{bucketName}/replicationPolicies";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'listReplicationSources':
    // path /n/{namespaceName}/b/{bucketName}/replicationSources
    public function listReplicationSources($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listReplicationSources_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "page"),
            HttpUtils::orNull($params, "limit")
        );
    }

    public function listReplicationSources_Helper(
        $namespaceName,
        $bucketName,
        $opcClientRequestId = null,
        $page = null,
        $limit = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($page != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeQueryParam($page));
        }
        if ($limit != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeQueryParam($limit));
        }

        $__path = "/n/{namespaceName}/b/{bucketName}/replicationSources";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'listRetentionRules':
    // path /n/{namespaceName}/b/{bucketName}/retentionRules
    public function listRetentionRules($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listRetentionRules_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "page")
        );
    }

    public function listRetentionRules_Helper(
        $namespaceName,
        $bucketName,
        $page = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];

        $__query = [];
        if ($page != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeQueryParam($page));
        }

        $__path = "/n/{namespaceName}/b/{bucketName}/retentionRules";
        $__path = str_replace('{namespaceName}', $namespaceName, $__path);
        $__path = str_replace('{bucketName}', $bucketName, $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'listWorkRequestErrors':
    // path /workRequests/{workRequestId}/errors
    public function listWorkRequestErrors($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listWorkRequestErrors_Helper(
            HttpUtils::orNull($params, "workRequestId", true),
            HttpUtils::orNull($params, "page"),
            HttpUtils::orNull($params, "limit"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function listWorkRequestErrors_Helper(
        $workRequestId,
        $page = null,
        $limit = null,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($page != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeQueryParam($page));
        }
        if ($limit != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeQueryParam($limit));
        }

        $__path = "/workRequests/{workRequestId}/errors";
        $__path = str_replace('{workRequestId}', $workRequestId, $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'listWorkRequestLogs':
    // path /workRequests/{workRequestId}/logs
    public function listWorkRequestLogs($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listWorkRequestLogs_Helper(
            HttpUtils::orNull($params, "workRequestId", true),
            HttpUtils::orNull($params, "page"),
            HttpUtils::orNull($params, "limit"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function listWorkRequestLogs_Helper(
        $workRequestId,
        $page = null,
        $limit = null,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($page != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeQueryParam($page));
        }
        if ($limit != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeQueryParam($limit));
        }

        $__path = "/workRequests/{workRequestId}/logs";
        $__path = str_replace('{workRequestId}', $workRequestId, $__path);

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'listWorkRequests':
    // path /workRequests
    public function listWorkRequests($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listWorkRequests_Helper(
            HttpUtils::orNull($params, "compartmentId", true),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "page"),
            HttpUtils::orNull($params, "limit")
        );
    }

    public function listWorkRequests_Helper(
        $compartmentId,
        $opcClientRequestId = null,
        $page = null,
        $limit = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($compartmentId != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "compartmentId", HttpUtils::attemptEncodeQueryParam($compartmentId));
        }
        if ($page != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeQueryParam($page));
        }
        if ($limit != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeQueryParam($limit));
        }

        $__path = "/workRequests";

        $__response = $this->client->get(
            "{$this->endpoint}{$__path}",
            [ 'headers' => $__headers, 'query' => $__query ]
        );
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'makeBucketWritable':
    // path /n/{namespaceName}/b/{bucketName}/actions/makeBucketWritable
    public function makeBucketWritable($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->makeBucketWritable_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function makeBucketWritable_Helper(
        $namespaceName,
        $bucketName,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'putObject':
    // path /n/{namespaceName}/b/{bucketName}/o/{objectName}
    public function putObject($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->putObject_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "objectName", true),
            HttpUtils::orNull($params, "putObjectBody", true),
            HttpUtils::orNull($params, "contentLength"),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "ifNoneMatch"),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "expect"),
            HttpUtils::orNull($params, "contentMD5"),
            HttpUtils::orNull($params, "contentType"),
            HttpUtils::orNull($params, "contentLanguage"),
            HttpUtils::orNull($params, "contentEncoding"),
            HttpUtils::orNull($params, "contentDisposition"),
            HttpUtils::orNull($params, "cacheControl"),
            HttpUtils::orNull($params, "opcSseCustomerAlgorithm"),
            HttpUtils::orNull($params, "opcSseCustomerKey"),
            HttpUtils::orNull($params, "opcSseCustomerKeySha256"),
            HttpUtils::orNull($params, "opcSseKmsKeyId"),
            HttpUtils::orNull($params, "storageTier"),
            HttpUtils::orNull($params, "opcMeta")
        );
    }

    public function putObject_Helper(
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
        $opcMeta = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'putObjectLifecyclePolicy':
    // path /n/{namespaceName}/b/{bucketName}/l
    public function putObjectLifecyclePolicy($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->putObjectLifecyclePolicy_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "putObjectLifecyclePolicyDetails", true),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "ifNoneMatch")
        );
    }

    public function putObjectLifecyclePolicy_Helper(
        $namespaceName,
        $bucketName,
        $putObjectLifecyclePolicyDetails,
        $opcClientRequestId = null,
        $ifMatch = null,
        $ifNoneMatch = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'reencryptBucket':
    // path /n/{namespaceName}/b/{bucketName}/actions/reencrypt
    public function reencryptBucket($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->reencryptBucket_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function reencryptBucket_Helper(
        $namespaceName,
        $bucketName,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'reencryptObject':
    // path /n/{namespaceName}/b/{bucketName}/actions/reencrypt/{objectName}
    public function reencryptObject($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->reencryptObject_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "objectName", true),
            HttpUtils::orNull($params, "reencryptObjectDetails", true),
            HttpUtils::orNull($params, "versionId"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function reencryptObject_Helper(
        $namespaceName,
        $bucketName,
        $objectName,
        $reencryptObjectDetails,
        $versionId = null,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null)
        {
            $__headers['opcClientRequestId'] = $opcClientRequestId;
        }

        $__query = [];
        if ($versionId != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "versionId", HttpUtils::attemptEncodeQueryParam($versionId));
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'renameObject':
    // path /n/{namespaceName}/b/{bucketName}/actions/renameObject
    public function renameObject($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->renameObject_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "renameObjectDetails", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function renameObject_Helper(
        $namespaceName,
        $bucketName,
        $renameObjectDetails,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'restoreObjects':
    // path /n/{namespaceName}/b/{bucketName}/actions/restoreObjects
    public function restoreObjects($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->restoreObjects_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "restoreObjectsDetails", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function restoreObjects_Helper(
        $namespaceName,
        $bucketName,
        $restoreObjectsDetails,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'updateBucket':
    // path /n/{namespaceName}/b/{bucketName}/
    public function updateBucket($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->updateBucket_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "updateBucketDetails", true),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function updateBucket_Helper(
        $namespaceName,
        $bucketName,
        $updateBucketDetails,
        $ifMatch = null,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'updateNamespaceMetadata':
    // path /n/{namespaceName}
    public function updateNamespaceMetadata($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->updateNamespaceMetadata_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "updateNamespaceMetadataDetails", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function updateNamespaceMetadata_Helper(
        $namespaceName,
        $updateNamespaceMetadataDetails,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'updateObjectStorageTier':
    // path /n/{namespaceName}/b/{bucketName}/actions/updateObjectStorageTier
    public function updateObjectStorageTier($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->updateObjectStorageTier_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "updateObjectStorageTierDetails", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function updateObjectStorageTier_Helper(
        $namespaceName,
        $bucketName,
        $updateObjectStorageTierDetails,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'updateRetentionRule':
    // path /n/{namespaceName}/b/{bucketName}/retentionRules/{retentionRuleId}
    public function updateRetentionRule($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->updateRetentionRule_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "retentionRuleId", true),
            HttpUtils::orNull($params, "updateRetentionRuleDetails", true),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function updateRetentionRule_Helper(
        $namespaceName,
        $bucketName,
        $retentionRuleId,
        $updateRetentionRuleDetails,
        $ifMatch = null,
        $opcClientRequestId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

    // Operation 'uploadPart':
    // path /n/{namespaceName}/b/{bucketName}/u/{objectName}
    public function uploadPart($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1))
        {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->uploadPart_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "objectName", true),
            HttpUtils::orNull($params, "uploadId", true),
            HttpUtils::orNull($params, "uploadPartNum", true),
            HttpUtils::orNull($params, "uploadPartBody", true),
            HttpUtils::orNull($params, "contentLength"),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "ifNoneMatch"),
            HttpUtils::orNull($params, "expect"),
            HttpUtils::orNull($params, "contentMD5"),
            HttpUtils::orNull($params, "opcSseCustomerAlgorithm"),
            HttpUtils::orNull($params, "opcSseCustomerKey"),
            HttpUtils::orNull($params, "opcSseCustomerKeySha256"),
            HttpUtils::orNull($params, "opcSseKmsKeyId")
        );
    }

    public function uploadPart_Helper(
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
        $opcSseKmsKeyId = null
    )
    {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
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
            // isMap? false
            HttpUtils::addToArray($__query, "uploadId", HttpUtils::attemptEncodeQueryParam($uploadId));
        }
        if ($uploadPartNum != null)
        {
            // isMap? false
            HttpUtils::addToArray($__query, "uploadPartNum", HttpUtils::attemptEncodeQueryParam($uploadPartNum));
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
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody()));
    }

}
?>