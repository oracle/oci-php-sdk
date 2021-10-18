<?php

namespace Oracle\Oci\Common;

use DateTime;

class HttpUtils 
{
    public static function encodeArray(string $paramName, $array, string $collectionFormat) : string
    {
        if ($array == null || empty($array))
        {
            return "";
        }
        $sep = "&" . $paramName . "=";
        switch($collectionFormat)
        {
            case "csv":
                $sep = ',';
                break;
            case "ssv":
                $sep = ' ';
                break;
            case "tsv":
                $sep = "\t";
                break;
            case "pipes":
                $sep = '|';
                break;
        }
        $result = "";
        foreach($array as $item)
        {
            if (strlen($result) > 0)
            {
                $result = $result . $sep;
            }
            $result = $result . HttpUtils::attemptEncodeQueryParam($item);
        }
        return $paramName . "=" . $result;
    }

    public static function encodeMap(string $paramName, string $prefix, $map) : string
    {
        if ($prefix == null) {
            $prefix = "";
        }
        $result = "";
        if ($map != null) {
            foreach ($map as $key => $value) {
                $result = $result . "&" . HttpUtils::encodeMapQueryParamValue($prefix . $key, $value);
            }
            $result = substr($result, 1);   
        }
        return $result;
    }

    public static function encodeMapQueryParamValue(string $prefixedKey, $value) : string
    {
        if (is_array($value)) {
            $result = "";
            foreach($value as $item)
            {
                $result = $result . "&" . $prefixedKey . "=" . HttpUtils::attemptEncodeQueryParam($item);
            }
            return substr($result, 1);
        }
        return $prefixedKey . "=" . HttpUtils::attemptEncodeQueryParam($value);
    }

    public static function attemptEncodeQueryParam($value) : string
    {
        if ($value instanceof DateTime)
        {
            return $value->format(DateTime::RFC3339_EXTENDED);
        }
        return strval($value);
    }
}
?>