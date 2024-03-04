<?php

define('ABSPATH', __DIR__);

require_once ABSPATH . "/infrastructure/RequireAll.php";


if (isset($_SERVER['REQUEST_URI'])) {
    $url = $_SERVER['REQUEST_URI'];
    // Parse the URL
    $urlComponents = parse_url($url);
    // Extract parameters from the query string
    parse_str($urlComponents['query'] ?? '', $queryParams);
    // Now $queryParams contains an associative array of parameters
    // Extract the solid path (path without query parameters)
    $path = $urlComponents['path'] ?? '';
    Router::getInstance()->dispatch($_SERVER['REQUEST_METHOD'], rtrim($path, '/'));
} else {
    // Handle the case where the requested path is not set
    echo "Unable to determine requested path\n";
}
