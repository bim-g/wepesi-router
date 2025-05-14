<?php
/*
 * Copyright (c) 2024-2025. Wepesi inc.
 */


use PHPUnit\Framework\TestCase;
use Wepesi\Routing\Providers\Contracts\RoutingContract;
use Wepesi\Routing\Route;
use Wepesi\Routing\Router;


class RouterTest extends TestCase
{
    private RoutingContract $router;
    public function setUp(): void
    {
        // Setup the router instance before each test
        $this->router = new Router();
    }

    public function testRouterRegister(): void
    {
        // when we call a router registered
        $route = $this->router->register('/users', ['Users', 'index'], 'get', 'getUsers');

        $expected = ['users' => ['Users', 'index']];

        $this->assertInstanceOf(Route::class, $route);
        $routes = $this->getPrivateProperty($this->router, 'routes');
        $this->assertIsArray($routes);
        $this->assertNotEmpty($routes['GET']);
        $callable = $this->getPrivateProperty($routes['GET'][0], 'callable');
        $_path = $this->getPrivateProperty($routes['GET'][0], '_path');
        $this->assertEquals($expected, [$_path => $callable]);
    }

    // TO DO: add more test cases for other HTTP methods (POST, PUT, DELETE, etc.)
    public function testMatchingRoute(): void
    {
        $_SERVER['REQUEST_URI'] = '/test';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $router = new Router();
        $router->get('/test', function () {
            echo "Route '/test' Executed";
        });
        $this->expectOutputString("Route '/test' Executed");
        $router->run();
    }

    public function testClassNotDefine(): void
    {
        $_SERVER['REQUEST_URI'] = '/users';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $router = new Router();
        $router->get('/users', ['Users', 'index']);
        $this->expectExceptionObject(new Exception());
        $this->expectExceptionMessage("Class 'Users' does not exist");
        $router->run();
    }

    public function testGetMethodDoesNotMatch(): void
    {
        $_SERVER['REQUEST_URI'] = '/non-existent';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $router = new Router();
        $router->get('/test', function () {
            echo 'GET Route';
        });

        $this->expectOutputString(json_encode([
            'status' => '404',
            'status_text' => 'route not defined'
        ], true));

        $router->run();
    }

    public function testUndefinedRequestMethod(): void
    {
        $router = new Router();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Request method is not defined');

        $router->run();
    }

    public function testTrigger404(): void
    {
        // when call for user register
        $_SERVER['REQUEST_URI'] = '/non-existent';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $router = new Router();
        $router->get('/test', function () {
            echo 'GET Route';
        });
        $router->set404(function () {
            echo json_encode([
                'status' => '404',
                'status_text' => 'this route is not defined'
            ]);
        });
        $this->expectOutputString(json_encode([
            'status' => '404',
            'status_text' => 'this route is not defined'
        ], true));
        $router->run();
    }

    private function getPrivateProperty($object, string $property)
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($object);
    }
}
