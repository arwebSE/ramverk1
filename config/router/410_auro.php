<?php
/**
 * Load the stylechooser as a controller class.
 */
return [
    "mount" => "dev/auro",
    "routes" => [
        [
            "info" => "Auro controller.",
            "handler" => "\Anax\Controller\AuroController",
        ],
/*         [
            "info" => "Lookup weather by IP.",
            "method" => "get",
            "path" => "ip",
            "handler" => ["\Anax\Controller\AuroController", "getGeoByIP"],
        ],
        [
            "info" => "Lookup weather by coordinates.",
            "method" => "get",
            "path" => "coords",
            "handler" => ["\Anax\Controller\AuroController", "getGeoByCoords"],
        ], */
    ]
];
