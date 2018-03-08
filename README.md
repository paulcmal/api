# fastsubroute

fastsubroute is a lightweight [fastroute](https://github.com/nikic/fastroute) wrapper to manage subroutes. That is, you can let different modules handle different part of the routes separately, without having to register all your routes centrally.

With fastsubroute, you can deploy a small API within minutes!

## Features

This library is nothing extraordinary. It was born out of the boredom of every time writing the same routing lines, for the same kind of setup. It's voluntarily minimal:

- [x] simple and verboseless routing with `Router`
- [x] subrouting with `Api` (nested subroutes are supported)
- [x] powerful regex for routes, thanks to [fastroute syntax](https://github.com/nikic/FastRoute#defining-routes)
- [x] sleek `BaseRouteMatched` and `NoRouteMatched` exceptions

## Using it

### Router

Router is a simple fastroute wrapper for less verbosity. The routes are defined following [fastroute syntax](https://github.com/nikic/FastRoute#defining-routes) in an array. When passing routes as a parameter, we're expecting a dictionary of methods pointing to a dictionary of routes pointing to [callables](https://secure.php.net/manual/en/language.types.callable.php). Like this:

```
$routes = [
	'GET' => [
		'/foo/{param}' => callable,
		'/bar/{param1}/param2' => callable, 
	],
];

new Router ($routes);
```

The Router constructor can also accept as a second parameter the request URI to be routed. This allows to handle subroutes:

```
$url = '/some/stuff';
new Router($routes, $url);
```

### Api

Api is designed to easily setup subroutes. You need to define your different backends in an array of names pointing to classes or callables. Your backends array may look like this:

```
$backend = [
	'login' => ['\foo\bar\login', 'loginAction'],
	'user' => '\foo\bar\user',
	'/' => ['\foo\bar\default', 'homepage'],
	'@' => ['\foo\bar\default', 'defaultAction']
];
```

In this case, your API will support `/`, `/login` and `/user` URLs, and redirect to the `defaultAction` method of `foo\bar\default` to process queries to undefined backends.

If no special `/` or `@` backend is specified, Api will respectively throw  `BaseRouteMatched` and `NoRouteMatched` exceptions. `BaseRouteMatched` is a child class of `NoRouteMatched`, so catching only the second will catch both. You can use it like this:

```
try {
    new Api([
        "foo" => ['\foo\bar\baz', 'dispatch'],
    ]);
} catch (\cmal\Api\Exception\BaseRouteMatched $e) {
    echo 'index page';
} catch (\cmal\Api\Exception\NoRouteMatched $e) {
    echo '404';
}
```

Api allows to easily setup subroutes. It will use the `callable` passed to to it in the `backends` array. If a mere class was passed (and not a proper callable), Api will try to call the `dispatch` method by default. This method should receive as arguments the request URI.

 The `NoRouteMatched` exception is also raised when no matching route was found within your backend. See below what this `dispatch()` method can look like.

```
namespace foo\bar;

class baz {
	static $routes = ['GET' => [
		'/' => ['\foo\bar\baz', 'index'],
		'/{query}.{ext}' => ['\foo\bar\baz', 'query']
	]];
	
	public function dispatch ($args) {
		try {
			new \cmal\Api\Router(self::$routes, $args);
		} catch (\cmal\Api\Exception\BaseRouteMatched $e){
			echo 'this backend index will not be matched as long as there is a / route defined';
		} catch (\cmal\Api\Exception\NoRouteMatched $e) {
			echo 'backend subroute not matched';
		}
	}
	
	public function index ($args) {
		echo 'this backend index has priority';
	}
	
	public function query ($args) {
		echo 'Querying ' . $args['query'] . ' in ' . $args['ext'] . ' format.';
	}
}
```

**Properly defined routes always have priority over exceptions.** As we have defined a `/` route here, the `BaseRouteMatched` exception here will never be thrown.

## TODO

- [ ] Clean ugly code
- [x] Custom error handling (stop using die)
- [x] URL param for Api for nested subroutes
- [ ] Write tests otherwise it's gonna get messy

## Contributing



## License

Copyleft [GPLv3](https://github.com/alct/noapi/blob/master/LICENSE)