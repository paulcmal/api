<?php

namespace cmal\Api;

class Backend {
    function dispatch($args, $routes) {
		new Router($routes, $args);
    }
}

?>
