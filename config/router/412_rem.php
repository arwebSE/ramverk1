<?php
/**
 * Controller for the REM server.
 */
return [
    "mount" => "rem",
    "routes" => [
        [
            "info" => "Get a dataset from the REM server.",
            "method" => "get",
            "path" => "{dataset:alphanum}",
            "handler" => ["\Anax\RemServer\RemServerController", "getDataset"],
        ],
        [
            "info" => "Get an item from the REM server.",
            "method" => "get",
            "path" => "{dataset:alphanum}/{id:digit}",
            "handler" => ["\Anax\RemServer\RemServerController", "getItem"],
        ],
        [
            "info" => "Post/add an item in the REM server.",
            "method" => "post",
            "path" => "{dataset:alphanum}",
            "handler" => ["\Anax\RemServer\RemServerController", "postItem"],
        ],
        [
            "info" => "REM server with REST JSON API.",
            "handler" => "\Anax\RemServer\RemServerController",
        ],
    ]
];
