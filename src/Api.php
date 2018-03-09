<?php
namespace cmal\Api;

use \cmal\Api\Router;
use \cmal\Api\Exception\BaseRouteMatched;
use \cmal\Api\Exception\NoRouteMatched;
use \cmal\Api\Exception\NoSuchBackend;

class Api {
    
    public function __construct($backends, $uri = NULL) {
        $this->backends = $backends;
    
        if (!isset($uri)) {
			$uri = $_SERVER['REQUEST_URI'];
		}

        $routes = [];
        foreach (['GET'] as $method) {
            $routes[$method] = [
				// A backend should probably have some params passed to
            	'/{backend}[/[{args:.+}]]' => [$this, 'process']
			];
        }
        try {
            // Router matched the backend regex
            new Router ($routes, $uri);
        } catch (BaseRouteMatched $e) {
            // Router matched '/'. Check for index backend, then default backend,
            // or raise a BaseRouteMatched Exception
            try {
		        $this->callBackendOrDefault('/', '/');
		    } catch (NoRouteMatched $f) {
		        throw $e;
		    }
        } catch (NoRouteMatched $e) {
            // Route did not match. Only happens when / is reached within backend
            // If the backend doesn't handle it, we certainly won't here
            throw $e;
        }
    }
    
    function toCallable ($recipient, $params = NULL) {
        // Check if the backend is a specific method or a class
        if (is_callable($recipient)) {
            call_user_func($recipient, $params);
        } else {
            // If we don't know about the method to call, call the 'dispatch' method
            call_user_func([$recipient, 'dispatch'], $params);
        }
    }
    
    function callBackendIfExists( $backend, $params = NULL) {
        if (!array_key_exists ($backend, $this->backends)) {
            throw new NoSuchBackend();
        }
        
        $this->toCallable( $this->backends[$backend], $params );
    }
    
    function callBackend ( $backend, $params = NULL ) {
        $this->callBackendIfExists( $backend, $params);
    }
    
    function callBackendOrDefault ( $backend, $params = NULL) {
        try {
            $this->callBackend( $backend, $params);
        } catch (BaseRouteMatched $e) {
            // If base route within backend didn't match, we want NoRouteMatched
            throw new NoRouteMatched();
        } catch (NoSuchBackend $e) {
            // If no backend matched, we want to try then default backend
            // But we need to give it back info about the 'backend' part of the URL
            if ($backend != '/') { $params = '/' . $backend . $params; }
            $this->callBackend( '@', $params);
        }
    }

    function process($params) {
        $backend = $params['backend'];
        
		// We stripped the leading '/' to match the route in __construct
		// So now we need to put it back in place
		$params['args'] = '/' . (empty($params['args']) ? '' : $params['args']);

        $this->callBackendOrDefault( $backend, $params['args'] );
    }
}
