<?php
/**Copyright (c) 2023, Oracle and/or its affiliates. All rights reserved.
 * This software is dual-licensed to you under the Universal Permissive License
 * (UPL) 1.0 as shown at https://oss.oracle.com/licenses/upl or Apache License
 * 2.0 as shown at http://www.apache.org/licenses/LICENSE-2.0. You may choose
 * either license.
*/
namespace Oracle\Oci\ObjectStorage;

use BadMethodCallException;
use Oracle\Oci\ObjectStorage\Transfer\UploadManager;
use Oracle\Oci\ObjectStorage\Transfer\BulkDeleteManager;
use UploadManagerConstants;

// This class wraps objectStorageAsyncClient, uploadManager and bulkDeleteManager
// All functions under those classes can be directly called through this helper class
class ObjectStorageHelper
{
    const UPLOADMANAGER_CONFIG = 'uploadManagerConfig';
    const BULKDELETEMANAGER_CONFIG = 'bulkDeleteManagerConfig';
    const DEFAULT_HELPER_CONFIG = [
        SELF::UPLOADMANAGER_CONFIG => UploadManagerConstants::DEFAULT_CONFIG,
        SELF::BULKDELETEMANAGER_CONFIG => []
    ];

    protected $client;

    protected $config;

    protected $uploadManager;
    
    protected $bulkDeleteManager;

    public function __construct(ObjectStorageAsyncClient $client, array $config = [])
    {
        $this->client = $client;
        $this->config = $config + SELF::DEFAULT_HELPER_CONFIG;
    }


    public function __call($method, $args)
    {
        // Check if the function exists in client
        if (method_exists($this->getClient(), $method)) {
            return call_user_func_array(array($this->getClient(), $method), $args);
        }
        // Check if the function exists in upload manager
        if (method_exists($this->getUploadManager(), $method)) {
            return call_user_func_array(array($this->getUploadManager(), $method), $args);
        }
        // Check if the function exists in bulk delete manager
        if (method_exists($this->getBulkDeleteManager(), $method)) {
            return call_user_func_array(array($this->getBulkDeleteManager(), $method), $args);
        }

        throw new BadMethodCallException("Unknown method call to '$method'(" . implode(", ", $args) . ")");
    }

    public function getUploadManager()
    {
        if (!isset($this->uploadManager)) {
            $this->uploadManager = new UploadManager($this->client, $this->config[SELF::UPLOADMANAGER_CONFIG]);
        }
        return $this->uploadManager;
    }

    public function getBulkDeleteManager()
    {
        if (!isset($this->bulkDeleteManager)) {
            $this->bulkDeleteManager = new BulkDeleteManager($this->client, $this->config[SELF::BULKDELETEMANAGER_CONFIG]);
        }
        return $this->bulkDeleteManager;
    }

    public function getClient()
    {
        return $this->client;
    }
}
