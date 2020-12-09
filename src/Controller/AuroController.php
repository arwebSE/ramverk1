<?php
namespace Anax\Controller;

use Anax\Commons\ContainerInjectableInterface;
use Anax\Commons\ContainerInjectableTrait;

use Anax\GeoIP\GeoIP;

/**
 * A test controller to show off redirect.
 */
class AuroController implements ContainerInjectableInterface
{
    use ContainerInjectableTrait;

    private $adress = null;

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

    public function validateIP($adress = null)
    {
        /**
         * A function to validate IP adress from GET request.
         *
         * @return string as response.
         */

        $valid = false;
        $result = false;
        $version = null;
        $hostname = null;

        if ($adress != null) {
            if ($this->validIP($adress)) {
                $valid = true;
                $hostname = gethostbyaddr($adress);
                $version = $this->IPVersion($adress);
            }
            $result = '<span style="color:' . ($valid ? "green" : "darkred") . '"><strong>' . $adress . '</strong> is ' .
                ($valid ? 'a valid <strong>' . $version . '</strong> adress.</span><br>Hostname: '
                . $hostname : "not a valid IP adress.</span>");
        } return $result;
    }

    public function indexAction()
    {
        $mount = $mount ?? null;
        $client = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];

        if (isset($_GET["ip"])) {
            $this->adress = $_GET["ip"];
        }

        $data = ["content" => '
            <h1>HTML IP Validator</h1>
            <p>Use the input field to check if given IP adress is valid.</p>
            <form action="' . $mount . 'auro" method="get">
                IP adress:
                <input type="text" name="ip" required>
                <input type="submit" value="Submit">
            </form>
            <a href="' . $mount . 'auro?ip=' . $client . '">Check using your own IP adress.</a><br>
            <a href="' . $mount . 'auro?ip=8.8.4.4">Or using an example <strong>IPv4</strong> adress.</a><br>
            <a href="' . $mount . 'auro?ip=2001:4860:4860::8888">
                Or using an example <strong>IPv6</strong> adress.
            </a>
        '];

        $resultData = ["content" => '
            <h1>JSON API Validator</h1>
            <p>Use the links below to try the JSON API or manipulate the URL to validate any IP adress.</p>
            <a href="' . $mount . 'json?ip=' . $client . '">Validate using JSON API with your own IP adress.</a><br>
            <a href="' . $mount . 'json?ip=8.8.4.4">Or using an example <strong>IPv4</strong> adress.</a><br>
            <a href="' . $mount . 'json?ip=2001:4860:4860::8888">Or using an example <strong>IPv6</strong> adress.</a>
        '];

        $page = $this->di->get("page");

        $page->add("anax/v2/article/default", $data);
        $page->add("anax/v2/article/default", $resultData, "sidebar-right");
        $page->add("anax/v2/article/default", ["content" => $this->validateIP($this->adress)]);
        $page->add("anax/v2/article/default", ["content" => $this->getGeoByIP($this->adress)]);


        return $page->render([
            "title" => "IP Validator",
            "baseTitle" => " | Auro Dev"
        ]);
    }

    public function getGeoByIP($adress = null)
    {
        $result = "";
        if ($adress != null) {
            if ($this->validIP($adress)) {
                $geoContainer = new GeoIP();
                $geoInfo = $geoContainer->getInfoByIP($adress);

                $lat = $geoInfo["latitude"];
                $long = $geoInfo["longitude"];
                $bbox1lat = strval(floatval($lat)-1);
                $bbox1long = strval(floatval($long)-1);
                $bbox2lat = strval(floatval($lat)+1);
                $bbox2long = strval(floatval($long)+1);

                $result = '
                    <h1>GeoIP Info</h1>
                    <p><strong>Continent:</strong> ' . $geoInfo["continent_name"] . '</p>
                    <p><strong>Country:</strong> ' . $geoInfo["country_name"] .
                    ' <img style="max-height:20px;" alt="' .
                    $geoInfo["country_name"] . " flag" . '" src="' .
                    $geoInfo["location"]["country_flag"] . '" /></p>
                    <p><strong>City:</strong> ' . $geoInfo["city"] . '</p>
                    <p><strong>Zip Code:</strong> ' . $geoInfo["zip"] . '</p>
                    <iframe
                        width="425"
                        height="350"
                        frameborder="0
                        scrolling="no"
                        marginheight="0"
                        marginwidth="0"
                        src="https://www.openstreetmap.org/export/embed.html?bbox=' .
                        $bbox1long . "%2C" .
                        $bbox1lat . "%2C" .
                        $bbox2long . "%2C" .
                        $bbox2lat . '&amp;layer=mapnik&amp;marker=' .
                        $lat . "%2C" .
                        $long . '" style="border: 1px solid black">
                    </iframe>
                    <br/><small><a href="https://www.openstreetmap.org/#map=9/' . $lat . '/'. $long . '">View Larger Map</a></small>
                ';
            }
        } return $result;
    }
}
