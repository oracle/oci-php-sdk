<?php
/**Copyright (c) 2023, Oracle and/or its affiliates. All rights reserved.
 * This software is dual-licensed to you under the Universal Permissive License
 * (UPL) 1.0 as shown at https://oss.oracle.com/licenses/upl or Apache License
 * 2.0 as shown at http://www.apache.org/licenses/LICENSE-2.0. You may choose
 * either license.
*/
namespace Oracle\Oci\Common;

class Defer
{
    private $callback;

    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    public function __destruct()
    {
        call_user_func($this->callback);
    }
}

function defer(&$context, $callback)
{
    $context[] = new Defer($callback);
}
