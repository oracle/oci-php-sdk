<?php

namespace Oracle\Oci\Examples;

require 'vendor/autoload.php';

require 'src/Oracle/Oci/Common/AuthProviderInterface.php';
require 'src/Oracle/Oci/Common/OciResponse.php';
require 'src/Oracle/Oci/Common/Regions.php';
require 'src/Oracle/Oci/ObjectStorage/ObjectStorageClient.php';

use Oracle\Oci\Common\UserAuthProviderInterface;
use Oracle\Oci\Common\Region;
use Oracle\Oci\ObjectStorage\ObjectStorageClient;

$region = Region::getRegion("us-phoenix-1");
echo "Region: $region".PHP_EOL;

$auth_provider = new UserAuthProviderInterface(
    tenancy_id: 'ocid1.tenancy.oc1..aaaaaaaacqp432hpa5oc2kvxm4kpwbkodfru4okbw2obkcdob5zuegi4rwxq',
    user_id: 'ocid1.user.oc1..aaaaaaaabiszhenencetzhewboxb3fimi4izpxzsatigo7cqrmbdlitzngza',
    fingerprint: '83:f3:27:6b:bf:0d:50:7b:09:d0:92:49:6f:1f:89:32',
    key_filename: 'file:///Users/mricken/.oci/bmcs_api_key.pem'
);

$c = new ObjectStorageClient(
    auth_provider: $auth_provider,
    region: $region
);

echo "----- getNamespace -----".PHP_EOL;
$response = $c->getNamespace();
$response->print();
$namespace = $response->getJson();

echo "Namespace = '{$namespace}'".PHP_EOL;

$bucket_name = "mricken-test";
$object_name = "php-test.txt";
$body = "This is a test of Object Storage from PHP.";

echo "----- putObject -----".PHP_EOL;
$response = $c->putObject($namespace, $bucket_name, $object_name, $body);
$response->print();

echo "----- getObject -----".PHP_EOL;
$response = $c->getObject($namespace, $bucket_name, $object_name);
$response->print();

$retrieved_body = $response->getBody();

if ($body != $retrieved_body)
{
    echo "ERROR: Retrieved body does not equal uploaded body!".PHP_EOL;
    die;
}
else
{
    echo "Retrieved body equals uploaded body!".PHP_EOL;
}

echo "----- headObject -----".PHP_EOL;
$response = $c->headObject($namespace, $bucket_name, $object_name);
$response->print();

$object_name2 = "php-test2.txt";

echo "----- putObject with file -----".PHP_EOL;
$file_handle = fopen("composer.json", "rb");
$response = $c->putObject($namespace, $bucket_name, $object_name2, $file_handle);
$response->print();

echo "----- headObject of uploaded file -----".PHP_EOL;
$file_handle = fopen("composer.json", "rb");
$response = $c->headObject($namespace, $bucket_name, $object_name2);
$response->print();
$retrieved_filesize = $response->getHeaders()['Content-Length'][0];
$size = filesize("composer.json");
if ($size != $retrieved_filesize)
{
    echo "ERROR: Retrieved file size ($retrieved_filesize) does not equal uploaded file size ($size)!".PHP_EOL;
    die;
}
else
{
    echo "Retrieved file size ($retrieved_filesize) equals uploaded file size ($size)!".PHP_EOL;
}

echo "----- copyObject -----".PHP_EOL;

$object_name3 = "php-test3.txt";
$copy_object_details = [
    'sourceObjectName' => $object_name2,
    'destinationRegion' => $region->getRegionId(),
    'destinationNamespace' => $namespace,
    'destinationBucket' => $bucket_name,
    'destinationObjectName' => $object_name3
];
$response = $c->copyObject($namespace, $bucket_name, $copy_object_details);
$response->print();

echo "----- headObject of copied file -----".PHP_EOL;
$file_handle = fopen("composer.json", "rb");
$response = $c->headObject($namespace, $bucket_name, $object_name3);
$response->print();
$retrieved_filesize = $response->getHeaders()['Content-Length'][0];
$size = filesize("composer.json");
if ($size != $retrieved_filesize)
{
    echo "ERROR: Retrieved file size ($retrieved_filesize) does not equal uploaded file size ($size)!".PHP_EOL;
    die;
}
else
{
    echo "Retrieved file size ($retrieved_filesize) equals uploaded file size ($size)!".PHP_EOL;
}

echo "----- listObjects -----".PHP_EOL;
$response = $c->listObjects($namespace, $bucket_name);
$response->print();

echo "----- listObjects with prefix -----".PHP_EOL;
$response = $c->listObjects(namespaceName: $namespace, bucketName: $bucket_name, prefix: "dexreq-");
$response->print();

?>