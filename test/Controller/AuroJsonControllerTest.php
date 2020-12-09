<?php

namespace Anax\Controller;

use Anax\DI\DIFactoryConfig;
use PHPUnit\Framework\TestCase;

/**
 * Test the AuroJsonController.
 */
class AuroJsonControllerTest extends TestCase
{
    // Create the di container.
    protected $di;
    protected $controller;

    /**
     * Prepare before each test.
     */
    protected function setUp()
    {
        global $di;

        // Setup di
        $this->di = new DIFactoryConfig();
        $this->di->loadServices(ANAX_INSTALL_PATH . "/config/di");

        // Use a different cache dir for unit test
        $this->di->get("cache")->setPath(ANAX_INSTALL_PATH . "/test/cache");

        // View helpers uses the global $di so it needs its value
        $di = $this->di;

        // Setup the controller
        $this->controller = new AuroJsonController();
        $this->controller->setDI($this->di);
    }

    /**
     * Test the route "index".
     */
    public function testIndexActionIPv4()
    {
        $_GET["ip"] = "8.8.8.8";
        $this->controller->initialize();

        $res = $this->controller->indexActionGet();
        $this->assertInternalType("array", $res);

        $json = $res[0];
        $json = json_decode($json, true);

        $this->assertEquals("8.8.8.8", $json["ip"]);
        $this->assertEquals(true, $json["valid"]);
        $this->assertEquals("IPv4", $json["version"]);
        $this->assertEquals("dns.google", $json["hostname"]);
        $this->assertEquals("United States", $json["country"]);
        $this->assertEquals("Mountain View", $json["city"]);
    }

    /**
     * Test the route "index" with IPv6.
     */
    public function testIndexActionIPv6()
    {
        $_GET["ip"] = "2001:4860:4860::8888";
        $this->controller->initialize();

        $res = $this->controller->indexActionGet();
        $this->assertInternalType("array", $res);

        $json = $res[0];
        $json = json_decode($json, true);

        $this->assertEquals("2001:4860:4860::8888", $json["ip"]);
        $this->assertEquals(true, $json["valid"]);
        $this->assertEquals("IPv6", $json["version"]);
        $this->assertEquals("dns.google", $json["hostname"]);
    }

    /**
     * Test the route "index".
     */
    public function testIndexActionFail()
    {
        $_GET["ip"] = "123";
        $this->controller->initialize();

        $res = $this->controller->indexActionGet();
        $this->assertInternalType("array", $res);

        $json = $res[0];
        $json = json_decode($json, true);

        $this->assertEquals("123", $json["ip"]);
        $this->assertEquals(false, $json["valid"]);
        $this->assertEquals(null, $json["version"]);
        $this->assertEquals(null, $json["hostname"]);
    }
}
