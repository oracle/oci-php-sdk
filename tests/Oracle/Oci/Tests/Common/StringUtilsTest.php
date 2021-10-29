<?php

namespace Oracle\Oci\Common;

use PHPUnit\Framework\TestCase;

class StringUtilsTest extends TestCase
{
    public function testBase64Url_RoundTrip()
    {
        $input = hex2bin("fae789d2af75b795ce9c560ff1769cdc") .
        "How do you like to go up in a swing? Up in the air so blue?\n" .
        "by Robert Louis Stevenson";
        $b64url = StringUtils::base64url_encode($input);
        $output = StringUtils::base64url_decode($b64url, true);
        $this->assertEquals($input, $output);
        $this->assertNotContains("/", $b64url);
        $this->assertNotContains("+", $b64url);

        $b64 = base64_encode($input);
        $this->assertContains("/", $b64);
        $this->assertContains("+", $b64);
    }

    public function testBase64_RoundTrip()
    {
        $input = "Night time shows us where they are.";
        $b64 = base64_encode($input);
        $output = base64_decode($b64, true);
        $this->assertEquals($input, $output);
    }
}
