# api

Simple fastroute wrapper for quick project setup. Allows different backend classes to manage their own routes. This allows to easily setup an API !

Each backend must have a `$routes` property and a `dispatch()` method.

The routes are defined following [fastroute syntax](https://github.com/nikic/FastRoute#defining-routes) in an array. The routes is an dictionary of methods pointing to a dictionary of routes pointing to [callables](https://secure.php.net/manual/en/language.types.callable.php).

Below is an example inspired by [noapi-api](https://github.com/paulcmal/noapi-api), a short script i wrote a while back to interface with a [Twitter api](https://github.com/alct/noapi):

```

use \cmal\Api\Api;
use \cmal\Api\Backend;

class TwitterBackend extends \cmal\Api\Backend {

	static $routes = [
		'GET' => [
			'/{action}/{query:.+}.{ext}' => ['\cmal\Form\TwitterBackend', 'action'],
		],
	];

	function dispatch($args, $routes = NULL) {
		parent::dispatch($args, self::$routes);
	}

	public function action ($args) {
		echo "The " . $args['action'] . ' action took place in the twitter backend, with arguments: ' . $args['query'] . '. We need to serve it in ' . $args['ext'] . ' format.';		
		/* Here you do your stuff */
	}
}

new Api(["twitter" => '\cmal\Form\TwitterBackend']);

```
