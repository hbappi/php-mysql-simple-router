<?php



class Route
{
    public $childs = array();
    public $middleware = array();
    public $handler = null;
    public $fullpath = null;
    private static $instance = null;
}



class Router
{


    private $childs = array();

    private static $instance = null;



    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();

            $initRoutes = include ABSPATH . "/routes/routes.php";

            call_user_func($initRoutes, self::$instance);
        }
        return self::$instance;
    }



    function getParts($path)
    {
        // $path = "/api/category/paginate";

        $parts = explode('/', trim($path, '/'));

        // print_r($parts);
        return $parts;
    }

    function getSafeDestination($method, $path)
    {

        if (isset($this->childs[$method])) {
            $dest = $this->childs[$method];
        } else if (isset($this->childs['ANY'])) {
            $dest = $this->childs['ANY'];
        }

        if (!isset($dest)) {
            return null;
        }


        $parts = $this->getParts($path);

        foreach ($parts as $part) {
            if (isset($dest->childs[$part])) {
                $dest = $dest->childs[$part];
            } else {
                return null;
            }
        }


        if (!isset($dest->fullpath)) {
            $dest->fullpath = $path;
        }

        return $dest;
    }
    function getVerboseDestination($method, $path)
    {

        if (!isset($this->childs[$method])) {
            $this->childs[$method] = new Route();
        }

        $dest = $this->childs[$method];

        $parts = $this->getParts($path);

        foreach ($parts as $part) {
            if (!isset($dest->childs[$part])) {
                $dest->childs[$part] = new Route();
            }
            $dest = $dest->childs[$part];
        }


        if (!isset($dest->fullpath)) {
            $dest->fullpath = $path;
        }

        return $dest;
    }
    function use($path, $middlewares)
    {
        $dest = $this->getVerboseDestination('POST', $path);
        if (!isset($dest->middleware)) {
            $dest->middleware = array();
        }
        if (!is_array($middlewares)) {
            $middlewares = array($middlewares);
        }

        foreach ($middlewares as $mw) {
            array_push($dest->middleware, $mw);
        }
    }
    function post($path, $handler)
    {
        $dest = $this->getVerboseDestination('POST', $path);

        $dest->handler = $handler;
    }

    function get($path, $handler)
    {
        $dest = $this->getVerboseDestination('GET', $path);

        $dest->handler = $handler;
    }

    function any($path, $handler)
    {
        $dest = $this->getVerboseDestination('ANY', $path);

        $dest->handler = $handler;
    }

    public function dispatch($method, $path)
    {


        $dest = $this->getSafeDestination($method, $path);

        if (!isset($dest)) {
            echo '404 Not Found';
            return;
        }

        $middleware = $dest->middleware;
        $handler = $dest->handler;



        // //check whether route added with or without trailing slash.
        // if (!isset($routes[$method][$path])) {
        //     if (!isset($routes[$method][$path . '/'])) {
        //         echo '404 Not Found';
        //         return;
        //     } else {
        //         $path = $path . '/';
        //     }
        // }


        // $handler = $routes[$method][$path];

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

        AutoLoader::requireFileOnce($fullPath);

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
        // if ($result != null) {
        echo $jsonResponse;
        // }
    }
}
