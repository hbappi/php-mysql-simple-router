<?php


class HeadController extends Controller
{

    public function method($req, \Response $res)
    {
        // echo 'hi ============================================';
        $res->end();
    }
}

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

        if (isset($dest) && is_array($dest->middleware)) {
            $middlewares =  $dest->middleware;
        }

        return $middlewares;
    }


    function getMiddlewares($path)
    {

        $middlewares = array();

        $method = 'MW';

        if (isset($this->childs[$method])) {
            $dest = $this->childs[$method];
        }

        if (!isset($dest)) {
            return $middlewares;
        }


        $parts = $this->getParts($path);

        // return $middlewares;

        foreach ($parts as $part) {
            // if (!isset($dest->childs[$part])) {
            //     $dest =  null;
            //     $middlewares = array();
            //     break;
            // }

            if (isset($dest) && isset($dest->childs[$part])) {

                $dest = $dest->childs[$part];
                $middlewares = array_merge($middlewares,  $this->getMiddlewaresFromDestination($dest));
            }
        }


        return $middlewares;
    }
    function getSafeDestination($method, $path)
    {


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
                // $middlewares = array();
                break;
            }

            $dest = $dest->childs[$part];
            // $middlewares = array_merge($middlewares, $this->getMiddlewaresFromDestination($dest));
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
                // $middlewares = array_merge($middlewares, $this->getMiddlewaresFromDestination($dest));
            }
        }



        // if (isset($dest)) {

        //     // $dest->mws = $middlewares;

        //     if (!isset($dest->fullpath)) {
        //         $dest->fullpath = $path;
        //     }
        // }




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
        $dest = $this->getVerboseDestination('MW', $path);
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

    public function getClassNameAfterImport($parentPath, $classPath, $res)
    {

        $parts = explode('/', $classPath);
        // Get the last part
        $className = end($parts);

        $fullPath =  __DIR__ . '/../' . $parentPath . '/' . $classPath . '.php';

        if (!file_exists($fullPath)) {
            return null;
            // $res->setStatusCode(404)
            //     ->setBody("404  $fullPath file not found")
            //     ->end();
            // exit;
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

        // echo $method;

        if ($method == "HEAD") {
            $dest = new Route();
            $dest->handler = [HeadController::class, 'method'];
        } else {
            $dest = $this->getSafeDestination($method, $path);
        }



        if ((!isset($dest) || !is_array($dest->handler))) {
            $response->setStatusCode(404)
                ->setBody("404 : '$method:$path' Path Not Found")
                ->end();

            exit;
        }


        //run middlewares................................................................

        // return null;

        // $this->mws = $dest->mws;
        $this->mws = $this->getMiddlewares($path);
        $this->handler = $dest->handler;

        if (count($this->handler) != 2) {

            $response->setStatusCode(500)
                ->setBody('500 Internal Server Error')
                ->end();

            exit;
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

            $controllerClass = class_exists($controllerClassPath) ? $controllerClassPath : $this->getClassNameAfterImport(
                'controllers',
                $controllerClassPath,
                $res
            );

            // Using the autoloader to dynamically load the controller class
            if (!class_exists($controllerClass)) {

                $res->setStatusCode(404)
                    ->setBody('404 Controller Not Found')
                    ->end();


                exit;
            }

            $controllerInstance = new $controllerClass();

            if (!method_exists($controllerInstance, $methodName)) {

                $res->setStatusCode(404)
                    ->setBody('404 Method Not Found')
                    ->end();


                exit;
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
            $mwClassPath,
            $res
        );

        $mw = new $mwClass();

        if (!method_exists($mw, 'handle')) {

            $res->setStatusCode(500)
                ->setBody('500. Internal Server Error : Invalid Middleware.')
                ->end();
            exit;
        }


        $mw->handle($req, $res, function ($req, $res) {
            $this->next($req, $res);
        });
    }
}
