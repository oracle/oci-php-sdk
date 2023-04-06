<?php
/**Copyright (c) 2023, Oracle and/or its affiliates. All rights reserved.
 * This software is dual-licensed to you under the Universal Permissive License
 * (UPL) 1.0 as shown at https://oss.oracle.com/licenses/upl or Apache License
 * 2.0 as shown at http://www.apache.org/licenses/LICENSE-2.0. You may choose
 * either license.
*/

// Generated using OracleSDKGenerator, API Version: 20160918

namespace Oracle\Oci\ObjectStorage;

use InvalidArgumentException;
use BadMethodCallException;
use Oracle\Oci\Common\Auth\AuthProviderInterface;
use Oracle\Oci\Common\HttpUtils;
use Oracle\Oci\Common\UserAgent;
use Oracle\Oci\Common\AbstractClient;
use Oracle\Oci\Common\Constants;
use Oracle\Oci\Common\SigningStrategies;

class ObjectStorageAsyncClient extends AbstractClient
{
    const ENDPOINT_TEMPLATE = "https://objectstorage.{region}.{secondLevelDomain}";

    public function __construct(
        AuthProviderInterface $auth_provider,
        $region=null,
        $endpoint=null
    ) {
        parent::__construct(
            ObjectStorageAsyncClient::ENDPOINT_TEMPLATE,
            $auth_provider,
            SigningStrategies::get("standard"),
            $region,
            $endpoint
        );
        $this->setMultiCurlHandler();
    }


    // Should have waiters.

    // Operation 'abortMultipartUpload':
    // path /n/{namespaceName}/b/{bucketName}/u/{objectName}

    /**
     * Aborts an in-progress multipart upload and deletes all parts that have been uploaded.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for abortMultipartUpload:
     * Aborts an in-progress multipart upload and deletes all parts that have been uploaded.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param string $objectName required parameter
     * @param string $uploadId required parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("DELETE", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'cancelWorkRequest':
    // path /workRequests/{workRequestId}

    /**
     * Cancels a work request.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for cancelWorkRequest:
     * Cancels a work request.
     * @param string $workRequestId required parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("DELETE", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'commitMultipartUpload':
    // path /n/{namespaceName}/b/{bucketName}/u/{objectName}

    /**
     * Commits a multipart upload, which involves checking part numbers and entity tags (ETags) of the parts, to create an aggregate object.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for commitMultipartUpload:
     * Commits a multipart upload, which involves checking part numbers and entity tags (ETags) of the parts, to create an aggregate object.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param string $objectName required parameter
     * @param string $uploadId required parameter
     * @param mixed $commitMultipartUploadDetails required parameter
     * @param string|null $ifMatch optional parameter
     * @param string|null $ifNoneMatch optional parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("POST", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers, 'body' => $__body ]);
    }
    // Operation 'copyObject':
    // path /n/{namespaceName}/b/{bucketName}/actions/copyObject

    /**
     * Creates a request to copy an object within a region or to another region.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for copyObject:
     * Creates a request to copy an object within a region or to another region.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param mixed $copyObjectDetails required parameter
     * @param string|null $opcClientRequestId optional parameter
     * @param string|null $opcSseCustomerAlgorithm optional parameter
     * @param string|null $opcSseCustomerKey optional parameter
     * @param string|null $opcSseCustomerKeySha256 optional parameter
     * @param string|null $opcSourceSseCustomerAlgorithm optional parameter
     * @param string|null $opcSourceSseCustomerKey optional parameter
     * @param string|null $opcSourceSseCustomerKeySha256 optional parameter
     * @param string|null $opcSseKmsKeyId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("POST", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers, 'body' => $__body ]);
    }
    // Operation 'createBucket':
    // path /n/{namespaceName}/b/

    /**
     * Creates a bucket in the given namespace with a bucket name and optional user-defined metadata. Avoid entering confidential information in bucket names.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for createBucket:
     * Creates a bucket in the given namespace with a bucket name and optional user-defined metadata. Avoid entering confidential information in bucket names.
     * @param string $namespaceName required parameter
     * @param mixed $createBucketDetails required parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("POST", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers, 'body' => $__body ]);
    }
    // Operation 'createMultipartUpload':
    // path /n/{namespaceName}/b/{bucketName}/u

    /**
     * Starts a new multipart upload to a specific object in the given bucket in the given namespace.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for createMultipartUpload:
     * Starts a new multipart upload to a specific object in the given bucket in the given namespace.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param mixed $createMultipartUploadDetails required parameter
     * @param string|null $ifMatch optional parameter
     * @param string|null $ifNoneMatch optional parameter
     * @param string|null $opcClientRequestId optional parameter
     * @param string|null $opcSseCustomerAlgorithm optional parameter
     * @param string|null $opcSseCustomerKey optional parameter
     * @param string|null $opcSseCustomerKeySha256 optional parameter
     * @param string|null $opcSseKmsKeyId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("POST", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers, 'body' => $__body ]);
    }
    // Operation 'createPreauthenticatedRequest':
    // path /n/{namespaceName}/b/{bucketName}/p/

    /**
     * Creates a pre-authenticated request specific to the bucket.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for createPreauthenticatedRequest:
     * Creates a pre-authenticated request specific to the bucket.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param mixed $createPreauthenticatedRequestDetails required parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("POST", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers, 'body' => $__body ]);
    }
    // Operation 'createReplicationPolicy':
    // path /n/{namespaceName}/b/{bucketName}/replicationPolicies

    /**
     * Creates a replication policy for the specified bucket.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for createReplicationPolicy:
     * Creates a replication policy for the specified bucket.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param mixed $createReplicationPolicyDetails required parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("POST", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers, 'body' => $__body ]);
    }
    // Operation 'createRetentionRule':
    // path /n/{namespaceName}/b/{bucketName}/retentionRules

    /**
     * Creates a new retention rule in the specified bucket. The new rule will take effect typically within 30 seconds. Note that a maximum of 100 rules are supported on a bucket.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for createRetentionRule:
     * Creates a new retention rule in the specified bucket. The new rule will take effect typically within 30 seconds. Note that a maximum of 100 rules are supported on a bucket.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param mixed $createRetentionRuleDetails required parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("POST", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers, 'body' => $__body ]);
    }
    // Operation 'deleteBucket':
    // path /n/{namespaceName}/b/{bucketName}/

    /**
     * Deletes a bucket if the bucket is already empty. If the bucket is not empty, use Deletes a bucket if the bucket is already empty. If the bucket is not empty, use [DeleteObject](#/en/objectstorage/20160918/methods/DeleteObject) first. In addition, you cannot delete a bucket that has a multipart upload in progress or a pre-authenticated request associated with that bucket.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for deleteBucket:
     * Deletes a bucket if the bucket is already empty. If the bucket is not empty, use Deletes a bucket if the bucket is already empty. If the bucket is not empty, use [DeleteObject](#/en/objectstorage/20160918/methods/DeleteObject) first. In addition, you cannot delete a bucket that has a multipart upload in progress or a pre-authenticated request associated with that bucket.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param string|null $ifMatch optional parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("DELETE", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'deleteObject':
    // path /n/{namespaceName}/b/{bucketName}/o/{objectName}

    /**
     * Deletes an object.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for deleteObject:
     * Deletes an object.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param string $objectName required parameter
     * @param string|null $ifMatch optional parameter
     * @param string|null $opcClientRequestId optional parameter
     * @param string|null $versionId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("DELETE", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'deleteObjectLifecyclePolicy':
    // path /n/{namespaceName}/b/{bucketName}/l

    /**
     * Deletes the object lifecycle policy for the bucket.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for deleteObjectLifecyclePolicy:
     * Deletes the object lifecycle policy for the bucket.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param string|null $opcClientRequestId optional parameter
     * @param string|null $ifMatch optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("DELETE", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'deletePreauthenticatedRequest':
    // path /n/{namespaceName}/b/{bucketName}/p/{parId}

    /**
     * Deletes the pre-authenticated request for the bucket.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for deletePreauthenticatedRequest:
     * Deletes the pre-authenticated request for the bucket.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param string $parId required parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("DELETE", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'deleteReplicationPolicy':
    // path /n/{namespaceName}/b/{bucketName}/replicationPolicies/{replicationId}

    /**
     * Deletes the replication policy associated with the source bucket.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for deleteReplicationPolicy:
     * Deletes the replication policy associated with the source bucket.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param string $replicationId required parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("DELETE", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'deleteRetentionRule':
    // path /n/{namespaceName}/b/{bucketName}/retentionRules/{retentionRuleId}

    /**
     * Deletes the specified rule. The deletion takes effect typically within 30 seconds.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for deleteRetentionRule:
     * Deletes the specified rule. The deletion takes effect typically within 30 seconds.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param string $retentionRuleId required parameter
     * @param string|null $ifMatch optional parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("DELETE", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'getBucket':
    // path /n/{namespaceName}/b/{bucketName}/

    /**
     * Gets the current representation of the given bucket in the given Object Storage namespace.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for getBucket:
     * Gets the current representation of the given bucket in the given Object Storage namespace.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param string|null $ifMatch optional parameter
     * @param string|null $ifNoneMatch optional parameter
     * @param string|null $opcClientRequestId optional parameter
     * @param array|null $fields optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("GET", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'getNamespace':
    // path /n/

    /**
     * Each Oracle Cloud Infrastructure tenant is assigned one unique and uneditable Object Storage namespace. The namespace is a system-generated string assigned during account creation. For some older tenancies, the namespace string may be the tenancy name in all lower-case letters. You cannot edit a namespace.  GetNamespace returns the name of the Object Storage namespace for the user making the request. If an optional compartmentId query parameter is provided, GetNamespace returns the namespace name of the corresponding tenancy, provided the user has access to it.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for getNamespace:
     * Each Oracle Cloud Infrastructure tenant is assigned one unique and uneditable Object Storage namespace. The namespace is a system-generated string assigned during account creation. For some older tenancies, the namespace string may be the tenancy name in all lower-case letters. You cannot edit a namespace.  GetNamespace returns the name of the Object Storage namespace for the user making the request. If an optional compartmentId query parameter is provided, GetNamespace returns the namespace name of the corresponding tenancy, provided the user has access to it.
     * @param string|null $opcClientRequestId optional parameter
     * @param string|null $compartmentId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("GET", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'getNamespaceMetadata':
    // path /n/{namespaceName}

    /**
     * Gets the metadata for the Object Storage namespace, which contains defaultS3CompartmentId and defaultSwiftCompartmentId.  Any user with the OBJECTSTORAGE_NAMESPACE_READ permission will be able to see the current metadata. If you are not authorized, talk to an administrator. If you are an administrator who needs to write policies to give users access, see Gets the metadata for the Object Storage namespace, which contains defaultS3CompartmentId and defaultSwiftCompartmentId.  Any user with the OBJECTSTORAGE_NAMESPACE_READ permission will be able to see the current metadata. If you are not authorized, talk to an administrator. If you are an administrator who needs to write policies to give users access, see [Getting Started with Policies](/Content/Identity/Concepts/policygetstarted.htm).
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for getNamespaceMetadata:
     * Gets the metadata for the Object Storage namespace, which contains defaultS3CompartmentId and defaultSwiftCompartmentId.  Any user with the OBJECTSTORAGE_NAMESPACE_READ permission will be able to see the current metadata. If you are not authorized, talk to an administrator. If you are an administrator who needs to write policies to give users access, see Gets the metadata for the Object Storage namespace, which contains defaultS3CompartmentId and defaultSwiftCompartmentId.  Any user with the OBJECTSTORAGE_NAMESPACE_READ permission will be able to see the current metadata. If you are not authorized, talk to an administrator. If you are an administrator who needs to write policies to give users access, see [Getting Started with Policies](/Content/Identity/Concepts/policygetstarted.htm).
     * @param string $namespaceName required parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("GET", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'getObject':
    // path /n/{namespaceName}/b/{bucketName}/o/{objectName}

    /**
     * Gets the metadata and body of an object.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for getObject:
     * Gets the metadata and body of an object.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param string $objectName required parameter
     * @param string|null $versionId optional parameter
     * @param string|null $ifMatch optional parameter
     * @param string|null $ifNoneMatch optional parameter
     * @param string|null $opcClientRequestId optional parameter
     * @param \Oracle\Oci\Common\Range|null $range optional parameter
     * @param string|null $opcSseCustomerAlgorithm optional parameter
     * @param string|null $opcSseCustomerKey optional parameter
     * @param string|null $opcSseCustomerKeySha256 optional parameter
     * @param string|null $httpResponseContentDisposition optional parameter
     * @param string|null $httpResponseCacheControl optional parameter
     * @param string|null $httpResponseContentType optional parameter
     * @param string|null $httpResponseContentLanguage optional parameter
     * @param string|null $httpResponseContentEncoding optional parameter
     * @param string|null $httpResponseExpires optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("GET", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers, 'response_body_type' => 'binary' ]);
    }
    // Operation 'getObjectLifecyclePolicy':
    // path /n/{namespaceName}/b/{bucketName}/l

    /**
     * Gets the object lifecycle policy for the bucket.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for getObjectLifecyclePolicy:
     * Gets the object lifecycle policy for the bucket.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("GET", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'getPreauthenticatedRequest':
    // path /n/{namespaceName}/b/{bucketName}/p/{parId}

    /**
     * Gets the pre-authenticated request for the bucket.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for getPreauthenticatedRequest:
     * Gets the pre-authenticated request for the bucket.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param string $parId required parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("GET", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'getReplicationPolicy':
    // path /n/{namespaceName}/b/{bucketName}/replicationPolicies/{replicationId}

    /**
     * Get the replication policy.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for getReplicationPolicy:
     * Get the replication policy.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param string $replicationId required parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("GET", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'getRetentionRule':
    // path /n/{namespaceName}/b/{bucketName}/retentionRules/{retentionRuleId}

    /**
     * Get the specified retention rule.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for getRetentionRule:
     * Get the specified retention rule.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param string $retentionRuleId required parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("GET", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'getWorkRequest':
    // path /workRequests/{workRequestId}

    /**
     * Gets the status of the work request for the given ID.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for getWorkRequest:
     * Gets the status of the work request for the given ID.
     * @param string $workRequestId required parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("GET", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'headBucket':
    // path /n/{namespaceName}/b/{bucketName}/

    /**
     * Efficiently checks to see if a bucket exists and gets the current entity tag (ETag) for the bucket.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for headBucket:
     * Efficiently checks to see if a bucket exists and gets the current entity tag (ETag) for the bucket.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param string|null $ifMatch optional parameter
     * @param string|null $ifNoneMatch optional parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("HEAD", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'headObject':
    // path /n/{namespaceName}/b/{bucketName}/o/{objectName}

    /**
     * Gets the user-defined metadata and entity tag (ETag) for an object.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for headObject:
     * Gets the user-defined metadata and entity tag (ETag) for an object.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param string $objectName required parameter
     * @param string|null $versionId optional parameter
     * @param string|null $ifMatch optional parameter
     * @param string|null $ifNoneMatch optional parameter
     * @param string|null $opcClientRequestId optional parameter
     * @param string|null $opcSseCustomerAlgorithm optional parameter
     * @param string|null $opcSseCustomerKey optional parameter
     * @param string|null $opcSseCustomerKeySha256 optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("HEAD", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'listBuckets':
    // path /n/{namespaceName}/b/

    /**
     * Gets a list of all BucketSummary items in a compartment. A BucketSummary contains only summary fields for the bucket and does not contain fields like the user-defined metadata.  ListBuckets returns a BucketSummary containing at most 1000 buckets. To paginate through more buckets, use the returned `opc-next-page` value with the `page` request parameter.  To use this and other API operations, you must be authorized in an IAM policy. If you are not authorized, talk to an administrator. If you are an administrator who needs to write policies to give users access, see Gets a list of all BucketSummary items in a compartment. A BucketSummary contains only summary fields for the bucket and does not contain fields like the user-defined metadata.  ListBuckets returns a BucketSummary containing at most 1000 buckets. To paginate through more buckets, use the returned `opc-next-page` value with the `page` request parameter.  To use this and other API operations, you must be authorized in an IAM policy. If you are not authorized, talk to an administrator. If you are an administrator who needs to write policies to give users access, see [Getting Started with Policies](/Content/Identity/Concepts/policygetstarted.htm).
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for listBuckets:
     * Gets a list of all BucketSummary items in a compartment. A BucketSummary contains only summary fields for the bucket and does not contain fields like the user-defined metadata.  ListBuckets returns a BucketSummary containing at most 1000 buckets. To paginate through more buckets, use the returned `opc-next-page` value with the `page` request parameter.  To use this and other API operations, you must be authorized in an IAM policy. If you are not authorized, talk to an administrator. If you are an administrator who needs to write policies to give users access, see Gets a list of all BucketSummary items in a compartment. A BucketSummary contains only summary fields for the bucket and does not contain fields like the user-defined metadata.  ListBuckets returns a BucketSummary containing at most 1000 buckets. To paginate through more buckets, use the returned `opc-next-page` value with the `page` request parameter.  To use this and other API operations, you must be authorized in an IAM policy. If you are not authorized, talk to an administrator. If you are an administrator who needs to write policies to give users access, see [Getting Started with Policies](/Content/Identity/Concepts/policygetstarted.htm).
     * @param string $namespaceName required parameter
     * @param string $compartmentId required parameter
     * @param int|null $limit optional parameter
     * @param string|null $page optional parameter
     * @param array|null $fields optional parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("GET", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'listMultipartUploadParts':
    // path /n/{namespaceName}/b/{bucketName}/u/{objectName}

    /**
     * Lists the parts of an in-progress multipart upload.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for listMultipartUploadParts:
     * Lists the parts of an in-progress multipart upload.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param string $objectName required parameter
     * @param string $uploadId required parameter
     * @param int|null $limit optional parameter
     * @param string|null $page optional parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("GET", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'listMultipartUploads':
    // path /n/{namespaceName}/b/{bucketName}/u

    /**
     * Lists all of the in-progress multipart uploads for the given bucket in the given Object Storage namespace.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for listMultipartUploads:
     * Lists all of the in-progress multipart uploads for the given bucket in the given Object Storage namespace.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param int|null $limit optional parameter
     * @param string|null $page optional parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("GET", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'listObjectVersions':
    // path /n/{namespaceName}/b/{bucketName}/objectversions

    /**
     * Lists the object versions in a bucket.  ListObjectVersions returns an ObjectVersionCollection containing at most 1000 object versions. To paginate through more object versions, use the returned `opc-next-page` value with the `page` request parameter.  To use this and other API operations, you must be authorized in an IAM policy. If you are not authorized, talk to an administrator. If you are an administrator who needs to write policies to give users access, see Lists the object versions in a bucket.  ListObjectVersions returns an ObjectVersionCollection containing at most 1000 object versions. To paginate through more object versions, use the returned `opc-next-page` value with the `page` request parameter.  To use this and other API operations, you must be authorized in an IAM policy. If you are not authorized, talk to an administrator. If you are an administrator who needs to write policies to give users access, see [Getting Started with Policies](/Content/Identity/Concepts/policygetstarted.htm).
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for listObjectVersions:
     * Lists the object versions in a bucket.  ListObjectVersions returns an ObjectVersionCollection containing at most 1000 object versions. To paginate through more object versions, use the returned `opc-next-page` value with the `page` request parameter.  To use this and other API operations, you must be authorized in an IAM policy. If you are not authorized, talk to an administrator. If you are an administrator who needs to write policies to give users access, see Lists the object versions in a bucket.  ListObjectVersions returns an ObjectVersionCollection containing at most 1000 object versions. To paginate through more object versions, use the returned `opc-next-page` value with the `page` request parameter.  To use this and other API operations, you must be authorized in an IAM policy. If you are not authorized, talk to an administrator. If you are an administrator who needs to write policies to give users access, see [Getting Started with Policies](/Content/Identity/Concepts/policygetstarted.htm).
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param string|null $prefix optional parameter
     * @param string|null $start optional parameter
     * @param string|null $end optional parameter
     * @param int|null $limit optional parameter
     * @param string|null $delimiter optional parameter
     * @param string|null $fields optional parameter
     * @param string|null $opcClientRequestId optional parameter
     * @param string|null $startAfter optional parameter
     * @param string|null $page optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("GET", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'listObjects':
    // path /n/{namespaceName}/b/{bucketName}/o

    /**
     * Lists the objects in a bucket. By default, ListObjects returns object names only. See the `fields` parameter for other fields that you can optionally include in ListObjects response.  ListObjects returns at most 1000 objects. To paginate through more objects, use the returned 'nextStartWith' value with the 'start' parameter. To filter which objects ListObjects returns, use the 'start' and 'end' parameters.  To use this and other API operations, you must be authorized in an IAM policy. If you are not authorized, talk to an administrator. If you are an administrator who needs to write policies to give users access, see Lists the objects in a bucket. By default, ListObjects returns object names only. See the `fields` parameter for other fields that you can optionally include in ListObjects response.  ListObjects returns at most 1000 objects. To paginate through more objects, use the returned 'nextStartWith' value with the 'start' parameter. To filter which objects ListObjects returns, use the 'start' and 'end' parameters.  To use this and other API operations, you must be authorized in an IAM policy. If you are not authorized, talk to an administrator. If you are an administrator who needs to write policies to give users access, see [Getting Started with Policies](/Content/Identity/Concepts/policygetstarted.htm).
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for listObjects:
     * Lists the objects in a bucket. By default, ListObjects returns object names only. See the `fields` parameter for other fields that you can optionally include in ListObjects response.  ListObjects returns at most 1000 objects. To paginate through more objects, use the returned 'nextStartWith' value with the 'start' parameter. To filter which objects ListObjects returns, use the 'start' and 'end' parameters.  To use this and other API operations, you must be authorized in an IAM policy. If you are not authorized, talk to an administrator. If you are an administrator who needs to write policies to give users access, see Lists the objects in a bucket. By default, ListObjects returns object names only. See the `fields` parameter for other fields that you can optionally include in ListObjects response.  ListObjects returns at most 1000 objects. To paginate through more objects, use the returned 'nextStartWith' value with the 'start' parameter. To filter which objects ListObjects returns, use the 'start' and 'end' parameters.  To use this and other API operations, you must be authorized in an IAM policy. If you are not authorized, talk to an administrator. If you are an administrator who needs to write policies to give users access, see [Getting Started with Policies](/Content/Identity/Concepts/policygetstarted.htm).
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param string|null $prefix optional parameter
     * @param string|null $start optional parameter
     * @param string|null $end optional parameter
     * @param int|null $limit optional parameter
     * @param string|null $delimiter optional parameter
     * @param string|null $fields optional parameter
     * @param string|null $opcClientRequestId optional parameter
     * @param string|null $startAfter optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("GET", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'listPreauthenticatedRequests':
    // path /n/{namespaceName}/b/{bucketName}/p/

    /**
     * Lists pre-authenticated requests for the bucket.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for listPreauthenticatedRequests:
     * Lists pre-authenticated requests for the bucket.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param string|null $objectNamePrefix optional parameter
     * @param int|null $limit optional parameter
     * @param string|null $page optional parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("GET", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'listReplicationPolicies':
    // path /n/{namespaceName}/b/{bucketName}/replicationPolicies

    /**
     * List the replication policies associated with a bucket.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for listReplicationPolicies:
     * List the replication policies associated with a bucket.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param string|null $opcClientRequestId optional parameter
     * @param string|null $page optional parameter
     * @param int|null $limit optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("GET", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'listReplicationSources':
    // path /n/{namespaceName}/b/{bucketName}/replicationSources

    /**
     * List the replication sources of a destination bucket.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for listReplicationSources:
     * List the replication sources of a destination bucket.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param string|null $opcClientRequestId optional parameter
     * @param string|null $page optional parameter
     * @param int|null $limit optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("GET", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'listRetentionRules':
    // path /n/{namespaceName}/b/{bucketName}/retentionRules

    /**
     * List the retention rules for a bucket. The retention rules are sorted based on creation time, with the most recently created retention rule returned first.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for listRetentionRules:
     * List the retention rules for a bucket. The retention rules are sorted based on creation time, with the most recently created retention rule returned first.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param string|null $page optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("GET", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'listWorkRequestErrors':
    // path /workRequests/{workRequestId}/errors

    /**
     * Lists the errors of the work request with the given ID.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for listWorkRequestErrors:
     * Lists the errors of the work request with the given ID.
     * @param string $workRequestId required parameter
     * @param string|null $page optional parameter
     * @param int|null $limit optional parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("GET", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'listWorkRequestLogs':
    // path /workRequests/{workRequestId}/logs

    /**
     * Lists the logs of the work request with the given ID.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for listWorkRequestLogs:
     * Lists the logs of the work request with the given ID.
     * @param string $workRequestId required parameter
     * @param string|null $page optional parameter
     * @param int|null $limit optional parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("GET", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'listWorkRequests':
    // path /workRequests

    /**
     * Lists the work requests in a compartment.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for listWorkRequests:
     * Lists the work requests in a compartment.
     * @param string $compartmentId required parameter
     * @param string|null $opcClientRequestId optional parameter
     * @param string|null $page optional parameter
     * @param int|null $limit optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("GET", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'makeBucketWritable':
    // path /n/{namespaceName}/b/{bucketName}/actions/makeBucketWritable

    /**
     * Stops replication to the destination bucket and removes the replication policy. When the replication policy was created, this destination bucket became read-only except for new and changed objects replicated automatically from the source bucket. MakeBucketWritable removes the replication policy. This bucket is no longer the target for replication and is now writable, allowing users to make changes to bucket contents.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for makeBucketWritable:
     * Stops replication to the destination bucket and removes the replication policy. When the replication policy was created, this destination bucket became read-only except for new and changed objects replicated automatically from the source bucket. MakeBucketWritable removes the replication policy. This bucket is no longer the target for replication and is now writable, allowing users to make changes to bucket contents.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("POST", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'putObject':
    // path /n/{namespaceName}/b/{bucketName}/o/{objectName}

    /**
     * Creates a new object or overwrites an existing object with the same name. The maximum object size allowed by PutObject is 50 GiB.  See Creates a new object or overwrites an existing object with the same name. The maximum object size allowed by PutObject is 50 GiB.  See [Object Names](/Content/Object/Tasks/managingobjects.htm#namerequirements) for object naming requirements.   See  for object naming requirements.   See [Special Instructions for Object Storage PUT](/Content/API/Concepts/signingrequests.htm#ObjectStoragePut) for request signature requirements.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for putObject:
     * Creates a new object or overwrites an existing object with the same name. The maximum object size allowed by PutObject is 50 GiB.  See Creates a new object or overwrites an existing object with the same name. The maximum object size allowed by PutObject is 50 GiB.  See [Object Names](/Content/Object/Tasks/managingobjects.htm#namerequirements) for object naming requirements.   See  for object naming requirements.   See [Special Instructions for Object Storage PUT](/Content/API/Concepts/signingrequests.htm#ObjectStoragePut) for request signature requirements.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param string $objectName required parameter
     * @param string $putObjectBody required parameter
     * @param int|null $contentLength optional parameter
     * @param string|null $ifMatch optional parameter
     * @param string|null $ifNoneMatch optional parameter
     * @param string|null $opcClientRequestId optional parameter
     * @param string|null $expect optional parameter
     * @param string|null $contentMD5 optional parameter
     * @param string|null $contentType optional parameter
     * @param string|null $contentLanguage optional parameter
     * @param string|null $contentEncoding optional parameter
     * @param string|null $contentDisposition optional parameter
     * @param string|null $cacheControl optional parameter
     * @param string|null $opcSseCustomerAlgorithm optional parameter
     * @param string|null $opcSseCustomerKey optional parameter
     * @param string|null $opcSseCustomerKeySha256 optional parameter
     * @param string|null $opcSseKmsKeyId optional parameter
     * @param string|null $storageTier optional parameter
     * @param array|null $opcMeta optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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
            HttpUtils::encodeMap($__headers, "", "opc-meta-", $opcMeta);
        }

        // set per-operation signing strategy
        HttpUtils::addToArray($__headers, Constants::PER_OPERATION_SIGNING_STRATEGY_NAME_HEADER_NAME, (string) SigningStrategies::get("exclude_body"));

        $__query = [];

        $__queryStr = HttpUtils::queryMapToString($__query);

        $__path = "/n/{namespaceName}/b/{bucketName}/o/{objectName}";
        $__path = str_replace('{namespaceName}', utf8_encode($namespaceName), $__path);
        $__path = str_replace('{bucketName}', utf8_encode($bucketName), $__path);
        $__path = str_replace('{objectName}', utf8_encode($objectName), $__path);

        $__body = $putObjectBody;

        return $this->callApiAsync("PUT", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers, 'body' => $__body ]);
    }
    // Operation 'putObjectLifecyclePolicy':
    // path /n/{namespaceName}/b/{bucketName}/l

    /**
     * Creates or replaces the object lifecycle policy for the bucket.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for putObjectLifecyclePolicy:
     * Creates or replaces the object lifecycle policy for the bucket.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param mixed $putObjectLifecyclePolicyDetails required parameter
     * @param string|null $opcClientRequestId optional parameter
     * @param string|null $ifMatch optional parameter
     * @param string|null $ifNoneMatch optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("PUT", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers, 'body' => $__body ]);
    }
    // Operation 'reencryptBucket':
    // path /n/{namespaceName}/b/{bucketName}/actions/reencrypt

    /**
     * Re-encrypts the unique data encryption key that encrypts each object written to the bucket by using the most recent  version of the master encryption key assigned to the bucket. (All data encryption keys are encrypted by a master  encryption key. Master encryption keys are assigned to buckets and managed by Oracle by default, but you can assign  a key that you created and control through the Oracle Cloud Infrastructure Key Management service.) The kmsKeyId property  of the bucket determines which master encryption key is assigned to the bucket. If you assigned a different Key Management  master encryption key to the bucket, you can call this API to re-encrypt all data encryption keys with the newly  assigned key. Similarly, you might want to re-encrypt all data encryption keys if the assigned key has been rotated to  a new key version since objects were last added to the bucket. If you call this API and there is no kmsKeyId associated  with the bucket, the call will fail.  Calling this API starts a work request task to re-encrypt the data encryption key of all objects in the bucket. Only  objects created before the time of the API call will be re-encrypted. The call can take a long time, depending on how many  objects are in the bucket and how big they are. This API returns a work request ID that you can use to retrieve the status  of the work request task. All the versions of objects will be re-encrypted whether versioning is enabled or suspended at the bucket.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for reencryptBucket:
     * Re-encrypts the unique data encryption key that encrypts each object written to the bucket by using the most recent  version of the master encryption key assigned to the bucket. (All data encryption keys are encrypted by a master  encryption key. Master encryption keys are assigned to buckets and managed by Oracle by default, but you can assign  a key that you created and control through the Oracle Cloud Infrastructure Key Management service.) The kmsKeyId property  of the bucket determines which master encryption key is assigned to the bucket. If you assigned a different Key Management  master encryption key to the bucket, you can call this API to re-encrypt all data encryption keys with the newly  assigned key. Similarly, you might want to re-encrypt all data encryption keys if the assigned key has been rotated to  a new key version since objects were last added to the bucket. If you call this API and there is no kmsKeyId associated  with the bucket, the call will fail.  Calling this API starts a work request task to re-encrypt the data encryption key of all objects in the bucket. Only  objects created before the time of the API call will be re-encrypted. The call can take a long time, depending on how many  objects are in the bucket and how big they are. This API returns a work request ID that you can use to retrieve the status  of the work request task. All the versions of objects will be re-encrypted whether versioning is enabled or suspended at the bucket.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("POST", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers ]);
    }
    // Operation 'reencryptObject':
    // path /n/{namespaceName}/b/{bucketName}/actions/reencrypt/{objectName}

    /**
     * Re-encrypts the data encryption keys that encrypt the object and its chunks. By default, when you create a bucket, the Object Storage service manages the master encryption key used to encrypt each object's data encryption keys. The encryption mechanism that you specify for the bucket applies to the objects it contains.  You can alternatively employ one of these encryption strategies for an object:  - You can assign a key that you created and control through the Oracle Cloud Infrastructure Vault service.  - You can encrypt an object using your own encryption key. The key you supply is known as a customer-provided encryption key (SSE-C).
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for reencryptObject:
     * Re-encrypts the data encryption keys that encrypt the object and its chunks. By default, when you create a bucket, the Object Storage service manages the master encryption key used to encrypt each object's data encryption keys. The encryption mechanism that you specify for the bucket applies to the objects it contains.  You can alternatively employ one of these encryption strategies for an object:  - You can assign a key that you created and control through the Oracle Cloud Infrastructure Vault service.  - You can encrypt an object using your own encryption key. The key you supply is known as a customer-provided encryption key (SSE-C).
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param string $objectName required parameter
     * @param mixed $reencryptObjectDetails required parameter
     * @param string|null $versionId optional parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("POST", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers, 'body' => $__body ]);
    }
    // Operation 'renameObject':
    // path /n/{namespaceName}/b/{bucketName}/actions/renameObject

    /**
     * Rename an object in the given Object Storage namespace.  See Rename an object in the given Object Storage namespace.  See [Object Names](/Content/Object/Tasks/managingobjects.htm#namerequirements) for object naming requirements.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for renameObject:
     * Rename an object in the given Object Storage namespace.  See Rename an object in the given Object Storage namespace.  See [Object Names](/Content/Object/Tasks/managingobjects.htm#namerequirements) for object naming requirements.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param mixed $renameObjectDetails required parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("POST", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers, 'body' => $__body ]);
    }
    // Operation 'restoreObjects':
    // path /n/{namespaceName}/b/{bucketName}/actions/restoreObjects

    /**
     * Restores one or more objects specified by the objectName parameter. By default objects will be restored for 24 hours. Duration can be configured using the hours parameter.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for restoreObjects:
     * Restores one or more objects specified by the objectName parameter. By default objects will be restored for 24 hours. Duration can be configured using the hours parameter.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param mixed $restoreObjectsDetails required parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("POST", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers, 'body' => $__body ]);
    }
    // Operation 'updateBucket':
    // path /n/{namespaceName}/b/{bucketName}/

    /**
     * Performs a partial or full update of a bucket's user-defined metadata.  Use UpdateBucket to move a bucket from one compartment to another within the same tenancy. Supply the compartmentID of the compartment that you want to move the bucket to. For more information about moving resources between compartments, see Performs a partial or full update of a bucket's user-defined metadata.  Use UpdateBucket to move a bucket from one compartment to another within the same tenancy. Supply the compartmentID of the compartment that you want to move the bucket to. For more information about moving resources between compartments, see [Moving Resources to a Different Compartment](/iaas/Content/Identity/Tasks/managingcompartments.htm#moveRes).
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for updateBucket:
     * Performs a partial or full update of a bucket's user-defined metadata.  Use UpdateBucket to move a bucket from one compartment to another within the same tenancy. Supply the compartmentID of the compartment that you want to move the bucket to. For more information about moving resources between compartments, see Performs a partial or full update of a bucket's user-defined metadata.  Use UpdateBucket to move a bucket from one compartment to another within the same tenancy. Supply the compartmentID of the compartment that you want to move the bucket to. For more information about moving resources between compartments, see [Moving Resources to a Different Compartment](/iaas/Content/Identity/Tasks/managingcompartments.htm#moveRes).
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param mixed $updateBucketDetails required parameter
     * @param string|null $ifMatch optional parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("POST", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers, 'body' => $__body ]);
    }
    // Operation 'updateNamespaceMetadata':
    // path /n/{namespaceName}

    /**
     * By default, buckets created using the Amazon S3 Compatibility API or the Swift API are created in the root compartment of the Oracle Cloud Infrastructure tenancy.  You can change the default Swift/Amazon S3 compartmentId designation to a different compartmentId. All subsequent bucket creations will use the new default compartment, but no previously created buckets will be modified. A user must have OBJECTSTORAGE_NAMESPACE_UPDATE permission to make changes to the default compartments for Amazon S3 and Swift.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for updateNamespaceMetadata:
     * By default, buckets created using the Amazon S3 Compatibility API or the Swift API are created in the root compartment of the Oracle Cloud Infrastructure tenancy.  You can change the default Swift/Amazon S3 compartmentId designation to a different compartmentId. All subsequent bucket creations will use the new default compartment, but no previously created buckets will be modified. A user must have OBJECTSTORAGE_NAMESPACE_UPDATE permission to make changes to the default compartments for Amazon S3 and Swift.
     * @param string $namespaceName required parameter
     * @param mixed $updateNamespaceMetadataDetails required parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("PUT", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers, 'body' => $__body ]);
    }
    // Operation 'updateObjectStorageTier':
    // path /n/{namespaceName}/b/{bucketName}/actions/updateObjectStorageTier

    /**
     * Changes the storage tier of the object specified by the objectName parameter.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for updateObjectStorageTier:
     * Changes the storage tier of the object specified by the objectName parameter.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param mixed $updateObjectStorageTierDetails required parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("POST", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers, 'body' => $__body ]);
    }
    // Operation 'updateRetentionRule':
    // path /n/{namespaceName}/b/{bucketName}/retentionRules/{retentionRuleId}

    /**
     * Updates the specified retention rule. Rule changes take effect typically within 30 seconds.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for updateRetentionRule:
     * Updates the specified retention rule. Rule changes take effect typically within 30 seconds.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param string $retentionRuleId required parameter
     * @param mixed $updateRetentionRuleDetails required parameter
     * @param string|null $ifMatch optional parameter
     * @param string|null $opcClientRequestId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

        return $this->callApiAsync("PUT", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers, 'body' => $__body ]);
    }
    // Operation 'uploadPart':
    // path /n/{namespaceName}/b/{bucketName}/u/{objectName}

    /**
     * Uploads a single part of a multipart upload.
     * @param array $params the request parameters containing the details to send
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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

    /**
     * Helper function for uploadPart:
     * Uploads a single part of a multipart upload.
     * @param string $namespaceName required parameter
     * @param string $bucketName required parameter
     * @param string $objectName required parameter
     * @param string $uploadId required parameter
     * @param int $uploadPartNum required parameter
     * @param string $uploadPartBody required parameter
     * @param int|null $contentLength optional parameter
     * @param string|null $opcClientRequestId optional parameter
     * @param string|null $ifMatch optional parameter
     * @param string|null $ifNoneMatch optional parameter
     * @param string|null $expect optional parameter
     * @param string|null $contentMD5 optional parameter
     * @param string|null $opcSseCustomerAlgorithm optional parameter
     * @param string|null $opcSseCustomerKey optional parameter
     * @param string|null $opcSseCustomerKeySha256 optional parameter
     * @param string|null $opcSseKmsKeyId optional parameter
     * @return \GuzzleHttp\Promise\PromiseInterface promise for the response object
     */
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
        HttpUtils::addToArray($__headers, Constants::PER_OPERATION_SIGNING_STRATEGY_NAME_HEADER_NAME, (string) SigningStrategies::get("exclude_body"));

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

        return $this->callApiAsync("PUT", "{$this->endpoint}{$__path}{$__queryStr}", [ 'headers' => $__headers, 'body' => $__body ]);
    }

    private $iterators = null;

    // Iterator for async client works in sync mode
    public function iterators()
    {
        if ($this->iterators == null) {
            $this->iterators = new ObjectStorageAsyncIterators($this);
        }
        return $this->iterators;
    }

    /**
    * Magic method. Calls to unknown methods end up here, with the name of the called method in $method.
    *
    * @param string $method method name
    * @param array $args arguments
    * @return mixed result
    */
    public function __call($method, $args)
    {
        // This checks if the called method is a response iterator method, meaning it ends with "...ResponseIterator",
        // and either begins with "list" or has a special iterator config set; if so, it returns a response iterator
        // for the operation (which is the method name without "ResponseIterator" at the end).
        if (parent::isResponseIteratorMethod($method, $this->iterators())) {
            $params = isset($args[0]) ? $args[0] : null;
            return $this->iterators()->responseIterator(substr($method, 0, -strlen(self::RESPONSE_ITERATOR_METHOD_SUFFIX)), $params);
        }

        // If that's not the case, it does the same thing, but for item iterator methods. It checks if the called method
        // is an item iterator method, meaning it ends with "...Iterator",
        // and either begins with "list" or has a special iterator config set; if so, it returns an item iterator
        // for the operation (which is the method name without "Iterator" at the end).
        if (parent::isItemIteratorMethod($method, $this->iterators())) {
            $params = isset($args[0]) ? $args[0] : null;
            return $this->iterators()->itemIterator(substr($method, 0, -strlen(self::ITEM_ITERATOR_METHOD_SUFFIX)), $params);
        }
        throw new BadMethodCallException("Unknown method call to '$method'(" . implode(", ", $args) . ")");
    }
}
