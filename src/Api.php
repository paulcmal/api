<?php
namespace cmal\Api;

use \cmal\Api\Router;

class Api {
    
    public function __construct($backends) {
        
        $this->backends = $backends;
        $routes = [];
        foreach (['GET'] as $method) {
            $routes[$method] = [
				// A backend should always have some params passed to
            	'/{backend}[/[{args:.+}]]' => [$this, 'toBackend']
			];
        }
        
        new Router ($routes);
    }

    function toBackend($params) {
        $backend = $params['backend'];
		
		// We stripped the leading '/' to match the route in __construct
		// So now we need to put it back in place
		$params['args'] = '/' . (empty($params['args']) ? '' : $params['args']);

        if (array_key_exists($backend, $this->backends)) {
            // Check if the backend is a specific method or a class
            if (is_callable($this->backends[$backend])) {
                call_user_func($this->backends[$backend], $params['args']);
            } else {
                // If we don't know about the method to call, call the 'dispatch' method
                call_user_func([$this->backends[$backend], 'dispatch'], $params['args']);
            }
        } else {
            die('Backend not found.');
        }
    }
}

?>
