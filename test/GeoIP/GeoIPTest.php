<?php

namespace Anax\GeoIP;

use Anax\DI\DIFactoryConfig;
use PHPUnit\Framework\TestCase;

/**
 * Testclass.
 */
class GeoIPTest extends TestCase
{
    /**
     * Test getInfoByIP
     */
    public function testGetInfoByIP()
    {
        $geoIP = new GeoIP();
        $res = $geoIP->getInfoByIP("8.8.8.8");
        $this->assertContains("United States", $res);
    }
}
