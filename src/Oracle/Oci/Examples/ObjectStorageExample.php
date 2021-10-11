<?php

namespace Oracle\Oci\Examples;

require 'vendor/autoload.php';

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

$response = $c->getNamespace();

echo $response->getStatusCode().PHP_EOL;
echo $response->getBody().PHP_EOL

?>