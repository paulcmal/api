# fastsubroute

fastsubroute is a lightweight [fastroute](https://github.com/nikic/fastroute) wrapper to manage subroutes. That is, you can let different modules handle different part of the routes separately, without having to register all your routes centrally.

With fastsubroute, you can deploy a small API within minutes!

## Features

This library is nothing extraordinary. It was born out of the boredom of every time writing the same routing lines, for the same kind of setup. It's voluntarily minimal:

- [x] simple and verboseless routing with `Router`
- [x] subrouting support with `Api`
- [x] [fastroute syntax](https://github.com/nikic/FastRoute#defining-routes) for routes

## Using it

The routes are defined following [fastroute syntax](https://github.com/nikic/FastRoute#defining-routes) in an array.

When passing routes as a parameter, we're expecting a dictionary of methods pointing to a dictionary of routes pointing to [callables](https://secure.php.net/manual/en/language.types.callable.php). Like this:

```
$routes = [
	'GET' => [
		'/foo/{param}' => callable,
		'/bar/{param1}/param2' => callable, 
	],
];
```

### Router

Router is a simple fastroute wrapper for less verbosity. It's used like this:

```
new Router ($routes);
```

The Router constructor can also accept as a second parameter the URL to be routed to handle subroutes (portions of URLs).

```
new Router($routes, $url);
```

### Api

Api is a subrouter. It allows to easily setup subroutes. Classes that need to do subrouting need to extend `\cmal\Api\Backend`. They must have a static `$routes` attribute and a `dispatch()` method.

For example, to create URLs `/login` and `/user/{id:\d+}[]`, here's a fully working example:

```
namespace foo\bar;
require (__DIR__ . '/vendor/autoload.php');
    
use \cmal\Api\Api;

class login {
	
	public function loginAction ($args) {
	    echo "Somebody wants to login, eh?";
    }
}

class user extends \cmal\Api\Backend {
	static $routes = ['GET' => ['/{id:\d+}[/]' => ['\foo\bar\user', 'getUserFromID']]];
	
	public function dispatch ($args, $routes = NULL) {
	    parent::dispatch($args, self::$routes);
    }
	
	public function getUserFromID ($args) {
	    echo "So you'd like to know about user number " . $args['id'] . ".";
	}
}

new Api([
	'login' => ['\foo\bar\login', 'loginAction'],
	'user' => '\foo\bar\user'
]);
```

## TODO

- [ ] Clean ugly code
- [ ] Custom error handling (stop using die)
- [ ] URL param for Api for nested subroutes

## License

Copyleft [GPLv3](https://github.com/alct/noapi/blob/master/LICENSE)