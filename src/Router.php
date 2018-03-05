<?php
namespace cmal\Api;

require __DIR__ . '/vendor/autoload.php';

use \FastRoute;
use \cmal\Api\Api;

class Router {

    public function __construct($routes, $uri = NULL) {
		if (!isset($uri)) {
			$uri = $_SERVER['REQUEST_URI'];
		}

        $this->routes = $routes;
        $dispatcher = $this->registerRoutes();
        $this->dispatchRoutes($dispatcher, $uri);
    }

    public function registerRoutes()  {
        $dispatcher = \FastRoute\simpleDispatcher(function(\FastRoute\RouteCollector $r) {
            foreach($this->routes as $verb => $verbRoutes) {
                foreach($verbRoutes as $route => $action) {
					//echo $verb . $route . $action;
                    $r->addRoute($verb, $route, $action);
                }
            }
        });
        return $dispatcher;
    }

     public function dispatchRoutes($dispatcher, $uri) {
        $httpMethod = $_SERVER['REQUEST_METHOD'];

        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
        switch ($routeInfo[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                echo '404';
                break;
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                echo 'HTTP Method not allowed';
                break;
            case \FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                //call_user_func($handlers)
                $handler($vars);
                break;
        }
    }
}

?>
