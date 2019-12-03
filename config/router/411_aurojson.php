<?php
/**
 * Load the auro json api controller class.
 */
return [
    "routes" => [
        [
            "info" => "Auro JSON API controller.",
            "mount" => "json",
            "handler" => "\Anax\Controller\AuroJsonController",
        ],
    ]
];
