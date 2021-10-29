<?php

namespace Oracle\Oci\Examples;

require 'vendor/autoload.php';

use DateTime;
use GuzzleHttp\Exception\ServerException;
use Oracle\Oci\ObjectStorage\ObjectStorageClient;
use Oracle\Oci\Common\Auth\InstancePrincipalsAuthProvider;
use Oracle\Oci\Common\Logging\EchoLogAdapter;
use Oracle\Oci\Common\Logging\Logger;
use Oracle\Oci\Common\OciBadResponseException;

// This example must be run on an OCI instance.
// 1. Create a dynamic group. You can use a matching rule like this to get all instances in a certain compartment:
//        Any {instance.compartment.id = '<ocid-of-compartment>'}
// 2. Start an OCI instance. Make sure that it is matched by the dynamic group, e.g. by creating it in the correct compartment.
// 3. Create a policy for the dynamic group that grants the desired permissions. For example:
//        Allow dynamic-group <name-of-dynamic-group> to manage buckets in compartment <name-of-compartment>
//        Allow dynamic-group <name-of-dynamic-group> to manage objects in compartment <name-of-compartment>
//        Allow dynamic-group mricken-test-dg to manage objectstorage-namespaces in compartment mricken-test
// 4. Copy the OCI PHP SDK and this example to the OCI instance (using scp or rsync).
// 5. SSH into the OCI instance and run it.

// TODO: Update these to your own values
$bucket_name = "mricken-test";
$file_to_upload = "composer.json";
$compartmentId = "ocid1.compartment.oc1..aaaaaaaagc6xvyuhplu3mkb4ewmgjma6uuxfwz56d3gk6alpsc5bfj54wwna";
// END TODO: Update these to your own values

date_default_timezone_set('Etc/UTC');
Logger::setGlobalLogAdapter(new EchoLogAdapter(0, [
    "Oracle\\Oci\\ObjectStorage\\ObjectStorageClient" => LOG_DEBUG,
    "Oracle\\Oci\\Common\\OciResponse" => LOG_DEBUG
]));

try {
    $auth_provider = new InstancePrincipalsAuthProvider();
} catch (ServerException $e) {
    if (strpos($e, "cannotconnect") !== false) {
        echo("This sample only works when running on an OCI instance. Are you sure you’re running on an OCI instance?" . PHP_EOL
        . "For more info see: https://docs.cloud.oracle.com/Content/Identity/Tasks/callingservicesfrominstances.htm" . PHP_EOL);
        exit(1);
    }
    throw $e;
}

$c = new ObjectStorageClient($auth_provider);

echo "----- getNamespace -----".PHP_EOL;
$response = $c->getNamespace();
$namespace = $response->getJson();

echo "Namespace = '{$namespace}'".PHP_EOL;

$object_name = "php-test.txt";
$body = "This is a test of Object Storage from PHP.";

echo "----- putObject -----".PHP_EOL;
$response = $c->putObject([
    'namespaceName' => $namespace,
    'bucketName' => $bucket_name,
    'objectName' => $object_name,
    'putObjectBody' => $body]);

echo "----- getObject -----".PHP_EOL;
$response = $c->getObject([
    'namespaceName' => $namespace,
    'bucketName' => $bucket_name,
    'objectName' => $object_name]);

$retrieved_body = $response->getBody();

echo "Sent: $body" . PHP_EOL;
echo "Recv: $retrieved_body" . PHP_EOL;

if ($body != $retrieved_body) {
    echo "ERROR: Retrieved body does not equal uploaded body!".PHP_EOL;
    die;
} else {
    echo "Retrieved body equals uploaded body!".PHP_EOL;
}

echo "----- headObject -----".PHP_EOL;
$response = $c->headObject([
    'namespaceName' => $namespace,
    'bucketName' => $bucket_name,
    'objectName' => $object_name]);

$object_name2 = "php-test2.txt";

echo "----- putObject with file -----".PHP_EOL;
$file_handle = fopen($file_to_upload, "rb");
$response = $c->putObject([
    'namespaceName' => $namespace,
    'bucketName' => $bucket_name,
    'objectName' => $object_name2,
    'putObjectBody' => $file_handle]);

echo "----- headObject of uploaded file -----".PHP_EOL;
$file_handle = fopen($file_to_upload, "rb");
$response = $c->headObject([
    'namespaceName' => $namespace,
    'bucketName' => $bucket_name,
    'objectName' => $object_name2]);
$retrieved_filesize = $response->getHeaders()['Content-Length'][0];
$size = filesize($file_to_upload);
if ($size != $retrieved_filesize) {
    echo "ERROR: Retrieved file size ($retrieved_filesize) does not equal uploaded file size ($size)!".PHP_EOL;
    die;
} else {
    echo "Retrieved file size ($retrieved_filesize) equals uploaded file size ($size)!".PHP_EOL;
}

echo "----- copyObject -----".PHP_EOL;

$object_name3 = "php-test3.txt";
$copy_object_details = [
    'sourceObjectName' => $object_name2,
    'destinationRegion' => $auth_provider->getRegion()->getRegionId(),
    'destinationNamespace' => $namespace,
    'destinationBucket' => $bucket_name,
    'destinationObjectName' => $object_name3
];
$response = $c->copyObject([
    'namespaceName' => $namespace,
    'bucketName' => $bucket_name,
    'copyObjectDetails' => $copy_object_details]);
$workrequest_id = $response->getHeaders()['opc-work-request-id'][0];
echo "Work request id: $workrequest_id".PHP_EOL;

echo "----- Wait for Work Request to be Done (getWorkRequest) -----".PHP_EOL;
$isDone = false;
while (!$isDone) {
    $response = $c->getWorkRequest([
        'workRequestId' => $workrequest_id
    ]);
    $status = $response->getJson()->status;
    $timeFinished = $response->getJson()->timeFinished;
    if ($status == "COMPLETED" || $timeFinished != null) {
        echo "Work request status: $status".PHP_EOL;
        $isDone = true;
    } elseif ($timeFinished != null) {
        echo "Work request status: $status, terminal state reached at $timeFinished".PHP_EOL;
        $isDone = true;
    } else {
        echo "Work request status: $status, sleeping for 1 seconds..." . PHP_EOL;
        sleep(1);
    }
}

echo "----- listWorkRequestLogs -----".PHP_EOL;
$c->listWorkRequestLogs([
    'workRequestId' => $workrequest_id
]);

echo "----- listWorkRequestErrors -----".PHP_EOL;
$c->listWorkRequestErrors([
    'workRequestId' => $workrequest_id
]);

echo "----- headObject of copied file -----".PHP_EOL;
$file_handle = fopen($file_to_upload, "rb");
$response = $c->headObject([
    'namespaceName' => $namespace,
    'bucketName' => $bucket_name,
    'objectName' => $object_name3]);
$retrieved_filesize = $response->getHeaders()['Content-Length'][0];
$size = filesize($file_to_upload);
if ($size != $retrieved_filesize) {
    echo "ERROR: Retrieved file size ($retrieved_filesize) does not equal uploaded file size ($size)!".PHP_EOL;
    die;
} else {
    echo "Retrieved file size ($retrieved_filesize) equals uploaded file size ($size)!".PHP_EOL;
}

echo "----- listObjects -----".PHP_EOL;
$response = $c->listObjects([
    'namespaceName' => $namespace,
    'bucketName' => $bucket_name]);

echo "----- listObjects with prefix -----".PHP_EOL;
$response = $c->listObjects([
    'namespaceName' => $namespace,
    'bucketName' => $bucket_name,
    'prefix' => "dexreq-"]);

echo "----- headObject for missing file -----".PHP_EOL;
try {
    $response = $c->headObject([
        'namespaceName' => $namespace,
        'bucketName' => $bucket_name,
        'objectName' => "doesNotExist"]);
    echo "ERROR: Object was supposed to not exist!".PHP_EOL;
    die;
} catch (OciBadResponseException $e) {
    echo $e . PHP_EOL;
    $statusCode = $e->getStatusCode();
    if ($statusCode != 404) {
        echo "ERROR: Returned $statusCode instead of 404!".PHP_EOL;
        die;
    }
}

echo "----- putObject with file into subdirectory -----".PHP_EOL;
$object_name4 = "php-test/php-test4.txt";
$file_handle = fopen($file_to_upload, "rb");
$response = $c->putObject([
    'namespaceName' => $namespace,
    'bucketName' => $bucket_name,
    'objectName' => $object_name4,
    'putObjectBody' => $file_handle,
    'opcMeta' => [
        'header1' => new DateTime(),
        'header2' => ["2", "3"]
    ]]);

echo "----- headObject of uploaded file -----".PHP_EOL;
$file_handle = fopen($file_to_upload, "rb");
$response = $c->headObject([
    'namespaceName' => $namespace,
    'bucketName' => $bucket_name,
    'objectName' => $object_name4]);
$retrieved_filesize = $response->getHeaders()['Content-Length'][0];
$size = filesize($file_to_upload);
if ($size != $retrieved_filesize) {
    echo "ERROR: Retrieved file size ($retrieved_filesize) does not equal uploaded file size ($size)!".PHP_EOL;
    die;
} else {
    echo "Retrieved file size ($retrieved_filesize) equals uploaded file size ($size)!".PHP_EOL;
}

echo "----- listBuckets -----".PHP_EOL;
$response = $c->listBuckets([
    'namespaceName' => $namespace,
    'compartmentId' => $compartmentId,
    'fields' => ["tags"]]);