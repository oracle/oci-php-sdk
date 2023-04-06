<?php
/**Copyright (c) 2023, Oracle and/or its affiliates. All rights reserved.
 * This software is dual-licensed to you under the Universal Permissive License
 * (UPL) 1.0 as shown at https://oss.oracle.com/licenses/upl or Apache License
 * 2.0 as shown at http://www.apache.org/licenses/LICENSE-2.0. You may choose
 * either license.
*/
namespace Oracle\Oci\Examples;

require 'vendor/autoload.php';

use Exception;
use Oracle\Oci\ObjectStorage\ObjectStorageClient;
use Oracle\Oci\Common\Auth\ConfigFileAuthProvider;
use Oracle\Oci\Common\Logging\EchoLogAdapter;
use Oracle\Oci\Common\Logging\Logger;
use Oracle\Oci\ObjectStorage\StreamWrapper\StreamWrapper;

// TODO: Update these to your own values
$compartmentId = "ocid1.compartment.oc1..aaaaaaaagc6xvyuhplu3mkb4ewmgjma6uuxfwz56d3gk6alpsc5bfj54wwna";
// END TODO: Update these to your own values

date_default_timezone_set('Etc/UTC');
Logger::setGlobalLogAdapter(new EchoLogAdapter(0, [
]));

$auth_provider = new ConfigFileAuthProvider();
echo "Region from config file: {$auth_provider->getRegion()}" . PHP_EOL;

$c = new ObjectStorageClient($auth_provider);

echo "----- getNamespace -----".PHP_EOL;
$response = $c->getNamespace();
$namespace = $response->getJson();
echo "Namespace = '{$namespace}'".PHP_EOL;

StreamWrapper::register($c, [
    'oci' => [
        StreamWrapper::NAMESPACE_NAME_PARAM => $namespace,
        StreamWrapper::COMPARTMENT_ID_PARAM => $compartmentId
    ]
]);

// mkdir and rmdir

mkdir("oci://testBucket");
mkdir("oci://testBucket/testPseudoDir");
mkdir("oci://testBucket/testPseudoDir/dir");
rmdir("oci://testBucket/testPseudoDir/dir");

// rename
rename("oci://testBucket/testPseudoDir/", "oci://testBucket/testPseudoDir2/");

rmdir("oci://testBucket/testPseudoDir2");

// fopen, fclose, fread, fwrite
if ($fh = fopen("oci://testBucket/test.txt", "w")) {
    fwrite($fh, "This is a test of using the stream wrapper for OCI Object Storage.");
    fclose($fh);
}

if ($fh = fopen("oci://testBucket/test.txt", "r")) {
    $data = fread($fh, 4);
    echo $data;
    $data = fread($fh, 1000);
    echo $data . PHP_EOL;
    fclose($fh);
}

// opendir, readdir, closedir
deleteRecursively("oci://testBucket");

echo "Success!";

function deleteRecursively($dir)
{
    try {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                $path = $dir . '/' . $file;
                try {
                    if (substr($file, -1) == '/') {
                        deleteRecursively($path);
                        rmdir($path);
                    } else {
                        unlink($path);
                    }
                } catch (Exception $e) {
                    // ignore
                }
            }
            closedir($dh);
        }
    } catch (Exception $e) {
        // ignore
    }
    rmdir($dir);
}
