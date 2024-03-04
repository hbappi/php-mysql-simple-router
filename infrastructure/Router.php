<?php

require_once(__DIR__ . "/AutoLoader.php");

class Router
{

    public function dispatch($method, $path)
    {

        $routes = include __DIR__ . "/../routes/routes.php";


        //check whether route added with or without trailing slash.
        if (!isset($routes[$method][$path])) {
            if (!isset($routes[$method][$path . '/'])) {
                echo '404 Not Found';
                return;
            } else {
                $path = $path . '/';
            }
        }


        $handler = $routes[$method][$path];

        // $handler = $routes['GET']['/test'];

        if (!is_array($handler) || count($handler) != 2) {
            echo '500 Internal Server Error';
            return;
        }


        // $handler now contains ['HomeController', 'index']
        $controllerClassPath = $handler[0];
        // $controllerClass = $handler[0];
        $methodName = $handler[1];

        $parts = explode('/', $controllerClassPath);
        // Get the last part
        $controllerClass = end($parts);

        $fullPath =  __DIR__ . '/../controllers/' . $controllerClassPath . '.php';

        if (!file_exists($fullPath)) {
            echo "404  $fullPath file not found";
            return;
        }

        requireFileOnce($fullPath);

        // Using the autoloader to dynamically load the controller class
        if (!class_exists($controllerClass)) {
            echo '404 Controller Not Found';
            return;
        }

        $controllerInstance = new $controllerClass();

        if (!method_exists($controllerInstance, $methodName)) {
            echo '404 Method Not Found';
            return;
        }

        $result =  $controllerInstance->$methodName();

        // Convert the array to JSON
        $jsonResponse = json_encode($result);
        // Set HTTP headers to indicate JSON response
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *'); // Adjust the CORS headers as needed
        // Output the JSON response
        echo $jsonResponse;
    }
}
