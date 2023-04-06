<?php
/**Copyright (c) 2023, Oracle and/or its affiliates. All rights reserved.
 * This software is dual-licensed to you under the Universal Permissive License
 * (UPL) 1.0 as shown at https://oss.oracle.com/licenses/upl or Apache License
 * 2.0 as shown at http://www.apache.org/licenses/LICENSE-2.0. You may choose
 * either license.
*/
namespace Oracle\Oci\Common;

use DateTime;
use Exception;
use GuzzleHttp\Exception\BadResponseException;
use InvalidArgumentException;

/**
 * Utilities for making HTTP requests or handling HTTP responses.
 */
class HttpUtils
{
    /**
     * Add a value to a map.
     * 1. If this is the first value with the key, a key-to-value entry is added.
     * 2. If the key already exists and the value is just an object, then the value
     *    is changed to a two-item array, with the old value and the new value in it.
     * 3. If the key already exists and the value is an array, then the new value is added at the end.
     *
     * @param array $queryMap the map to be modified
     * @param string $paramName key name
     * @param string $value value to be added
     */
    public static function addToArray(&$queryMap, $paramName, /*string*/ $value)
    {
        if (array_key_exists($paramName, $queryMap)) {
            $oldValue = $queryMap[$paramName];
            if (is_array($oldValue)) {
                $oldValue[] = $value;
                $queryMap[$paramName] = $oldValue;
            } else {
                $queryMap[$paramName] = [$oldValue, $value];
            }
        } else {
            $queryMap[$paramName] = $value;
        }
    }

    /**
     * Encode an array as a parameter and add it to the map.
     *
     * @param array $queryMap the map to be modified
     * @param string $paramName key name
     * @param array $array the array to be encoded
     * @param string $collectionFormat encoding format (one of 'csv', 'ssv', 'tsv', 'pipes', or 'multi')
     */
    public static function encodeArray(&$queryMap, /*string*/ $paramName, $array, /*string*/ $collectionFormat)
    {
        if ($array == null || count($array) == 0) {
            return;
        }
        $sep = "";
        switch ($collectionFormat) {
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
        if ($collectionFormat == "multi") {
            foreach ($array as $item) {
                HttpUtils::addToArray($queryMap, $paramName, HttpUtils::attemptEncodeParam($item));
            }
        } else {
            $result = "";
            foreach ($array as $item) {
                if (strlen($result) > 0) {
                    $result = $result . $sep;
                }
                $result = $result . HttpUtils::attemptEncodeParam($item);
            }
            HttpUtils::addToArray($queryMap, $paramName, $result);
        }
    }

    /**
     * Encode a map as a parameter and add it to the map. Each item $key => $value in the map will be added to the map
     * as $prefix . $key => $value.
     * @param array $queryMap the map to be modified
     * @param string|null $paramName not used
     * @param string $prefix prefix for the keys
     * @param array|null $map the map with the key-value pairs
     */
    public static function encodeMap(&$queryMap, /*string*/ $paramName, /*?string*/ $prefix, $map)
    {
        if ($prefix == null) {
            $prefix = "";
        }
        if ($map != null) {
            foreach ($map as $key => $value) {
                HttpUtils::encodeMapParamValue($queryMap, $prefix . $key, $value);
            }
        }
    }

    /**
     * Encode a single key-value pair into the map.
     * @param array $queryMap the map to be modified
     * @param string $prefixedKey the key, already prefixed with the common prefix for the map
     * @param mixed $value the value for the key-value pair
     */
    public static function encodeMapParamValue(&$queryMap, /*string*/ $prefixedKey, $value)
    {
        if (is_array($value)) {
            foreach ($value as $item) {
                HttpUtils::addToArray($queryMap, $prefixedKey, HttpUtils::attemptEncodeParam($item));
            }
        } else {
            HttpUtils::addToArray($queryMap, $prefixedKey, HttpUtils::attemptEncodeParam($value));
        }
    }

    /**
     * Encode the passed date-time in OCI format (extended RFC3339).
     * @param DateTime $dateTime the date-time to encode
     * @return string the date-time in OCI format
     */
    public static function encodeDateTime(DateTime $dateTime)
    {
        return $dateTime->format(HttpUtils::$RFC3339_EXTENDED);
    }

    /**
     * Encode the passed value in the correct format.
     * @param DateTime|mixed $value the value to encode
     * @return string the value as string
     */
    public static function attemptEncodeParam($value) // : string
    {
        if ($value instanceof DateTime) {
            return self::encodeDateTime($value);
        }
        return strval($value);
    }

    /**
     * OCI date-time format.
     */
    public static $RFC3339_EXTENDED = "Y-m-d\TH:i:s.uP";

    /**
     * Get the value indicated by the parameter name, or return null if not found.
     * @param array $params the map where to look up the value
     * @param string $paramName name of the parameter
     * @param bool $required if set to true, throws an InvalidArgumentException instead of returning null
     * @return mixed|null the value indicated by the parameter name, or null if not found
     * @throws InvalidArgumentException if the parameter does not exist and $required is true
     */
    public static function orNull($params=[], $paramName, $required = false)
    {
        if (array_key_exists($paramName, $params)) {
            return $params[$paramName];
        }
        if ($required) {
            throw new InvalidArgumentException("The parameter '$paramName' is required");
        }
        return null;
    }

    /**
     * Convert a query map with possibly repeated parameters into a query string.
     * @param array $queryMap the query map
     * @return string query string
     */
    public static function queryMapToString($queryMap) // : string
    {
        // It is not straight-forward to get repeated query parameters to work in the OCI way using Guzzle.
        // Instead of
        //     ?key=value&key=other
        // Guzzle by default produces
        //     ?key[0]=value&key[1]=other
        // Instead, we build our own query string.
        $str = '';
        foreach ($queryMap as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $str .= '&' . $key . '=' . $item;
                }
            } else {
                $str .= '&' . $key . '=' . $value;
            }
        }
        if (strlen($str) > 0) {
            $str[0] = '?';
        }
        return $str;
    }

    /**
     * Process a Guzzle BadResponseException.
     * @param Exception $e
     * @return OciBadResponseException|Exception either an OciBadResponseException (if $e was a Guzzle BadResponseException), or $e unmodified
     */
    public static function processBadResponseException($e)
    {
        // BadResponseException includes 4xx and 5xx exceptions
        if ($e instanceof BadResponseException) {
            $__response = $e->getResponse();
            return new OciBadResponseException($__response);
        }
        // We'll directly throw ConnectException, RequestException (excluding BadResponseException)
        return $e;
    }
}
