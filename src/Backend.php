<?php

namespace cmal\Api;

use \cmal\Api\Exception\BaseRouteMatched;
use \cmal\Api\Exception\NoRouteMatched;

class Backend {
    function dispatch($args, $routes) {
        try {
		    new Router($routes, $args);
		} catch (BaseRouteMatched $e) {
		    throw new NoRouteMatched();
		}
    }
}
