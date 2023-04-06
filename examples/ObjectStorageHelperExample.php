<?php
/**Copyright (c) 2023, Oracle and/or its affiliates. All rights reserved.
 * This software is dual-licensed to you under the Universal Permissive License
 * (UPL) 1.0 as shown at https://oss.oracle.com/licenses/upl or Apache License
 * 2.0 as shown at http://www.apache.org/licenses/LICENSE-2.0. You may choose
 * either license.
*/
namespace Oracle\Oci\Examples;

require 'vendor/autoload.php';

use Oracle\Oci\Common\Region;
use Oracle\Oci\Common\UserAgent;
use Oracle\Oci\Common\Auth\ConfigFileAuthProvider;
use Oracle\Oci\ObjectStorage\BulkDeleteHelper;
use Oracle\Oci\ObjectStorage\ObjectStorageAsyncClient;
use Oracle\Oci\ObjectStorage\ObjectStorageClient;
use Oracle\Oci\ObjectStorage\ObjectStorageHelper;
use Oracle\Oci\ObjectStorage\Transfer\UploadManagerRequest;
use UploadManagerConstants;

date_default_timezone_set('Europe/Istanbul');

echo "UserAgent: " . UserAgent::getUserAgent() . PHP_EOL;

$region = Region::getRegion("us-phoenix-1");
echo "Region: $region".PHP_EOL;

$auth_provider = new ConfigFileAuthProvider();

$c = new ObjectStorageAsyncClient(
    $auth_provider,
    $region
);
echo "----- getNamespace -----".PHP_EOL;
$response = $c->getNamespaceAsync()->wait();
$namespace = $response->getJson();

echo "Namespace = '{$namespace}'".PHP_EOL;

$bucket_name = "ziyao_test_object_storage";

// This helper wraps async object storage client, upload manager and bulkd delete manger
$objectStorageHelper = new ObjectStorageHelper($c);

$response = $c->listObjectsAsync([
    'namespaceName'=>$namespace,
    'bucketName'=>$bucket_name
])->wait();

// list anything contains word 'example' using regex
// @phpstan-ignore-next-line
$response = $objectStorageHelper->bulkDeleteAsync($namespace, $bucket_name, 'Oci', '/example/');
// @phpstan-ignore-next-line
$responses = $objectStorageHelper->waitOnBulkDelete($response);

var_dump($responses);
