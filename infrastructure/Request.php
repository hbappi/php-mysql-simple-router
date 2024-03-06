<?php

class Request
{
    public $url;
    public $method;
    public $parameters = [];

    public function __construct()
    {
        $this->url = $this->getUrl();
        $this->method = $this->getMethod();
        $this->parameters = $this->getParameters();
    }

    protected function getUrl()
    {
        $url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';

        // Parse the URL
        $urlComponents = parse_url($url);
        // Extract parameters from the query string
        parse_str($urlComponents['query'] ?? '', $queryParams);
        // Now $queryParams contains an associative array of parameters
        // Extract the solid path (path without query parameters)
        $path = $urlComponents['path'] ?? '';

        return  rtrim($path, '/'); // Remove query string from URL
    }

    protected function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getJsonBody()
    {
        $jsonData = array();

        $entityBody = file_get_contents('php://input');

        if (!empty($entityBody)) {
            $jsonData = json_decode($entityBody, true);
        }

        return $jsonData;
    }

    protected function getParameters()
    {
        $parameters = [];
        if ($this->method == 'POST' && !empty($_POST)) {
            $parameters = $_POST;
        } elseif ($this->method == 'GET' && !empty($_GET)) {
            $parameters = $_GET;
        }
        // Add support for JSON payloads
        if ($this->method == 'POST') {
            $json = file_get_contents('php://input');
            if ($json) {
                $parameters = json_decode($json, true);
            }
        }
        return $parameters;
    }
}
