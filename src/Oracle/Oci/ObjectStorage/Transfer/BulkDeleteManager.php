<?php
/**Copyright (c) 2023, Oracle and/or its affiliates. All rights reserved.
 * This software is dual-licensed to you under the Universal Permissive License
 * (UPL) 1.0 as shown at https://oss.oracle.com/licenses/upl or Apache License
 * 2.0 as shown at http://www.apache.org/licenses/LICENSE-2.0. You may choose
 * either license.
*/
namespace Oracle\Oci\ObjectStorage\Transfer;

use Oracle\Oci\ObjectStorage\ObjectStorageAsyncClient;
use GuzzleHttp\Promise\Utils;

class BulkDeleteManager
{
    protected $client;
    protected $config;

    /**
     * Construct method for BulkDeleteManager
     * @param ObjectStorageAsyncClient $client async object storage client
     * @param array $config config the bulkDeleteManager, currently not in use
     */
    public function __construct(ObjectStorageAsyncClient $client, array $config = [])
    {
        $this->client = $client;
        $this->config = $config;
    }

    public function config(array $config)
    {
        $this->config = $config + $this->config;
    }

    /**
     * Bulk Delete the objects, this function will wait until all operations are completed, return a list of responses
     * @param string $namespace Namespace where the bucket is located
     * @param string $bucketName Bucket where the objects are stored
     * @param string $prefix Delete objects under this key prefix
     * @param string $regex Delete objects that match this regex
     * @param array $listParams Params for listing the objects under the bucket
     * @param array $deleteParams Params when performing delete operation
     * @param bool $caseInsensitive whether to allow case insensitive comparison on object name, only valid for prefix comaprison, default to true
     * @return mixed result
     */
    public function bulkDelete($namespace, $bucketName, $prefix = '', $regex = '', $customFilter = null, array $listParams = [], array $deleteParams = [], $caseInsensitive = true)
    {
        return $this->waitOnBulkDelete($this->bulkDeleteAsync($namespace, $bucketName, $prefix, $regex, $customFilter, $listParams, $deleteParams, $caseInsensitive));
    }

    /**
     * Bulk Delete the objects, this function will return a list of promises directly
     * @param string $namespace Namespace where the bucket is located
     * @param string $bucketName Bucket where the objects are stored
     * @param string $prefix Delete objects under this key prefix
     * @param string $regex Delete objects that match this regex
     * @param callable $customFilter Function that filter the object to be deleted
     * @param array $listParams Params for listing the objects under the bucket
     * @param array $deleteParams Params when performing delete operation
     * @param bool $caseInsensitive whether to allow case insensitive comparison on object name, only valid for prefix comaprison, default to true
     * @return mixed result
     */
    public function bulkDeleteAsync($namespace, $bucketName, $prefix = '', $regex = '', $customFilter = null, array $listParams = [], array $deleteParams = [], $caseInsensitive = true)
    {
        // Params for listing the objects
        $params = [
            'namespaceName' => $namespace,
            'bucketName' => $bucketName,
        ];

        $prefix = $caseInsensitive && $prefix ? strtolower($prefix) : $prefix;
        $prefixLength = $prefix ? strlen($prefix) : 0;
        $prefixMatch = function ($name) use ($prefix, $prefixLength) {
            return strtolower(substr($name, 0, $prefixLength)) === $prefix;
        };
        if (!$caseInsensitive && $prefix) {
            // case-sensitive comparison by the server
            $params['prefix'] = $prefix;
            // we'll not compare on client side
            $prefixMatch = function ($name) {
                return true;
            };
        }

        $regexMatch = function ($name) use ($regex) {
            return empty($regex) ? true : preg_match($regex, $name);
        };

        $customFilterMatch = function ($name) use ($customFilter) {
            return $customFilter == null ? true: $customFilter($name);
        };


        $iterator = $this->client->listObjectsAsyncIterator(array_merge($params, $listParams));

        $deletePromises = [];
        foreach ($iterator as $k => $v) {
            if ($prefixMatch($v->name) && $regexMatch($v->name) && $customFilterMatch($v->name)) {
                $deletePromises[] = $this->client->deleteObjectAsync(array_merge(
                    [
                    'namespaceName'=>$namespace,
                    'bucketName'=>$bucketName,
                    'objectName'=>$v->name],
                    $deleteParams
                ));
            }
        }
        return $deletePromises;
    }


    /**
    * Waits on all of the provided promises and returns the fulfilled values.
    *
    * Returns an array that contains the value of each promise (in the same
    * order the promises were provided). An exception is thrown if any of the
    * promises are rejected.
    */
    public function waitOnBulkDelete($requestDeleteArr)
    {
        return Utils::unwrap($requestDeleteArr);
    }
}
