<?php

use Oracle\Oci\Common\ExcludeBodySigningStrategy;
use Oracle\Oci\Common\FederationSigningStrategy;
use Oracle\Oci\Common\ObjectStorageSigningStrategy;
use Oracle\Oci\Common\StandardSigningStrategy;
use PHPUnit\Framework\TestCase;

use function Oracle\Oci\Common\getSigningStrategy;

class SigningStrategyTest extends TestCase
{
    public function testGetSigningStrategy()
    {
        $this->assertEquals(StandardSigningStrategy::getSingleton(), getSigningStrategy("standard"));
        $this->assertEquals(StandardSigningStrategy::getSingleton(), getSigningStrategy("STANDARD"));
        $this->assertEquals(StandardSigningStrategy::getSingleton(), getSigningStrategy("Standard"));

        $this->assertEquals(ExcludeBodySigningStrategy::getSingleton(), getSigningStrategy("exclude_body"));
        $this->assertEquals(ObjectStorageSigningStrategy::getSingleton(), getSigningStrategy("object_storage"));
        $this->assertEquals(FederationSigningStrategy::getSingleton(), getSigningStrategy("federation"));

        try {
            $this->assertEquals(FederationSigningStrategy::getSingleton(), getSigningStrategy("bogey"));
            $this->fail("Should have thrown");
        } catch (InvalidArgumentException $iae) {
            // expected
        } catch (Exception $e) {
            $this->fail("Should have thrown an InvalidArgumentException");
        }
    }

    public function testFederationPost()
    {
        $ss = getSigningStrategy("federation");

        $requiredHeaders = $ss->getRequiredSigningHeaders("post");

        $this->assertFalse(array_search("host", $requiredHeaders) !== false);
    }

    public function testObjectStoragePut()
    {
        $ss = getSigningStrategy("object_storage");

        $requiredHeaders = $ss->getRequiredSigningHeaders("put");

        $this->assertTrue(array_search("host", $requiredHeaders) !== false);
        $this->assertFalse(array_search("content-length", $requiredHeaders) !== false);
        $this->assertFalse(array_search("content-type", $requiredHeaders) !== false);
        $this->assertFalse(array_search("x-content-sha256", $requiredHeaders) !== false);
    }

    public function testStandardPut()
    {
        $ss = getSigningStrategy("standard");

        $requiredHeaders = $ss->getRequiredSigningHeaders("put");

        $this->assertTrue(array_search("host", $requiredHeaders) !== false);
        $this->assertTrue(array_search("content-length", $requiredHeaders) !== false);
        $this->assertTrue(array_search("content-type", $requiredHeaders) !== false);
        $this->assertTrue(array_search("x-content-sha256", $requiredHeaders) !== false);
    }

    public function testExcludeBodyPut()
    {
        $ss = getSigningStrategy("exclude_body");

        $requiredHeaders = $ss->getRequiredSigningHeaders("put");

        $this->assertTrue(array_search("host", $requiredHeaders) !== false);
        $this->assertFalse(array_search("content-length", $requiredHeaders) !== false);
        $this->assertFalse(array_search("content-type", $requiredHeaders) !== false);
        $this->assertFalse(array_search("x-content-sha256", $requiredHeaders) !== false);
    }

    public function testObjectStoragePost()
    {
        $ss = getSigningStrategy("object_storage");

        $requiredHeaders = $ss->getRequiredSigningHeaders("post");

        $this->assertTrue(array_search("host", $requiredHeaders) !== false);
        $this->assertTrue(array_search("content-length", $requiredHeaders) !== false);
        $this->assertTrue(array_search("content-type", $requiredHeaders) !== false);
        $this->assertTrue(array_search("x-content-sha256", $requiredHeaders) !== false);
    }

    public function testStandardPost()
    {
        $ss = getSigningStrategy("standard");

        $requiredHeaders = $ss->getRequiredSigningHeaders("post");

        $this->assertTrue(array_search("host", $requiredHeaders) !== false);
        $this->assertTrue(array_search("content-length", $requiredHeaders) !== false);
        $this->assertTrue(array_search("content-type", $requiredHeaders) !== false);
        $this->assertTrue(array_search("x-content-sha256", $requiredHeaders) !== false);
    }

    public function testExcludeBodyPost()
    {
        $ss = getSigningStrategy("exclude_body");

        $requiredHeaders = $ss->getRequiredSigningHeaders("post");

        $this->assertTrue(array_search("host", $requiredHeaders) !== false);
        $this->assertFalse(array_search("content-length", $requiredHeaders) !== false);
        $this->assertFalse(array_search("content-type", $requiredHeaders) !== false);
        $this->assertFalse(array_search("x-content-sha256", $requiredHeaders) !== false);
    }
}
