<?php

namespace Oracle\Oci\Common;

use DateTime;
use InvalidArgumentException;

class StringUtils
{
    public static function get_type_or_class($data)
    {
        $t = gettype($data);
        if ($t == "object") {
            return get_class($data);
        }
        return $t;
    }
}
