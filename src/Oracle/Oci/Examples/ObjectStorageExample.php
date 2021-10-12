<?php

namespace Oracle\Oci\Examples;

require 'vendor/autoload.php';

require 'src/Oracle/Oci/Common/OciResponse.php';
require 'src/Oracle/Oci/ObjectStorage/ObjectStorageClient.php';

use \Oracle\Oci\ObjectStorage\ObjectStorageClient;

$c = new ObjectStorageClient(
    tenancy_id: 'ocid1.tenancy.oc1..aaaaaaaacqp432hpa5oc2kvxm4kpwbkodfru4okbw2obkcdob5zuegi4rwxq',
    user_id: 'ocid1.user.oc1..aaaaaaaabiszhenencetzhewboxb3fimi4izpxzsatigo7cqrmbdlitzngza',
    thumbprint: '83:f3:27:6b:bf:0d:50:7b:09:d0:92:49:6f:1f:89:32',
    region: 'us-phoenix-1',
    key_filename: 'file:///Users/mricken/.oci/bmcs_api_key.pem',
    key_passphrase: null
);

echo "----- getNamespace -----".PHP_EOL;
$response = $c->getNamespace();
$response->print();
$namespace = $response->getBody();

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
    echo "Retrieved body does not uploaded body!";
}

echo "----- headObject -----".PHP_EOL;
$response = $c->headObject($namespace, $bucket_name, $object_name);
$response->print();

$object_name2 = "php-test2.txt";

echo "----- putObject with file -----".PHP_EOL;
$file_handle = fopen("composer.json", "rb");
$response = $c->putObject($namespace, $bucket_name, $object_name2, $file_handle);
$response->print();

echo "----- listObjects -----".PHP_EOL;
$response = $c->listObjects($namespace, $bucket_name);
$response->print();

echo "----- listObjects with prefix -----".PHP_EOL;
$response = $c->listObjects(namespace: $namespace, bucket_name: $bucket_name, prefix: "dexreq-");
$response->print();

?>