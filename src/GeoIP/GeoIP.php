<?php

namespace Anax\GeoIP;

/**
 * A model class retrievieng data from an external server.
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class GeoIP
{
    public function getUser(int $userId) : array
    {
        $url = "https://rem.dbwebb.se/api/users/$userId";
        $res = file_get_contents($url);
        $user = json_decode($res, true);
        return $user;
    }

    public function getInfoByIP(string $userIP) : array
    {
        $config = require ANAX_INSTALL_PATH . "/config/api/config.php";
        $geoAPI = $config["geoAPI"];
        $geoURL = $config["geoURL"];

        $url = "$geoURL/$userIP?access_key=$geoAPI";

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

        $userIP = json_decode($data, true);
        return $userIP;
    }

    public function getInfoByCoords(array $userIds) : array
    {
        $url = "https://rem.dbwebb.se/api/users";

        $options = [
            CURLOPT_RETURNTRANSFER => true,
        ];

        // Add all curl handlers and remember them
        // Initiate the multi curl handler
        $mh = curl_multi_init();
        $chAll = [];
        foreach ($userIds as $id) {
            $ch = curl_init("$url/$id");
            curl_setopt_array($ch, $options);
            curl_multi_add_handle($mh, $ch);
            $chAll[] = $ch;
        }

        // Execute all queries simultaneously,
        // and continue when all are complete
        $running = null;
        do {
            curl_multi_exec($mh, $running);
        } while ($running);

        // Close the handles
        foreach ($chAll as $ch) {
            curl_multi_remove_handle($mh, $ch);
        }
        curl_multi_close($mh);

        // All of our requests are done, we can now access the results
        $response = [];
        foreach ($chAll as $ch) {
            $data = curl_multi_getcontent($ch);
            $response[] = json_decode($data, true);
        }

        return $response;
    }
}
