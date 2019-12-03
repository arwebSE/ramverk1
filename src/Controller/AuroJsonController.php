<?php
namespace Anax\Controller;

use Anax\Commons\ContainerInjectableInterface;
use Anax\Commons\ContainerInjectableTrait;

/**
 * Auro JSON API controller.
 */
class AuroJsonController implements ContainerInjectableInterface
{
    use ContainerInjectableTrait;

    private $adress = null;

    /**
     * Initialize method
     *
     * @return void
     */
    public function initialize() : void
    {
        if (isset($_GET["ip"])) {
            $this->adress = $_GET["ip"];
        }
    }

    /**
     * Index method action
     *
     * @return array
     */
    public function indexActionGet() : array
    {
        $adress = null;
        $valid = false;
        $version = null;
        $hostname = null;

        if ($this->adress) {
            $adress = $this->adress;
            if (filter_var($adress, FILTER_VALIDATE_IP)) {
                $valid = true;
                $hostname = gethostbyaddr($adress);
                if (filter_var($adress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                    $version = "IPv4";
                } else {
                    $version = "IPv6";
                }
            }
        }

        $json = [
            "ip" => $adress,
            "valid" => $valid,
            "version" => $version,
            "hostname" => $hostname
        ];

        return [$json];
    }
}
