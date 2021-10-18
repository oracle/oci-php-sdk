<?php

namespace Oracle\Oci\Common;

class HttpUtils 
{
    public static function encodeArray(string $paramName, $array, string $collectionFormat) : string
    {
        switch($collectionFormat)
        {
            case "csv": return implode(',', $array);
            case "ssv": return implode(' ', $array);
            case "tsv": return implode('\t', $array);
            case "pipes": return implode('|', $array);
        }
        // default: multi
        return implode("&" . $paramName . "=", $array);
    }
}
?>