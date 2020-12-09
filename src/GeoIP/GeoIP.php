<?php

namespace Anax\GeoIP;

/**
 * A model class retrievieng data from an external server.
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class GeoIP
{
    public function getInfoByIP(string $adress) : array
    {
        $config = require ANAX_INSTALL_PATH . "/config/api/config.php";
        $geoAPI = $config["geoAPI"];
        $geoURL = $config["geoURL"];

        $url = "$geoURL/$adress?access_key=$geoAPI";

        //  Initiate curl handler
        $ch = curl_init();

        // Will return the response, if false it print the response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Set the url
        curl_setopt($ch, CURLOPT_URL, $url);

        // Execute
        $data = curl_exec($ch);

        // Closing
        curl_close($ch);

        $data = json_decode($data, true);
        return $data;
    }
}
