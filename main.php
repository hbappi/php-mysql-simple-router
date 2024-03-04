<?php

require_once dirname(__FILE__) . "/infrastructure/Db.php";
require_once __DIR__ . "/infrastructure/Router.php";


if (isset($_SERVER['REQUEST_URI'])) {
    $url = $_SERVER['REQUEST_URI'];
    // Parse the URL
    $urlComponents = parse_url($url);
    // Extract parameters from the query string
    parse_str($urlComponents['query'] ?? '', $queryParams);
    // Now $queryParams contains an associative array of parameters
    // Extract the solid path (path without query parameters)
    $path = $urlComponents['path'] ?? '';
    (new Router())->dispatch($_SERVER['REQUEST_METHOD'], rtrim($path, '/'));
} else {
    // Handle the case where the requested path is not set
    echo "Unable to determine requested path\n";
}
