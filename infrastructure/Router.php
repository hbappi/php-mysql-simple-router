<?php



class Route
{
    public $mws = array(); // this will be used only to store all middleware while dispatching...
    public $childs = array();
    public $middleware = array();
    public $handler = null;
    public $fullpath = null;
}



class Router
{


    private $childs = array();

    private static $instance = null;

    private $mws = array();
    private $handler = null;



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

        $path = '-' . $path;

        $parts = explode('/', trim($path, '/'));
        // echo json_encode($parts);
        // $parts = explode('/', $path);

        // print_r($parts);
        return $parts;
    }


    function getMiddlewaresFromDestination($dest)
    {
        $middlewares = array();

        if (is_array($dest->middleware)) {
            $middlewares =  $dest->middleware;
        }

        return $middlewares;
    }

    function getSafeDestination($method, $path)
    {

        $middlewares = array();

        if (isset($this->childs[$method])) {
            $dest = $this->childs[$method];
        } else if (isset($this->childs['ANY'])) {
            $method = 'ANY';
            $dest = $this->childs['ANY'];
        }

        if (!isset($dest)) {
            return null;
        }


        $parts = $this->getParts($path);

        foreach ($parts as $part) {
            if (!isset($dest->childs[$part])) {
                $dest =  null;
                $middlewares = array();
                break;
            }

            $dest = $dest->childs[$part];
            $middlewares = array_merge($middlewares, $this->getMiddlewaresFromDestination($dest));
        }


        if (!isset($dest) && $method != 'ANY') {

            if (isset($this->childs['ANY'])) {
                $method = 'ANY';
                $dest = $this->childs['ANY'];
            }

            if (!isset($dest)) {
                return null;
            }


            $parts = $this->getParts($path);

            foreach ($parts as $part) {
                if (!isset($dest->childs[$part])) {
                    $dest =  null;
                    break;
                }

                $dest = $dest->childs[$part];
                $middlewares = array_merge($middlewares, $this->getMiddlewaresFromDestination($dest));
            }
        }



        if (isset($dest)) {

            $dest->mws = $middlewares;

            if (!isset($dest->fullpath)) {
                $dest->fullpath = $path;
            }
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

    public function getClassNameAfterImport($parentPath, $classPath)
    {

        $parts = explode('/', $classPath);
        // Get the last part
        $className = end($parts);

        $fullPath =  __DIR__ . '/../' . $parentPath . '/' . $classPath . '.php';

        if (!file_exists($fullPath)) {
            echo "404  $fullPath file not found";
            return;
        }

        AutoLoader::requireFileOnce($fullPath);

        return $className;
    }
    public function dispatch()
    {

        //init request and response object;
        $request = new Request();
        $response = new Response();

        $method = $request->method;
        $path = $request->url;

        $dest = $this->getSafeDestination($method, $path);

        if (!isset($dest) || !is_array($dest->handler)) {
            echo "404 : '$method:$path' Path Not Found";
            return;
        }



        // echo 'middleware found : ' . json_encode($mws);


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

        //run middlewares................................................................




        $this->mws = $dest->mws;
        $this->handler = $dest->handler;


        if (count($this->handler) != 2) {
            echo '500 Internal Server Error';
            return;
        }



        $this->next($request, $response);


        // $controllerInstance->$methodName($request, $response);
        // }
    }

    function next($req, $res)
    {


        if (count($this->mws) == 0) {



            // $handler now contains ['HomeController', 'index']
            $controllerClassPath = $this->handler[0];
            // $controllerClass = $handler[0];
            $methodName = $this->handler[1];

            $controllerClass = $this->getClassNameAfterImport(
                'controllers',
                $controllerClassPath
            );

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

            $controllerInstance->$methodName($req, $res);

            return;
        }

        // if (!is_callable($this->mws[0])) {
        //     echo '500 Internal Server Error';
        //     echo json_encode($this->mws);
        //     return;
        // }

        $mwClassPath = array_shift($this->mws);


        $mwClass = $this->getClassNameAfterImport(
            'middlewares',
            $mwClassPath
        );

        $mw = new $mwClass();

        if (!method_exists($mw, 'handle')) {
            echo '500. Internal Server Error : Invalid Middleware.';
            return;
        }


        $mw->handle($req, $res, function ($req, $res) {
            $this->next($req, $res);
        });
    }
}
