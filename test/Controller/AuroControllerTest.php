<?php

namespace Anax\Controller;

use Anax\DI\DIFactoryConfig;
use PHPUnit\Framework\TestCase;

/**
 * Test the AuroController.
 */
class AuroControllerTest extends TestCase
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
        $this->controller = new AuroController();
        $this->controller->setDI($this->di);

        $_SERVER['REMOTE_ADDR'] = "127.0.0.1";
    }

    /**
     * Test the function validateIP.
     */
    public function testValidateIPv4()
    {
        $res = $this->controller->validateIP("127.0.0.1");

        $this->assertContains("127.0.0.1", $res);
        $this->assertContains("IPv4", $res);
        $this->assertContains("is a valid", $res);
    }

    /**
     * Test the function validateIP.
     */
    public function testValidateIPv6()
    {
        $res = $this->controller->validateIP("2001:4860:4860::8888");

        $this->assertContains("2001:4860:4860::8888", $res);
        $this->assertContains("IPv6", $res);
        $this->assertContains("is a valid", $res);
    }

    /**
     * Fail test the function validateIP.
     */
    public function testValidateIPFail()
    {
        $res = $this->controller->validateIP("123");
        $this->assertContains("not a valid", $res);
    }


    /**
     * Test the route "index".
     */
    public function testIndexAction()
    {
        $_GET["ip"] = "8.8.8.8";

        $res = $this->controller->indexAction();
        $this->assertInstanceOf("\Anax\Response\Response", $res);

        $body = $res->getBody();
        $exp = "<strong>8.8.8.8</strong> is a valid <strong>IPv4</strong> adress.</span>";
        $this->assertContains($exp, $body);
    }
}
