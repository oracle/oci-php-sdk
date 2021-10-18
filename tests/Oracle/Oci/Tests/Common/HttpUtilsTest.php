<?php

use PHPUnit\Framework\TestCase;
use Oracle\Oci\Common\HttpUtils;

class HttpUtilsTest extends TestCase
{
    public function testAttemptEncodeQueryParam()
    {
        $this->assertEquals("abc", HttpUtils::attemptEncodeQueryParam("abc"));
        
        // TODO: check if this is required, or if Guzzle escapes it
        // $this->assertEquals("%251%253D%253F%2540%255B%255D%2541%20aaaa", HttpUtils::attemptEncodeQueryParam("%1%3D%3F%40%5B%5D%41 aaaa"));

        $this->assertEquals("1", HttpUtils::attemptEncodeQueryParam(1));

        $dt = new DateTime(); // now
        $this->assertEquals($dt->format(DateTime::RFC3339_EXTENDED), HttpUtils::attemptEncodeQueryParam($dt));
    }

    public function testEncodeArray_null() {
        $array = null;
        $this->assertEquals("", HttpUtils::encodeArray("paramName", $array, "csv"));
        $this->assertEquals("", HttpUtils::encodeArray("paramName", $array, "ssv"));
        $this->assertEquals("", HttpUtils::encodeArray("paramName", $array, "tsv"));
        $this->assertEquals("", HttpUtils::encodeArray("paramName", $array, "pipes"));
        $this->assertEquals("", HttpUtils::encodeArray("paramName", $array, "multi"));
    }

    public function testEncodeArray_empty() {
        $array = [];
        $this->assertEquals("", HttpUtils::encodeArray("paramName", $array, "csv"));
        $this->assertEquals("", HttpUtils::encodeArray("paramName", $array, "ssv"));
        $this->assertEquals("", HttpUtils::encodeArray("paramName", $array, "tsv"));
        $this->assertEquals("", HttpUtils::encodeArray("paramName", $array, "pipes"));
        $this->assertEquals("", HttpUtils::encodeArray("paramName", $array, "multi"));
    }

    public function testEncodeArray_single() {
        $array = ["abc"];
        $this->assertEquals("paramName=abc", HttpUtils::encodeArray("paramName", $array, "csv"));
        $this->assertEquals("paramName=abc", HttpUtils::encodeArray("paramName", $array, "ssv"));
        $this->assertEquals("paramName=abc", HttpUtils::encodeArray("paramName", $array, "tsv"));
        $this->assertEquals("paramName=abc", HttpUtils::encodeArray("paramName", $array, "pipes"));
        $this->assertEquals("paramName=abc", HttpUtils::encodeArray("paramName", $array, "multi"));
    }

    public function testEncodeArray_singleDateTime() {
        $dt = new DateTime(); // now
        $array = [$dt];
        $expected = "paramName=" . $dt->format(DateTime::RFC3339_EXTENDED);
        $this->assertEquals($expected, HttpUtils::encodeArray("paramName", $array, "csv"));
        $this->assertEquals($expected, HttpUtils::encodeArray("paramName", $array, "ssv"));
        $this->assertEquals($expected, HttpUtils::encodeArray("paramName", $array, "tsv"));
        $this->assertEquals($expected, HttpUtils::encodeArray("paramName", $array, "pipes"));
        $this->assertEquals($expected, HttpUtils::encodeArray("paramName", $array, "multi"));
    }

    public function testEncodeArray_multiple() {
        $dt = new DateTime(); // now
        $array = ["abc", $dt, 1];
        $expected = $dt->format(DateTime::RFC3339_EXTENDED);
        $this->assertEquals("paramName=abc,$expected,1", HttpUtils::encodeArray("paramName", $array, "csv"));
        $this->assertEquals("paramName=abc $expected 1", HttpUtils::encodeArray("paramName", $array, "ssv"));
        $this->assertEquals("paramName=abc\t{$expected}\t1", HttpUtils::encodeArray("paramName", $array, "tsv"));
        $this->assertEquals("paramName=abc|$expected|1", HttpUtils::encodeArray("paramName", $array, "pipes"));
        $this->assertEquals("paramName=abc&paramName=$expected&paramName=1", HttpUtils::encodeArray("paramName", $array, "multi"));
    }

    public function testEncodeMap_null() {
        $map = null;
        $this->assertEquals("", HttpUtils::encodeMap("paramName", "prefix.", $map));
    }

    public function testEncodeMap_empty() {
        $map = [];
        $this->assertEquals("", HttpUtils::encodeMap("paramName", "prefix.", $map));
    }

    public function testEncodeMap_single() {
        $map = ["key" => "value"];
        $this->assertEquals("prefix.key=value", HttpUtils::encodeMap("paramName", "prefix.", $map));
    }

    public function testEncodeMap_singleDateTime() {
        $dt = new DateTime(); // now
        $map = ["key" => $dt];
        $expected = $dt->format(DateTime::RFC3339_EXTENDED);
        $this->assertEquals("prefix.key=$expected", HttpUtils::encodeMap("paramName", "prefix.", $map));
    }

    public function testEncodeMap_single_nullPrefix() {
        $map = ["key" => "value"];
        $this->assertEquals("key=value", HttpUtils::encodeMap("paramName", (string) null, $map));
    }

    public function testEncodeMap_singleDateTime_nullPrefix() {
        $dt = new DateTime(); // now
        $map = ["key" => $dt];
        $expected = $dt->format(DateTime::RFC3339_EXTENDED);
        $this->assertEquals("key=$expected", HttpUtils::encodeMap("paramName", (string) null, $map));
    }

    public function testEncodeMap_multiple() {
        $dt = new DateTime(); // now
        $map = [
            "key1" => "abc", 
            "key2" => $dt,
            "key3" => 1,
            "key4" => ["abc", $dt, 1]];
        $expected = $dt->format(DateTime::RFC3339_EXTENDED);
        $this->assertEquals("prefix.key1=abc&prefix.key2=$expected&prefix.key3=1&prefix.key4=abc&prefix.key4=$expected&prefix.key4=1", HttpUtils::encodeMap("paramName", "prefix.", $map));
    }

    public function testEncodeMap_multiple_nullPrefix() {
        $dt = new DateTime(); // now
        $map = [
            "key1" => "abc", 
            "key2" => $dt,
            "key3" => 1,
            "key4" => ["abc", $dt, 1]];
        $expected = $dt->format(DateTime::RFC3339_EXTENDED);
        $this->assertEquals("key1=abc&key2=$expected&key3=1&key4=abc&key4=$expected&key4=1", HttpUtils::encodeMap("paramName", (string) null, $map));
    }
}

?>