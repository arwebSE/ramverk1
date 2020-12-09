<?php
namespace Anax\Controller;

use Anax\Commons\ContainerInjectableInterface;
use Anax\Commons\ContainerInjectableTrait;

use Anax\GeoIP\GeoIP;

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

    public function validIP($adress = null)
    {
        /**
         * A function to validate IP adress from GET request.
         *
         * @return boolean as response.
         */
        if (filter_var($adress, FILTER_VALIDATE_IP)) {
            return true;
        } else {
            return false;
        }
    }

    public function IPVersion($adress = null)
    {
        /**
         * Returns IP version
         *
         * @return string as response.
         */
        if (filter_var($adress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return "IPv4";
        } else {
            return "IPv6";
        }
    }

    public function getGeoByIP($adress = null)
    {
        $result = "";
        if ($adress != null) {
            if ($this->validIP($adress)) {
                $geoContainer = new GeoIP();
                $result = $geoContainer->getInfoByIP($adress);
            }
        } return $result;
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
        $geo = null;
        $maplink = null;

        if ($this->adress) {
            $adress = $this->adress;
            if ($this->validIP($adress)) {
                $valid = true;
                $hostname = gethostbyaddr($adress);
                $version = $this->IPVersion($adress);
                $geo = $this->getGeoByIP($adress);
                $lat = $geo["latitude"];
                $long = $geo["longitude"];
                $maplink = 'https://www.openstreetmap.org/' .
                '?mlat=' . $lat . '&mlon=' . $long .
                '#map=10/' . $lat . '/' . $long;
            }
        }

        $json = [
            "ip" => $adress,
            "valid" => $valid,
            "version" => $version,
            "hostname" => $hostname,
            "continent" => $geo["continent_name"],
            "country" => $geo["country_name"],
            "city" => $geo["city"],
            "zip" => $geo["zip"],
            "maplink" => $maplink
        ];

        $json = json_encode($json, JSON_UNESCAPED_UNICODE);

        return [$json];
    }
}
