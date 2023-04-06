<?php
/**Copyright (c) 2023, Oracle and/or its affiliates. All rights reserved.
 * This software is dual-licensed to you under the Universal Permissive License
 * (UPL) 1.0 as shown at https://oss.oracle.com/licenses/upl or Apache License
 * 2.0 as shown at http://www.apache.org/licenses/LICENSE-2.0. You may choose
 * either license.
*/
namespace Oracle\Oci\Common;

class Version {
    const Major = 0;
    const Minor = 1;
    const Patch = 0;
    
    public static function Version() {
        return sprintf("%s.%s.%s", self::Major, self::Minor, self::Patch);
    }
}
