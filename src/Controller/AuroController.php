<?php
namespace Anax\Controller;

use Anax\Commons\ContainerInjectableInterface;
use Anax\Commons\ContainerInjectableTrait;

/**
 * A test controller to show off redirect.
 */
class AuroController implements ContainerInjectableInterface
{
    use ContainerInjectableTrait;

    private $adress = null;

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
            if (filter_var($adress, FILTER_VALIDATE_IP)) {
                $valid = true;
                $hostname = gethostbyaddr($adress);
                if (filter_var($adress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                    $version = "IPv4";
                } else {
                    $version = "IPv6";
                }
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

        return $page->render([
            "title" => "IP Validator",
            "baseTitle" => " | Auro Dev"
        ]);
    }
}
