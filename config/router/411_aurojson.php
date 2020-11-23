<?php
/**
 * Load the auro json api controller class.
 */
return [
    "routes" => [
        [
            "info" => "Auro JSON API controller.",
            "mount" => "dev/json",
            "handler" => "\Anax\Controller\AuroJsonController",
        ],
    ]
];
