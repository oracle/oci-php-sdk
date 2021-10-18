<?php

namespace Oracle\Oci\Common;

use DateTime;

class HttpUtils 
{
    public static function addToArray(&$queryMap, $paramName, string $value)
    {
        if (array_key_exists($paramName, $queryMap)) {
            $oldValue = $queryMap[$paramName];
            if (is_array($oldValue)) {
                $oldValue[] = $value;
                $queryMap[$paramName] = $oldValue;
            }
            else {
                $queryMap[$paramName] = [$oldValue, $value];
            }
        }
        else
        {
            $queryMap[$paramName] = $value;
        }
    }

    public static function encodeArray(&$queryMap, string $paramName, $array, string $collectionFormat)
    {
        if ($array == null || empty($array))
        {
            return;
        }
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
            default:
                $collectionFormat = "multi";
                break;
        }
        if ($collectionFormat == "multi") 
        {
            foreach($array as $item)
            {
                HttpUtils::addToArray($queryMap, $paramName, HttpUtils::attemptEncodeQueryParam($item));
            }    
        }
        else 
        {
            $result = "";
            foreach($array as $item)
            {
                if (strlen($result) > 0)
                {
                    $result = $result . $sep;
                }
                $result = $result . HttpUtils::attemptEncodeQueryParam($item);
            }
            HttpUtils::addToArray($queryMap, $paramName, $result);
        }
    }

    public static function encodeMap(&$queryMap, string $paramName, ?string $prefix, $map)
    {
        if ($prefix == null) {
            $prefix = "";
        }
        if ($map != null) {
            foreach ($map as $key => $value) {
                HttpUtils::encodeMapQueryParamValue($queryMap, $prefix . $key, $value);
            }
        }
    }

    public static function encodeMapQueryParamValue(&$queryMap, string $prefixedKey, $value)
    {
        if (is_array($value)) {
            foreach($value as $item)
            {
                HttpUtils::addToArray($queryMap, $prefixedKey, HttpUtils::attemptEncodeQueryParam($item));
            }
        }
        else
        {
            HttpUtils::addToArray($queryMap, $prefixedKey, HttpUtils::attemptEncodeQueryParam($value));
        }
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