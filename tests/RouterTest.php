<?php

declare(strict_types=1);

namespace cmal\Api\Test;

use PHPUnit\Framework\TestCase;
use cmal\Api\Router;

use \cmal\Api\Exception\BaseRouteMatched;
use \cmal\Api\Exception\NoRouteMatched;

final class RouterTest extends TestCase
{
    function __construct() {
        $this->idRoute = ['GET' => ['/{id:\d+}[/]' => ['\foo\bar\user', 'getUserFromID']]];
		$this->catchAllRoute = ['GET' => ['/[{everything:.+}]' => ['\cmal\Api\Test\RouterTest', 'echoTrue']]];
        $_SERVER['REQUEST_METHOD'] = 'GET';
        parent::__construct();
    }

	public function echoTrue() {
		echo "true";
	}
   
    public function testBaseRouteMatchExceptionThrownWhenNoBaseRoute() {
        $this->expectException(BaseRouteMatched::class);
        new Router ($this->idRoute, '/');
    }

	public function testNoMatchRouteExceptionThrownWhenNoRouteWasFound() {
		$this->expectException(NoRouteMatched::class);
		new Router ($this->idRoute, '/kropotkine/');
	}

	public function testNoMatchRouteExceptionWhenNoRoutes() {
		$this->expectException(NoRouteMatched::class);
		new Router ([], '/goldman');
	}

	public function testBaseRouteMatchExceptionThrownWhenNoBaseRouteEvenWithNoRoutes() {
		$this->expectException(BaseRouteMatched::class);
		new Router ([], '/');
	}

	public function testCatchAllRouteCatchesBaseRoute() {
		$this->expectOutputString(
			'true',
			new Router ($this->catchAllRoute, '/')
		);
	}

	public function testCatchAllRouteCatchesAnyRoute() {
		$this->expectOutPutString(
			'true', new Router ($this->catchAllRoute, '/sdqsdqs')
		);
	}
}

