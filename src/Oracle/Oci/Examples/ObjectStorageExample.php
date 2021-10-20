<?php

namespace Oracle\Oci\Examples;

require 'vendor/autoload.php';

require 'src/Oracle/Oci/Common/AuthProviderInterface.php';
require 'src/Oracle/Oci/Common/OciResponse.php';
require 'src/Oracle/Oci/Common/Regions.php';
require 'src/Oracle/Oci/Common/UserAgent.php';
require 'src/Oracle/Oci/Common/HttpUtils.php';
require 'src/Oracle/Oci/ObjectStorage/ObjectStorageClient.php';

use DateTime;
use Oracle\Oci\Common\Region;
use Oracle\Oci\Common\UserAgent;
use Oracle\Oci\ObjectStorage\ObjectStorageClient;
use GuzzleHttp\Exception\ClientException;
use Oracle\Oci\Common\AbstractClient;
use Oracle\Oci\Common\ConfigFileAuthProvider;
use Oracle\Oci\Common\Logging\EchoLogAdapter;

date_default_timezone_set('Europe/Istanbul');
AbstractClient::setGlobalLogAdapter(new EchoLogAdapter(LOG_INFO, [
    "Oracle\Oci\ObjectStorage\ObjectStorageClient\middleware\\uri" => LOG_DEBUG,
    "Oracle\Oci\Common\OciResponse" => LOG_DEBUG
]));

echo "UserAgent: " . UserAgent::getUserAgent() . PHP_EOL;
// UserAgent::setAdditionalClientUserAgent("Oracle-CloudShell");
// echo "UserAgent: " . UserAgent::getUserAgent() . PHP_EOL;
// putenv("OCI_SDK_APPEND_USER_AGENT=Oracle-CloudDevelopmentKit");
// UserAgent::init();
// echo "UserAgent: " . UserAgent::getUserAgent() . PHP_EOL;
// UserAgent::setAdditionalClientUserAgent("");
// echo "UserAgent: " . UserAgent::getUserAgent() . PHP_EOL;

$region = Region::getRegion("us-phoenix-1");
echo "Region: $region".PHP_EOL;

$auth_provider = new ConfigFileAuthProvider();

// $auth_provider = new UserAuthProvider(
//     'ocid1.tenancy.oc1..aaaaaaaacqp432hpa5oc2kvxm4kpwbkodfru4okbw2obkcdob5zuegi4rwxq',
//     'ocid1.user.oc1..aaaaaaaabiszhenencetzhewboxb3fimi4izpxzsatigo7cqrmbdlitzngza',
//     '83:f3:27:6b:bf:0d:50:7b:09:d0:92:49:6f:1f:89:32',
//     'file:///Users/mricken/.oci/bmcs_api_key.pem'
// );

// $auth_provider = new ConfigFileAuthProvider(ConfigFile::loadDefault("OTHER"));

// $auth_provider = new ConfigFileAuthProvider(ConfigFile::loadFromFile(ConfigFile::getUserHome() . "/.oci/config_dex-us-phoenix-1-manual", "OTHER"));

$c = new ObjectStorageClient(
    $auth_provider,
    $region
);

echo "----- getNamespace -----".PHP_EOL;
$response = $c->getNamespace();
$namespace = $response->getJson();

echo "Namespace = '{$namespace}'".PHP_EOL;

$bucket_name = "mricken-test";
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
$response = $c->headObject([
    'namespaceName' => $namespace,
    'bucketName' => $bucket_name,
    'objectName' => $object_name]);

$object_name2 = "php-test2.txt";

echo "----- putObject with file -----".PHP_EOL;
$file_handle = fopen("composer.json", "rb");
$response = $c->putObject([
    'namespaceName' => $namespace,
    'bucketName' => $bucket_name,
    'objectName' => $object_name2,
    'putObjectBody' => $file_handle]);

echo "----- headObject of uploaded file -----".PHP_EOL;
$file_handle = fopen("composer.json", "rb");
$response = $c->headObject([
    'namespaceName' => $namespace,
    'bucketName' => $bucket_name,
    'objectName' => $object_name2]);
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
$response = $c->copyObject([
    'namespaceName' => $namespace,
    'bucketName' => $bucket_name,
    'copyObjectDetails' => $copy_object_details]);

echo "----- headObject of copied file -----".PHP_EOL;
$file_handle = fopen("composer.json", "rb");
$response = $c->headObject([
    'namespaceName' => $namespace,
    'bucketName' => $bucket_name,
    'objectName' => $object_name3]);
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
$response = $c->listObjects([
    'namespaceName' => $namespace,
    'bucketName' => $bucket_name]);

echo "----- listObjects with prefix -----".PHP_EOL;
$response = $c->listObjects([
    'namespaceName' => $namespace,
    'bucketName' => $bucket_name,
    'prefix' => "dexreq-"]);

echo "----- headObject for missing file -----".PHP_EOL;
try
{
    $response = $c->headObject([
        'namespaceName' => $namespace,
        'bucketName' => $bucket_name,
        'objectName' => "doesNotExist"]);
    // $response->echoResponse();
    echo "ERROR: Object was supposed to not exist!".PHP_EOL;
    die;
}
catch(ClientException $e)
{
    $statusCode = $e->getResponse()->getStatusCode();
    if ($statusCode != 404)
    {
        echo "ERROR: Returned $statusCode instead of 404!".PHP_EOL;
        die;
    }
}

echo "----- putObject with file into subdirectory -----".PHP_EOL;
$object_name4 = "php-test/php-test4.txt";
$file_handle = fopen("composer.json", "rb");
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
$file_handle = fopen("composer.json", "rb");
$response = $c->headObject([
    'namespaceName' => $namespace,
    'bucketName' => $bucket_name,
    'objectName' => $object_name4]);
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

echo "----- listBuckets -----".PHP_EOL;
$response = $c->listBuckets([
    'namespaceName' => $namespace,
    'compartmentId' => "ocid1.tenancy.oc1..aaaaaaaacqp432hpa5oc2kvxm4kpwbkodfru4okbw2obkcdob5zuegi4rwxq",
    'fields' => ["tags", "tags"]]);

?>