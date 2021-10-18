<?php

use PHPUnit\Framework\TestCase;
use Oracle\Oci\Common\Realm;

class RegionsTest extends TestCase
{
    public function testRealms()
    {
        $this->assertEquals("oc1", Realm::getRealm("oc1"));
        $this->assertEquals("oc1", Realm::getRealm("OC1"));
        $this->assertEquals(Realm::getRealm("oc1"), Realm::getRealm("oC1"));
    }
}

?>