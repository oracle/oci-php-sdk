<?php

// Generated using OracleSDKGenerator, API Version: 20160918

namespace Oracle\Oci\ObjectStorage;

use InvalidArgumentException;
use Oracle\Oci\Common\Auth\AuthProviderInterface;
use Oracle\Oci\Common\HttpUtils;
use Oracle\Oci\Common\OciResponse;
use Oracle\Oci\Common\UserAgent;
use Oracle\Oci\Common\AbstractClient;

use function Oracle\Oci\Common\getPerOperationSigningStrategyNameHeaderName;
use function Oracle\Oci\Common\getSigningStrategy;

class ObjectStorageAsyncClient extends AbstractClient
{
    /*const*/ protected static $endpointTemplate = "https://objectstorage.{region}.{secondLevelDomain}";

    public function __construct(
        AuthProviderInterface $auth_provider,
        $region=null,
        $endpoint=null
    )
    {
        parent::__construct(
            ObjectStorageAsyncClient::$endpointTemplate,
            $auth_provider,
            getSigningStrategy("STANDARD"),
            $region,
            $endpoint
        );
    }


    // Should have waiters.

    // Should have paginators.

    // Operation 'abortMultipartUpload':
    // path /n/{namespaceName}/b/{bucketName}/u/{objectName}
    public function abortMultipartUploadAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->abortMultipartUploadAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "objectName", true),
            HttpUtils::orNull($params, "uploadId", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function abortMultipartUploadAsync_Helper(
        $namespaceName,
        $bucketName,
        $objectName,
        $uploadId,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($uploadId != null) {
            HttpUtils::addToArray($__query, "uploadId", HttpUtils::attemptEncodeParam($uploadId));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/u/{objectName}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{objectName}', utf8_encode($objectName), $__path);

        return $this->client->deleteAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'cancelWorkRequest':
    // path /workRequests/{workRequestId}
    public function cancelWorkRequestAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->cancelWorkRequestAsync_Helper(
            HttpUtils::orNull($params, "workRequestId", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function cancelWorkRequestAsync_Helper(
        $workRequestId,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/workRequests/{workRequestId}";
        $__path = str_replace('{workRequestId}', utf8_encode($workRequestId), $__path);

        return $this->client->deleteAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'commitMultipartUpload':
    // path /n/{namespaceName}/b/{bucketName}/u/{objectName}
    public function commitMultipartUploadAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->commitMultipartUploadAsync_Helper(
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

    public function commitMultipartUploadAsync_Helper(
        $namespaceName,
        $bucketName,
        $objectName,
        $uploadId,
        $commitMultipartUploadDetails,
        $ifMatch = null,
        $ifNoneMatch = null,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($ifMatch != null) {
            HttpUtils::addToArray($__headers, "ifMatch", HttpUtils::attemptEncodeParam($ifMatch));
        }
        if ($ifNoneMatch != null) {
            HttpUtils::addToArray($__headers, "ifNoneMatch", HttpUtils::attemptEncodeParam($ifNoneMatch));
        }
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($uploadId != null) {
            HttpUtils::addToArray($__query, "uploadId", HttpUtils::attemptEncodeParam($uploadId));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/u/{objectName}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{objectName}', utf8_encode($objectName), $__path);

        $__body = json_encode($commitMultipartUploadDetails);

        return $this->client->postAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'copyObject':
    // path /n/{namespaceName}/b/{bucketName}/actions/copyObject
    public function copyObjectAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->copyObjectAsync_Helper(
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

    public function copyObjectAsync_Helper(
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
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }
        if ($opcSseCustomerAlgorithm != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerAlgorithm", HttpUtils::attemptEncodeParam($opcSseCustomerAlgorithm));
        }
        if ($opcSseCustomerKey != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerKey", HttpUtils::attemptEncodeParam($opcSseCustomerKey));
        }
        if ($opcSseCustomerKeySha256 != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerKeySha256", HttpUtils::attemptEncodeParam($opcSseCustomerKeySha256));
        }
        if ($opcSourceSseCustomerAlgorithm != null) {
            HttpUtils::addToArray($__headers, "opcSourceSseCustomerAlgorithm", HttpUtils::attemptEncodeParam($opcSourceSseCustomerAlgorithm));
        }
        if ($opcSourceSseCustomerKey != null) {
            HttpUtils::addToArray($__headers, "opcSourceSseCustomerKey", HttpUtils::attemptEncodeParam($opcSourceSseCustomerKey));
        }
        if ($opcSourceSseCustomerKeySha256 != null) {
            HttpUtils::addToArray($__headers, "opcSourceSseCustomerKeySha256", HttpUtils::attemptEncodeParam($opcSourceSseCustomerKeySha256));
        }
        if ($opcSseKmsKeyId != null) {
            HttpUtils::addToArray($__headers, "opcSseKmsKeyId", HttpUtils::attemptEncodeParam($opcSseKmsKeyId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/actions/copyObject";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__body = json_encode($copyObjectDetails);

        return $this->client->postAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'createBucket':
    // path /n/{namespaceName}/b/
    public function createBucketAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->createBucketAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "createBucketDetails", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function createBucketAsync_Helper(
        $namespaceName,
        $createBucketDetails,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);

        $__body = json_encode($createBucketDetails);

        return $this->client->postAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'createMultipartUpload':
    // path /n/{namespaceName}/b/{bucketName}/u
    public function createMultipartUploadAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->createMultipartUploadAsync_Helper(
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

    public function createMultipartUploadAsync_Helper(
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
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($ifMatch != null) {
            HttpUtils::addToArray($__headers, "ifMatch", HttpUtils::attemptEncodeParam($ifMatch));
        }
        if ($ifNoneMatch != null) {
            HttpUtils::addToArray($__headers, "ifNoneMatch", HttpUtils::attemptEncodeParam($ifNoneMatch));
        }
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }
        if ($opcSseCustomerAlgorithm != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerAlgorithm", HttpUtils::attemptEncodeParam($opcSseCustomerAlgorithm));
        }
        if ($opcSseCustomerKey != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerKey", HttpUtils::attemptEncodeParam($opcSseCustomerKey));
        }
        if ($opcSseCustomerKeySha256 != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerKeySha256", HttpUtils::attemptEncodeParam($opcSseCustomerKeySha256));
        }
        if ($opcSseKmsKeyId != null) {
            HttpUtils::addToArray($__headers, "opcSseKmsKeyId", HttpUtils::attemptEncodeParam($opcSseKmsKeyId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/u";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__body = json_encode($createMultipartUploadDetails);

        return $this->client->postAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'createPreauthenticatedRequest':
    // path /n/{namespaceName}/b/{bucketName}/p/
    public function createPreauthenticatedRequestAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->createPreauthenticatedRequestAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "createPreauthenticatedRequestDetails", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function createPreauthenticatedRequestAsync_Helper(
        $namespaceName,
        $bucketName,
        $createPreauthenticatedRequestDetails,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/p/";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__body = json_encode($createPreauthenticatedRequestDetails);

        return $this->client->postAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'createReplicationPolicy':
    // path /n/{namespaceName}/b/{bucketName}/replicationPolicies
    public function createReplicationPolicyAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->createReplicationPolicyAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "createReplicationPolicyDetails", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function createReplicationPolicyAsync_Helper(
        $namespaceName,
        $bucketName,
        $createReplicationPolicyDetails,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/replicationPolicies";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__body = json_encode($createReplicationPolicyDetails);

        return $this->client->postAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'createRetentionRule':
    // path /n/{namespaceName}/b/{bucketName}/retentionRules
    public function createRetentionRuleAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->createRetentionRuleAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "createRetentionRuleDetails", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function createRetentionRuleAsync_Helper(
        $namespaceName,
        $bucketName,
        $createRetentionRuleDetails,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/retentionRules";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__body = json_encode($createRetentionRuleDetails);

        return $this->client->postAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'deleteBucket':
    // path /n/{namespaceName}/b/{bucketName}/
    public function deleteBucketAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->deleteBucketAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function deleteBucketAsync_Helper(
        $namespaceName,
        $bucketName,
        $ifMatch = null,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($ifMatch != null) {
            HttpUtils::addToArray($__headers, "ifMatch", HttpUtils::attemptEncodeParam($ifMatch));
        }
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        return $this->client->deleteAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'deleteObject':
    // path /n/{namespaceName}/b/{bucketName}/o/{objectName}
    public function deleteObjectAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->deleteObjectAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "objectName", true),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "versionId")
        );
    }

    public function deleteObjectAsync_Helper(
        $namespaceName,
        $bucketName,
        $objectName,
        $ifMatch = null,
        $opcClientRequestId = null,
        $versionId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($ifMatch != null) {
            HttpUtils::addToArray($__headers, "ifMatch", HttpUtils::attemptEncodeParam($ifMatch));
        }
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($versionId != null) {
            HttpUtils::addToArray($__query, "versionId", HttpUtils::attemptEncodeParam($versionId));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/o/{objectName}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{objectName}', utf8_encode($objectName), $__path);

        return $this->client->deleteAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'deleteObjectLifecyclePolicy':
    // path /n/{namespaceName}/b/{bucketName}/l
    public function deleteObjectLifecyclePolicyAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->deleteObjectLifecyclePolicyAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "ifMatch")
        );
    }

    public function deleteObjectLifecyclePolicyAsync_Helper(
        $namespaceName,
        $bucketName,
        $opcClientRequestId = null,
        $ifMatch = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }
        if ($ifMatch != null) {
            HttpUtils::addToArray($__headers, "ifMatch", HttpUtils::attemptEncodeParam($ifMatch));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/l";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        return $this->client->deleteAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'deletePreauthenticatedRequest':
    // path /n/{namespaceName}/b/{bucketName}/p/{parId}
    public function deletePreauthenticatedRequestAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->deletePreauthenticatedRequestAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "parId", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function deletePreauthenticatedRequestAsync_Helper(
        $namespaceName,
        $bucketName,
        $parId,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/p/{parId}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{parId}', utf8_encode($parId), $__path);

        return $this->client->deleteAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'deleteReplicationPolicy':
    // path /n/{namespaceName}/b/{bucketName}/replicationPolicies/{replicationId}
    public function deleteReplicationPolicyAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->deleteReplicationPolicyAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "replicationId", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function deleteReplicationPolicyAsync_Helper(
        $namespaceName,
        $bucketName,
        $replicationId,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/replicationPolicies/{replicationId}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{replicationId}', utf8_encode($replicationId), $__path);

        return $this->client->deleteAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'deleteRetentionRule':
    // path /n/{namespaceName}/b/{bucketName}/retentionRules/{retentionRuleId}
    public function deleteRetentionRuleAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->deleteRetentionRuleAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "retentionRuleId", true),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function deleteRetentionRuleAsync_Helper(
        $namespaceName,
        $bucketName,
        $retentionRuleId,
        $ifMatch = null,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($ifMatch != null) {
            HttpUtils::addToArray($__headers, "ifMatch", HttpUtils::attemptEncodeParam($ifMatch));
        }
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/retentionRules/{retentionRuleId}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{retentionRuleId}', utf8_encode($retentionRuleId), $__path);

        return $this->client->deleteAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'getBucket':
    // path /n/{namespaceName}/b/{bucketName}/
    public function getBucketAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->getBucketAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "ifNoneMatch"),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "fields")
        );
    }

    public function getBucketAsync_Helper(
        $namespaceName,
        $bucketName,
        $ifMatch = null,
        $ifNoneMatch = null,
        $opcClientRequestId = null,
        $fields = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($ifMatch != null) {
            HttpUtils::addToArray($__headers, "ifMatch", HttpUtils::attemptEncodeParam($ifMatch));
        }
        if ($ifNoneMatch != null) {
            HttpUtils::addToArray($__headers, "ifNoneMatch", HttpUtils::attemptEncodeParam($ifNoneMatch));
        }
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($fields != null) {
            HttpUtils::encodeArray($__query, "fields", $fields, "csv");
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        return $this->client->getAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'getNamespace':
    // path /n/
    public function getNamespaceAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->getNamespaceAsync_Helper(
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "compartmentId")
        );
    }

    public function getNamespaceAsync_Helper(
        $opcClientRequestId = null,
        $compartmentId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($compartmentId != null) {
            HttpUtils::addToArray($__query, "compartmentId", HttpUtils::attemptEncodeParam($compartmentId));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/";

        return $this->client->getAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'getNamespaceMetadata':
    // path /n/{namespaceName}
    public function getNamespaceMetadataAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->getNamespaceMetadataAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function getNamespaceMetadataAsync_Helper(
        $namespaceName,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);

        return $this->client->getAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'getObject':
    // path /n/{namespaceName}/b/{bucketName}/o/{objectName}
    public function getObjectAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->getObjectAsync_Helper(
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

    public function getObjectAsync_Helper(
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
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($ifMatch != null) {
            HttpUtils::addToArray($__headers, "ifMatch", HttpUtils::attemptEncodeParam($ifMatch));
        }
        if ($ifNoneMatch != null) {
            HttpUtils::addToArray($__headers, "ifNoneMatch", HttpUtils::attemptEncodeParam($ifNoneMatch));
        }
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }
        if ($range != null) {
            HttpUtils::addToArray($__headers, "range", HttpUtils::attemptEncodeParam($range));
        }
        if ($opcSseCustomerAlgorithm != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerAlgorithm", HttpUtils::attemptEncodeParam($opcSseCustomerAlgorithm));
        }
        if ($opcSseCustomerKey != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerKey", HttpUtils::attemptEncodeParam($opcSseCustomerKey));
        }
        if ($opcSseCustomerKeySha256 != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerKeySha256", HttpUtils::attemptEncodeParam($opcSseCustomerKeySha256));
        }

        $__query = [];
        if ($versionId != null) {
            HttpUtils::addToArray($__query, "versionId", HttpUtils::attemptEncodeParam($versionId));
        }
        if ($httpResponseContentDisposition != null) {
            HttpUtils::addToArray($__query, "httpResponseContentDisposition", HttpUtils::attemptEncodeParam($httpResponseContentDisposition));
        }
        if ($httpResponseCacheControl != null) {
            HttpUtils::addToArray($__query, "httpResponseCacheControl", HttpUtils::attemptEncodeParam($httpResponseCacheControl));
        }
        if ($httpResponseContentType != null) {
            HttpUtils::addToArray($__query, "httpResponseContentType", HttpUtils::attemptEncodeParam($httpResponseContentType));
        }
        if ($httpResponseContentLanguage != null) {
            HttpUtils::addToArray($__query, "httpResponseContentLanguage", HttpUtils::attemptEncodeParam($httpResponseContentLanguage));
        }
        if ($httpResponseContentEncoding != null) {
            HttpUtils::addToArray($__query, "httpResponseContentEncoding", HttpUtils::attemptEncodeParam($httpResponseContentEncoding));
        }
        if ($httpResponseExpires != null) {
            HttpUtils::addToArray($__query, "httpResponseExpires", HttpUtils::attemptEncodeParam($httpResponseExpires));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/o/{objectName}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{objectName}', utf8_encode($objectName), $__path);

        return $this->client->getAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            $__response->getBody(),
            null
        );
    });
    }

    // Operation 'getObjectLifecyclePolicy':
    // path /n/{namespaceName}/b/{bucketName}/l
    public function getObjectLifecyclePolicyAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->getObjectLifecyclePolicyAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function getObjectLifecyclePolicyAsync_Helper(
        $namespaceName,
        $bucketName,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/l";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        return $this->client->getAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'getPreauthenticatedRequest':
    // path /n/{namespaceName}/b/{bucketName}/p/{parId}
    public function getPreauthenticatedRequestAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->getPreauthenticatedRequestAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "parId", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function getPreauthenticatedRequestAsync_Helper(
        $namespaceName,
        $bucketName,
        $parId,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/p/{parId}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{parId}', utf8_encode($parId), $__path);

        return $this->client->getAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'getReplicationPolicy':
    // path /n/{namespaceName}/b/{bucketName}/replicationPolicies/{replicationId}
    public function getReplicationPolicyAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->getReplicationPolicyAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "replicationId", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function getReplicationPolicyAsync_Helper(
        $namespaceName,
        $bucketName,
        $replicationId,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/replicationPolicies/{replicationId}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{replicationId}', utf8_encode($replicationId), $__path);

        return $this->client->getAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'getRetentionRule':
    // path /n/{namespaceName}/b/{bucketName}/retentionRules/{retentionRuleId}
    public function getRetentionRuleAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->getRetentionRuleAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "retentionRuleId", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function getRetentionRuleAsync_Helper(
        $namespaceName,
        $bucketName,
        $retentionRuleId,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/retentionRules/{retentionRuleId}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{retentionRuleId}', utf8_encode($retentionRuleId), $__path);

        return $this->client->getAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'getWorkRequest':
    // path /workRequests/{workRequestId}
    public function getWorkRequestAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->getWorkRequestAsync_Helper(
            HttpUtils::orNull($params, "workRequestId", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function getWorkRequestAsync_Helper(
        $workRequestId,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/workRequests/{workRequestId}";
        $__path = str_replace('{workRequestId}', utf8_encode($workRequestId), $__path);

        return $this->client->getAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'headBucket':
    // path /n/{namespaceName}/b/{bucketName}/
    public function headBucketAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->headBucketAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "ifNoneMatch"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function headBucketAsync_Helper(
        $namespaceName,
        $bucketName,
        $ifMatch = null,
        $ifNoneMatch = null,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($ifMatch != null) {
            HttpUtils::addToArray($__headers, "ifMatch", HttpUtils::attemptEncodeParam($ifMatch));
        }
        if ($ifNoneMatch != null) {
            HttpUtils::addToArray($__headers, "ifNoneMatch", HttpUtils::attemptEncodeParam($ifNoneMatch));
        }
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        return $this->client->headAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'headObject':
    // path /n/{namespaceName}/b/{bucketName}/o/{objectName}
    public function headObjectAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->headObjectAsync_Helper(
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

    public function headObjectAsync_Helper(
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
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($ifMatch != null) {
            HttpUtils::addToArray($__headers, "ifMatch", HttpUtils::attemptEncodeParam($ifMatch));
        }
        if ($ifNoneMatch != null) {
            HttpUtils::addToArray($__headers, "ifNoneMatch", HttpUtils::attemptEncodeParam($ifNoneMatch));
        }
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }
        if ($opcSseCustomerAlgorithm != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerAlgorithm", HttpUtils::attemptEncodeParam($opcSseCustomerAlgorithm));
        }
        if ($opcSseCustomerKey != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerKey", HttpUtils::attemptEncodeParam($opcSseCustomerKey));
        }
        if ($opcSseCustomerKeySha256 != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerKeySha256", HttpUtils::attemptEncodeParam($opcSseCustomerKeySha256));
        }

        $__query = [];
        if ($versionId != null) {
            HttpUtils::addToArray($__query, "versionId", HttpUtils::attemptEncodeParam($versionId));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/o/{objectName}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{objectName}', utf8_encode($objectName), $__path);

        return $this->client->headAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'listBuckets':
    // path /n/{namespaceName}/b/
    public function listBucketsAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listBucketsAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "compartmentId", true),
            HttpUtils::orNull($params, "limit"),
            HttpUtils::orNull($params, "page"),
            HttpUtils::orNull($params, "fields"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function listBucketsAsync_Helper(
        $namespaceName,
        $compartmentId,
        $limit = null,
        $page = null,
        $fields = null,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($compartmentId != null) {
            HttpUtils::addToArray($__query, "compartmentId", HttpUtils::attemptEncodeParam($compartmentId));
        }
        if ($limit != null) {
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeParam($limit));
        }
        if ($page != null) {
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeParam($page));
        }
        if ($fields != null) {
            HttpUtils::encodeArray($__query, "fields", $fields, "csv");
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);

        return $this->client->getAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'listMultipartUploadParts':
    // path /n/{namespaceName}/b/{bucketName}/u/{objectName}
    public function listMultipartUploadPartsAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listMultipartUploadPartsAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "objectName", true),
            HttpUtils::orNull($params, "uploadId", true),
            HttpUtils::orNull($params, "limit"),
            HttpUtils::orNull($params, "page"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function listMultipartUploadPartsAsync_Helper(
        $namespaceName,
        $bucketName,
        $objectName,
        $uploadId,
        $limit = null,
        $page = null,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($uploadId != null) {
            HttpUtils::addToArray($__query, "uploadId", HttpUtils::attemptEncodeParam($uploadId));
        }
        if ($limit != null) {
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeParam($limit));
        }
        if ($page != null) {
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeParam($page));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/u/{objectName}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{objectName}', utf8_encode($objectName), $__path);

        return $this->client->getAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'listMultipartUploads':
    // path /n/{namespaceName}/b/{bucketName}/u
    public function listMultipartUploadsAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listMultipartUploadsAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "limit"),
            HttpUtils::orNull($params, "page"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function listMultipartUploadsAsync_Helper(
        $namespaceName,
        $bucketName,
        $limit = null,
        $page = null,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($limit != null) {
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeParam($limit));
        }
        if ($page != null) {
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeParam($page));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/u";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        return $this->client->getAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'listObjectVersions':
    // path /n/{namespaceName}/b/{bucketName}/objectversions
    public function listObjectVersionsAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listObjectVersionsAsync_Helper(
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

    public function listObjectVersionsAsync_Helper(
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
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($prefix != null) {
            HttpUtils::addToArray($__query, "prefix", HttpUtils::attemptEncodeParam($prefix));
        }
        if ($start != null) {
            HttpUtils::addToArray($__query, "start", HttpUtils::attemptEncodeParam($start));
        }
        if ($end != null) {
            HttpUtils::addToArray($__query, "end", HttpUtils::attemptEncodeParam($end));
        }
        if ($limit != null) {
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeParam($limit));
        }
        if ($delimiter != null) {
            HttpUtils::addToArray($__query, "delimiter", HttpUtils::attemptEncodeParam($delimiter));
        }
        if ($fields != null) {
            HttpUtils::addToArray($__query, "fields", HttpUtils::attemptEncodeParam($fields));
        }
        if ($startAfter != null) {
            HttpUtils::addToArray($__query, "startAfter", HttpUtils::attemptEncodeParam($startAfter));
        }
        if ($page != null) {
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeParam($page));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/objectversions";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        return $this->client->getAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'listObjects':
    // path /n/{namespaceName}/b/{bucketName}/o
    public function listObjectsAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listObjectsAsync_Helper(
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

    public function listObjectsAsync_Helper(
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
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($prefix != null) {
            HttpUtils::addToArray($__query, "prefix", HttpUtils::attemptEncodeParam($prefix));
        }
        if ($start != null) {
            HttpUtils::addToArray($__query, "start", HttpUtils::attemptEncodeParam($start));
        }
        if ($end != null) {
            HttpUtils::addToArray($__query, "end", HttpUtils::attemptEncodeParam($end));
        }
        if ($limit != null) {
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeParam($limit));
        }
        if ($delimiter != null) {
            HttpUtils::addToArray($__query, "delimiter", HttpUtils::attemptEncodeParam($delimiter));
        }
        if ($fields != null) {
            HttpUtils::addToArray($__query, "fields", HttpUtils::attemptEncodeParam($fields));
        }
        if ($startAfter != null) {
            HttpUtils::addToArray($__query, "startAfter", HttpUtils::attemptEncodeParam($startAfter));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/o";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        return $this->client->getAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'listPreauthenticatedRequests':
    // path /n/{namespaceName}/b/{bucketName}/p/
    public function listPreauthenticatedRequestsAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listPreauthenticatedRequestsAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "objectNamePrefix"),
            HttpUtils::orNull($params, "limit"),
            HttpUtils::orNull($params, "page"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function listPreauthenticatedRequestsAsync_Helper(
        $namespaceName,
        $bucketName,
        $objectNamePrefix = null,
        $limit = null,
        $page = null,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($objectNamePrefix != null) {
            HttpUtils::addToArray($__query, "objectNamePrefix", HttpUtils::attemptEncodeParam($objectNamePrefix));
        }
        if ($limit != null) {
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeParam($limit));
        }
        if ($page != null) {
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeParam($page));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/p/";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        return $this->client->getAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'listReplicationPolicies':
    // path /n/{namespaceName}/b/{bucketName}/replicationPolicies
    public function listReplicationPoliciesAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listReplicationPoliciesAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "page"),
            HttpUtils::orNull($params, "limit")
        );
    }

    public function listReplicationPoliciesAsync_Helper(
        $namespaceName,
        $bucketName,
        $opcClientRequestId = null,
        $page = null,
        $limit = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($page != null) {
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeParam($page));
        }
        if ($limit != null) {
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeParam($limit));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/replicationPolicies";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        return $this->client->getAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'listReplicationSources':
    // path /n/{namespaceName}/b/{bucketName}/replicationSources
    public function listReplicationSourcesAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listReplicationSourcesAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "page"),
            HttpUtils::orNull($params, "limit")
        );
    }

    public function listReplicationSourcesAsync_Helper(
        $namespaceName,
        $bucketName,
        $opcClientRequestId = null,
        $page = null,
        $limit = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($page != null) {
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeParam($page));
        }
        if ($limit != null) {
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeParam($limit));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/replicationSources";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        return $this->client->getAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'listRetentionRules':
    // path /n/{namespaceName}/b/{bucketName}/retentionRules
    public function listRetentionRulesAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listRetentionRulesAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "page")
        );
    }

    public function listRetentionRulesAsync_Helper(
        $namespaceName,
        $bucketName,
        $page = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];

        $__query = [];
        if ($page != null) {
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeParam($page));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/retentionRules";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        return $this->client->getAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'listWorkRequestErrors':
    // path /workRequests/{workRequestId}/errors
    public function listWorkRequestErrorsAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listWorkRequestErrorsAsync_Helper(
            HttpUtils::orNull($params, "workRequestId", true),
            HttpUtils::orNull($params, "page"),
            HttpUtils::orNull($params, "limit"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function listWorkRequestErrorsAsync_Helper(
        $workRequestId,
        $page = null,
        $limit = null,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($page != null) {
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeParam($page));
        }
        if ($limit != null) {
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeParam($limit));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/workRequests/{workRequestId}/errors";
        $__path = str_replace('{workRequestId}', utf8_encode($workRequestId), $__path);

        return $this->client->getAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'listWorkRequestLogs':
    // path /workRequests/{workRequestId}/logs
    public function listWorkRequestLogsAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listWorkRequestLogsAsync_Helper(
            HttpUtils::orNull($params, "workRequestId", true),
            HttpUtils::orNull($params, "page"),
            HttpUtils::orNull($params, "limit"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function listWorkRequestLogsAsync_Helper(
        $workRequestId,
        $page = null,
        $limit = null,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($page != null) {
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeParam($page));
        }
        if ($limit != null) {
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeParam($limit));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/workRequests/{workRequestId}/logs";
        $__path = str_replace('{workRequestId}', utf8_encode($workRequestId), $__path);

        return $this->client->getAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'listWorkRequests':
    // path /workRequests
    public function listWorkRequestsAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->listWorkRequestsAsync_Helper(
            HttpUtils::orNull($params, "compartmentId", true),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "page"),
            HttpUtils::orNull($params, "limit")
        );
    }

    public function listWorkRequestsAsync_Helper(
        $compartmentId,
        $opcClientRequestId = null,
        $page = null,
        $limit = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($compartmentId != null) {
            HttpUtils::addToArray($__query, "compartmentId", HttpUtils::attemptEncodeParam($compartmentId));
        }
        if ($page != null) {
            HttpUtils::addToArray($__query, "page", HttpUtils::attemptEncodeParam($page));
        }
        if ($limit != null) {
            HttpUtils::addToArray($__query, "limit", HttpUtils::attemptEncodeParam($limit));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/workRequests";

        return $this->client->getAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'makeBucketWritable':
    // path /n/{namespaceName}/b/{bucketName}/actions/makeBucketWritable
    public function makeBucketWritableAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->makeBucketWritableAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function makeBucketWritableAsync_Helper(
        $namespaceName,
        $bucketName,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/actions/makeBucketWritable";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        return $this->client->postAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'putObject':
    // path /n/{namespaceName}/b/{bucketName}/o/{objectName}
    public function putObjectAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->putObjectAsync_Helper(
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

    public function putObjectAsync_Helper(
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
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($ifMatch != null) {
            HttpUtils::addToArray($__headers, "ifMatch", HttpUtils::attemptEncodeParam($ifMatch));
        }
        if ($ifNoneMatch != null) {
            HttpUtils::addToArray($__headers, "ifNoneMatch", HttpUtils::attemptEncodeParam($ifNoneMatch));
        }
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }
        if ($expect != null) {
            HttpUtils::addToArray($__headers, "expect", HttpUtils::attemptEncodeParam($expect));
        }
        if ($contentLength != null) {
            HttpUtils::addToArray($__headers, "contentLength", HttpUtils::attemptEncodeParam($contentLength));
        }
        if ($contentMD5 != null) {
            HttpUtils::addToArray($__headers, "contentMD5", HttpUtils::attemptEncodeParam($contentMD5));
        }
        if ($contentType != null) {
            HttpUtils::addToArray($__headers, "contentType", HttpUtils::attemptEncodeParam($contentType));
        }
        if ($contentLanguage != null) {
            HttpUtils::addToArray($__headers, "contentLanguage", HttpUtils::attemptEncodeParam($contentLanguage));
        }
        if ($contentEncoding != null) {
            HttpUtils::addToArray($__headers, "contentEncoding", HttpUtils::attemptEncodeParam($contentEncoding));
        }
        if ($contentDisposition != null) {
            HttpUtils::addToArray($__headers, "contentDisposition", HttpUtils::attemptEncodeParam($contentDisposition));
        }
        if ($cacheControl != null) {
            HttpUtils::addToArray($__headers, "cacheControl", HttpUtils::attemptEncodeParam($cacheControl));
        }
        if ($opcSseCustomerAlgorithm != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerAlgorithm", HttpUtils::attemptEncodeParam($opcSseCustomerAlgorithm));
        }
        if ($opcSseCustomerKey != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerKey", HttpUtils::attemptEncodeParam($opcSseCustomerKey));
        }
        if ($opcSseCustomerKeySha256 != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerKeySha256", HttpUtils::attemptEncodeParam($opcSseCustomerKeySha256));
        }
        if ($opcSseKmsKeyId != null) {
            HttpUtils::addToArray($__headers, "opcSseKmsKeyId", HttpUtils::attemptEncodeParam($opcSseKmsKeyId));
        }
        if ($storageTier != null) {
            HttpUtils::addToArray($__headers, "storageTier", HttpUtils::attemptEncodeParam($storageTier));
        }
        if ($opcMeta != null) {
            HttpUtils::encodeMap($__headers, "opcMeta", "", $opcMeta);
        }

        // set per-operation signing strategy
        HttpUtils::addToArray($__headers, getPerOperationSigningStrategyNameHeaderName(), (string) getSigningStrategy("exclude_body"));

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/o/{objectName}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{objectName}', utf8_encode($objectName), $__path);

        $__body = $putObjectBody;

        return $this->client->putAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'putObjectLifecyclePolicy':
    // path /n/{namespaceName}/b/{bucketName}/l
    public function putObjectLifecyclePolicyAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->putObjectLifecyclePolicyAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "putObjectLifecyclePolicyDetails", true),
            HttpUtils::orNull($params, "opcClientRequestId"),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "ifNoneMatch")
        );
    }

    public function putObjectLifecyclePolicyAsync_Helper(
        $namespaceName,
        $bucketName,
        $putObjectLifecyclePolicyDetails,
        $opcClientRequestId = null,
        $ifMatch = null,
        $ifNoneMatch = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }
        if ($ifMatch != null) {
            HttpUtils::addToArray($__headers, "ifMatch", HttpUtils::attemptEncodeParam($ifMatch));
        }
        if ($ifNoneMatch != null) {
            HttpUtils::addToArray($__headers, "ifNoneMatch", HttpUtils::attemptEncodeParam($ifNoneMatch));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/l";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__body = json_encode($putObjectLifecyclePolicyDetails);

        return $this->client->putAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'reencryptBucket':
    // path /n/{namespaceName}/b/{bucketName}/actions/reencrypt
    public function reencryptBucketAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->reencryptBucketAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function reencryptBucketAsync_Helper(
        $namespaceName,
        $bucketName,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/actions/reencrypt";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        return $this->client->postAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'reencryptObject':
    // path /n/{namespaceName}/b/{bucketName}/actions/reencrypt/{objectName}
    public function reencryptObjectAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->reencryptObjectAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "objectName", true),
            HttpUtils::orNull($params, "reencryptObjectDetails", true),
            HttpUtils::orNull($params, "versionId"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function reencryptObjectAsync_Helper(
        $namespaceName,
        $bucketName,
        $objectName,
        $reencryptObjectDetails,
        $versionId = null,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];
        if ($versionId != null) {
            HttpUtils::addToArray($__query, "versionId", HttpUtils::attemptEncodeParam($versionId));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/actions/reencrypt/{objectName}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{objectName}', utf8_encode($objectName), $__path);

        $__body = json_encode($reencryptObjectDetails);

        return $this->client->postAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'renameObject':
    // path /n/{namespaceName}/b/{bucketName}/actions/renameObject
    public function renameObjectAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->renameObjectAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "renameObjectDetails", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function renameObjectAsync_Helper(
        $namespaceName,
        $bucketName,
        $renameObjectDetails,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/actions/renameObject";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__body = json_encode($renameObjectDetails);

        return $this->client->postAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'restoreObjects':
    // path /n/{namespaceName}/b/{bucketName}/actions/restoreObjects
    public function restoreObjectsAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->restoreObjectsAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "restoreObjectsDetails", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function restoreObjectsAsync_Helper(
        $namespaceName,
        $bucketName,
        $restoreObjectsDetails,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/actions/restoreObjects";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__body = json_encode($restoreObjectsDetails);

        return $this->client->postAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'updateBucket':
    // path /n/{namespaceName}/b/{bucketName}/
    public function updateBucketAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->updateBucketAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "updateBucketDetails", true),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function updateBucketAsync_Helper(
        $namespaceName,
        $bucketName,
        $updateBucketDetails,
        $ifMatch = null,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($ifMatch != null) {
            HttpUtils::addToArray($__headers, "ifMatch", HttpUtils::attemptEncodeParam($ifMatch));
        }
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__body = json_encode($updateBucketDetails);

        return $this->client->postAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'updateNamespaceMetadata':
    // path /n/{namespaceName}
    public function updateNamespaceMetadataAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->updateNamespaceMetadataAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "updateNamespaceMetadataDetails", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function updateNamespaceMetadataAsync_Helper(
        $namespaceName,
        $updateNamespaceMetadataDetails,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);

        $__body = json_encode($updateNamespaceMetadataDetails);

        return $this->client->putAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'updateObjectStorageTier':
    // path /n/{namespaceName}/b/{bucketName}/actions/updateObjectStorageTier
    public function updateObjectStorageTierAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->updateObjectStorageTierAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "updateObjectStorageTierDetails", true),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function updateObjectStorageTierAsync_Helper(
        $namespaceName,
        $bucketName,
        $updateObjectStorageTierDetails,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/actions/updateObjectStorageTier";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);

        $__body = json_encode($updateObjectStorageTierDetails);

        return $this->client->postAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'updateRetentionRule':
    // path /n/{namespaceName}/b/{bucketName}/retentionRules/{retentionRuleId}
    public function updateRetentionRuleAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->updateRetentionRuleAsync_Helper(
            HttpUtils::orNull($params, "namespaceName", true),
            HttpUtils::orNull($params, "bucketName", true),
            HttpUtils::orNull($params, "retentionRuleId", true),
            HttpUtils::orNull($params, "updateRetentionRuleDetails", true),
            HttpUtils::orNull($params, "ifMatch"),
            HttpUtils::orNull($params, "opcClientRequestId")
        );
    }

    public function updateRetentionRuleAsync_Helper(
        $namespaceName,
        $bucketName,
        $retentionRuleId,
        $updateRetentionRuleDetails,
        $ifMatch = null,
        $opcClientRequestId = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($ifMatch != null) {
            HttpUtils::addToArray($__headers, "ifMatch", HttpUtils::attemptEncodeParam($ifMatch));
        }
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/retentionRules/{retentionRuleId}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{retentionRuleId}', utf8_encode($retentionRuleId), $__path);

        $__body = json_encode($updateRetentionRuleDetails);

        return $this->client->putAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }

    // Operation 'uploadPart':
    // path /n/{namespaceName}/b/{bucketName}/u/{objectName}
    public function uploadPartAsync($params=[])
    {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter to the operation should be an associative array");
        }

        return $this->uploadPartAsync_Helper(
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

    public function uploadPartAsync_Helper(
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
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        if ($opcClientRequestId != null) {
            HttpUtils::addToArray($__headers, "opcClientRequestId", HttpUtils::attemptEncodeParam($opcClientRequestId));
        }
        if ($ifMatch != null) {
            HttpUtils::addToArray($__headers, "ifMatch", HttpUtils::attemptEncodeParam($ifMatch));
        }
        if ($ifNoneMatch != null) {
            HttpUtils::addToArray($__headers, "ifNoneMatch", HttpUtils::attemptEncodeParam($ifNoneMatch));
        }
        if ($expect != null) {
            HttpUtils::addToArray($__headers, "expect", HttpUtils::attemptEncodeParam($expect));
        }
        if ($contentLength != null) {
            HttpUtils::addToArray($__headers, "contentLength", HttpUtils::attemptEncodeParam($contentLength));
        }
        if ($contentMD5 != null) {
            HttpUtils::addToArray($__headers, "contentMD5", HttpUtils::attemptEncodeParam($contentMD5));
        }
        if ($opcSseCustomerAlgorithm != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerAlgorithm", HttpUtils::attemptEncodeParam($opcSseCustomerAlgorithm));
        }
        if ($opcSseCustomerKey != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerKey", HttpUtils::attemptEncodeParam($opcSseCustomerKey));
        }
        if ($opcSseCustomerKeySha256 != null) {
            HttpUtils::addToArray($__headers, "opcSseCustomerKeySha256", HttpUtils::attemptEncodeParam($opcSseCustomerKeySha256));
        }
        if ($opcSseKmsKeyId != null) {
            HttpUtils::addToArray($__headers, "opcSseKmsKeyId", HttpUtils::attemptEncodeParam($opcSseKmsKeyId));
        }

        // set per-operation signing strategy
        HttpUtils::addToArray($__headers, getPerOperationSigningStrategyNameHeaderName(), (string) getSigningStrategy("exclude_body"));

        $__query = [];
        if ($uploadId != null) {
            HttpUtils::addToArray($__query, "uploadId", HttpUtils::attemptEncodeParam($uploadId));
        }
        if ($uploadPartNum != null) {
            HttpUtils::addToArray($__query, "uploadPartNum", HttpUtils::attemptEncodeParam($uploadPartNum));
        }

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/u/{objectName}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{objectName}', utf8_encode($objectName), $__path);

        $__body = $uploadPartBody;

        return $this->client->putAsync(
            "{$this->endpoint}{$__path}{$__queryStr}",
            [ 'headers' => $__headers, 'body' => $__body ]
        )->then(function ($__response) {
        return new OciResponse(
            $__response->getStatusCode(),
            $__response->getHeaders(),
            null,
            json_decode($__response->getBody())
        );
    });
    }
}