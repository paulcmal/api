<?php

declare(strict_types=1);

namespace cmal\Api\Test;

use PHPUnit\Framework\TestCase;
use cmal\Api\Api;

use \cmal\Api\Exception\BaseRouteMatched;
use \cmal\Api\Exception\NoRouteMatched;

final class ApiTest extends TestCase
{
    function __construct() {
        $this->noBaseRouteBackends = ['@' => ['\cmal\Api\Test\ApiTest', 'defaultRoute']];
        $this->noDefaultRouteBackends = ['/' => ['\cmal\Api\Test\ApiTest', 'baseRoute']];
        $this->noSpecialRouteBackends = ['sample' => ['\cmal\Api\Test\ApiTest', 'sampleBackend']];
        
        $_SERVER['REQUEST_METHOD'] = 'GET';
        parent::__construct();
    }
    
    public function baseRoute() {
        echo "index";
    }
    
    public function defaultRoute($args) {
        echo $args;
    }
    
    public function sampleBackend($args) {
        echo 'sample';
    }
    
    public function testNoBaseRouteButDefaultRouteMatchesDefaultRoute() {
        $this->expectOutPutString(
            '/', new Api ($this->noBaseRouteBackends, '/')
        );
    }
    
    public function testNoBaseRouteButDefaultRouteMatchesDefaultRoute2() {
        $this->expectOutPutString(
            '/args', new Api ($this->noBaseRouteBackends, '/args')
        );
    }
    
    public function testNoBaseRouteButDefaultRouteMatchesDefaultRoute3() {
        $this->expectOutPutString(
            '/truc/12381209481', new Api ($this->noBaseRouteBackends, '/truc/12381209481')
        );
    }
    
    public function testNoDefaultRouteButBaseRouteMatchesOnlyBaseRoute() {
        $this->expectOutPutString(
            'index', new Api ($this->noDefaultRouteBackends, '/')
        );
    }
    
    public function testNoDefaultRouteButBaseRouteMatchesOnlyBaseRoute2() {
        $this->expectException(NoRouteMatched::class);
        new Api ($this->noDefaultRouteBackends, '/truc/');
    }
    
    public function testDefaultRoutePassesArgsProperly() {
        $this->expectOutPutString(
            '/truc/bar', new Api ($this->noBaseRouteBackends, '/truc/bar')
        );
    }
    
    public function testDefaultRoutePassesArgsProperly2() {
        $this->expectOutPutString(
            '/truc/', new Api ($this->noBaseRouteBackends, '/truc/')
        );
    }
    
    public function testNoBaseRouteAndNoDefaultRouteOnlyMatchDeclaredBackends() {
        $this->expectOutPutString(
            'sample', new Api ($this->noSpecialRouteBackends, '/sample/123/')
        );
    }
    
    public function testNoBaseRouteAndNoDefaultRouteOnlyMatchDeclaredBackends2() {
        $this->expectException(BaseRouteMatched::class);
        new Api ($this->noSpecialRouteBackends, '/');
    }
    
    public function testNoBaseRouteAndNoDefaultRouteOnlyMatchDeclaredBackends3() {
        $this->expectException(NoRouteMatched::class);
        new Api ($this->noSpecialRouteBackends, '/anything');
    }
}

