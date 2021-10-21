<?php

namespace Oracle\Oci\Common;

use Exception;

class StringUtils
{
    public static function get_type_or_class($data)
    {
        $t = gettype($data);
        if ($t == "object") {
            return get_class($data);
        } elseif ($t == "resource") {
            return $t . " (" . get_resource_type($data) . ")";
        }
        return $t;
    }

    public static function base64url_decode($data, $strict = false)
    {
        // Convert Base64URL to Base64 by replacing “-” with “+” and “_” with “/”
        $b64 = strtr($data, '-_', '+/');

        // Decode Base64 string and return the original data
        return base64_decode($b64, $strict);
    }

    public static function base64url_encode($data, $strict = false)
    {
        // Encode Base64 string
        $b64 = base64_encode($data);

        // Convert Base64 to Base64URL by replacing “+” with “-” and “/” with “_”
        return strtr($b64, '+/', '-_');
    }

    public static function base64_to_base64url($data, $strict = false)
    {
        // Convert Base64 Base64URL by replacing “+” with “-” and “/” with “_”
        return strtr($data, '+/', '-_');
    }

    public static function generateCallTrace()
    {
        // from https://www.php.net/manual/en/function.debug-backtrace.php#112238
        $e = new Exception();
        $trace = explode("\n", $e->getTraceAsString());
        // reverse array to make steps line up chronologically
        $trace = array_reverse($trace);
        array_shift($trace); // remove {main}
        array_pop($trace); // remove call to this method
        $length = count($trace);
        $result = array();
    
        for ($i = 0; $i < $length; $i++) {
            $result[] = ($i + 1)  . ')' . substr($trace[$i], strpos($trace[$i], ' ')); // replace '#someNum' with '$i)', set the right ordering
        }
    
        return "\t" . implode("\n\t", $result);
    }
}
