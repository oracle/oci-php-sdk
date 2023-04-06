<?php
/**Copyright (c) 2023, Oracle and/or its affiliates. All rights reserved.
 * This software is dual-licensed to you under the Universal Permissive License
 * (UPL) 1.0 as shown at https://oss.oracle.com/licenses/upl or Apache License
 * 2.0 as shown at http://www.apache.org/licenses/LICENSE-2.0. You may choose
 * either license.
*/

// Generated using OracleSDKGenerator, API Version: 20160918

namespace Oracle\Oci\ObjectStorage;

use Oracle\Oci\Common\AbstractClient;
use Oracle\Oci\Common\IteratorConfig;
use Oracle\Oci\Common\Iterators;

class ObjectStorageIterators extends Iterators
{
    public function __construct(AbstractClient $client)
    {
        parent::__construct($client, [
            'listObjectVersions' => new IteratorConfig([
                'responseItemsGetterMethod' => 'buildResponseItemsGetter',
                'responseItemsGetterArgs' => 'items',
            ]),

            'listObjects' => new IteratorConfig([
                'nextTokenResponseGetterMethod' => 'buildNextTokenResponseGetterFromJson',
                'nextTokenResponseGetterArgs' => 'nextStartWith',
                'pageRequestSetterMethod' => 'buildPageRequestSetterToParams',
                'pageRequestSetterArgs' => 'start',
                'responseItemsGetterMethod' => 'buildResponseItemsGetter',
                'responseItemsGetterArgs' => 'objects',
            ]),

            'listRetentionRules' => new IteratorConfig([
                'responseItemsGetterMethod' => 'buildResponseItemsGetter',
                'responseItemsGetterArgs' => 'items',
            ]),

        ]);
    }
}

class ObjectStorageAsyncIterators extends Iterators
{
    public function __construct(AbstractClient $client)
    {
        parent::__construct($client, [
            'listObjectVersionsAsync' => new IteratorConfig([
                'responseItemsGetterMethod' => 'buildResponseItemsGetter',
                'responseItemsGetterArgs' => 'items',
            ]),

            'listObjectsAsync' => new IteratorConfig([
                'nextTokenResponseGetterMethod' => 'buildNextTokenResponseGetterFromJson',
                'nextTokenResponseGetterArgs' => 'nextStartWith',
                'pageRequestSetterMethod' => 'buildPageRequestSetterToParams',
                'pageRequestSetterArgs' => 'start',
                'responseItemsGetterMethod' => 'buildResponseItemsGetter',
                'responseItemsGetterArgs' => 'objects',
            ]),

            'listRetentionRulesAsync' => new IteratorConfig([
                'responseItemsGetterMethod' => 'buildResponseItemsGetter',
                'responseItemsGetterArgs' => 'items',
            ]),

        ]);
    }
}
