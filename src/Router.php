<?php
/*
 * Copyright (c) 2023. Wepesi inc.
 */

namespace Wepesi\Routing;

use Closure;
use Exception;
use Wepesi\Resolver\Option;
use Wepesi\Resolver\OptionsResolver;
use Wepesi\Routing\Providers\Contracts\RoutingContract;
use Wepesi\Routing\Providers\RouteProvider;

/**
 * A lightweight and simple object-oriented PHP Router.
 */
class  Router extends RouteProvider implements RoutingContract
{
    private ?string $_url;
    private array $routes;
    private array $_nameRoute;
    private string $baseRoute;
    private $notFoundCallback;
    private array $baseMiddleware;
    private static  string $API_BaseRoute;

    function __construct()
    {
        self::$API_BaseRoute = '';
        $this->baseRoute = '';
        $this->routes = [];
        $this->_nameRoute = [];
        $this->_url = $_SERVER['REQUEST_URI'] ?? null;
        $this->notFoundCallback = null;
        $this->baseMiddleware = [];
    }

    /**
     * GET method
     * @param string $path
     * @param $callable
     * @param string|null $name
     * @return Route
     */
    public function get(string $path, $callable, ?string $name = null): Route
    {
        return $this->register($path, $callable, 'GET', $name);
    }

    /**
     * POST method
     * @param string $path
     * @param  $callable
     * @param string|null $name
     * @return Route
     */
    public function post(string $path, $callable, ?string $name = null): Route
    {
        return $this->register($path, $callable, 'POST', $name);
    }

    /**
     * DELETE method
     * @param string $path
     * @param $callable
     * @param string|null $name
     * @return Route
     */
    public function delete(string $path, $callable, ?string $name = null): Route
    {
        return $this->register($path, $callable, 'DELETE', $name);
    }

    /**
     * PUT method
     * @param string $path
     * @param $callable
     * @param string|null $name
     * @return Route
     */
    public function put(string $path, $callable, ?string $name = null): Route
    {
        return $this->register($path, $callable, 'PUT', $name);
    }

    /**
     * @param $base_route
     * @param callable $callable
     */
    public function group($base_route, Closure $callable): void
    {
        $pattern = $base_route;
        if (is_array($base_route)) {
            $resolver = new OptionsResolver(
                [
                    (new Option('pattern')),
                    (new Option('middleware'))
                ]
            );
            $option = $resolver->resolve($base_route);
            if (!isset($option['pattern']) || !isset($option['middleware'])) {
                print_r($option);
            }
            $pattern = $base_route['pattern'] ?? '/';

            if (isset($base_route['middleware'])) {
                $this->baseMiddleware = $this->validateMiddleware($base_route['middleware']);
            }
        }

        $cur_base_route = $this->baseRoute;
        $this->baseRoute .= $pattern;
        call_user_func($callable, $this);
        $this->baseRoute = $cur_base_route;
    }

    public function api($base_route, Closure $callable)
    {
        $pattern = '/api';
        if (is_array($base_route)) {
            $base_route['pattern'] = $pattern . '/' . trim($base_route['pattern'], '/');
        } else {
            $base_route = $pattern . '/' . trim($base_route, '/');
        }
        return $this->group($base_route, $callable);
    }
    /**
     * @param $middleware
     * @return callable[]
     */
    private function validateMiddleware($middleware): array
    {
        $valid_middleware = $middleware;
        if ((is_array($middleware) && count($middleware) == 2 && is_string($middleware[0]) && is_string($middleware[1])) || is_callable($middleware)) {
            $valid_middleware = [$middleware];
        }
        return $valid_middleware;
    }
    /**
     * @param string $path
     * @param $callable $callable
     * @param string|null $name
     * @param string $method
     * @return Route
     */
    public function register(string $path, $callable, string $method, ?string $name = null): Route
    {
        $path = $this->baseRoute . '/' . trim($path, '/');
        $path = $this->baseRoute ? rtrim($path, '/') : $path;

        $route = new Route($path, $callable, $this->baseMiddleware);
        $this->routes[strtoupper($method)][] = $route;

        if (is_string($callable) && $name == null) {
            $name = $callable;
        }

        if ($name) {
            $this->_nameRoute[$name] = $route;
        }
        return $route;
    }

    /**
     * @param string $name
     * @param array $params
     * @return void
     */
    public function url(string $name, array $params = [])
    {
        try {
            if (!isset($this->_nameRoute[$name])) {
                throw new Exception('No route match');
            }
            return $this->_nameRoute[$name]->geturl($params);
        } catch (Exception $ex) {
            print_r($ex);
        }
    }
    /**
     * Set the 404 handling function.
     *
     * @param object|callable|string $match_fn The function to be executed
     * @param null $callable
     */
    public function set404($match_fn, $callable = null)
    {
        if (!$callable) {
            $this->notFoundCallback = $match_fn;
        } else {
            $this->notFoundCallback = $callable;
        }
    }

    /**
     * @return void
     */
    protected function trigger404($match = null)
    {
        if ($match) {
            $this->callControllerMiddleware($match);
        } else {
            header('HTTP/1.1 404 Not Found');
            header('Content-Type: application/json');
            $result = [
                'status' => '404',
                'status_text' => 'route not defined'
            ];
            echo json_encode($result, true);
        }
    }

    private function getRequestMethod(): string
    {
        return php_sapi_name() === 'cli' ? 'GET' : ($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }
    /**
     * @return void
     */
    public function run()
    {
        try {
            if (!isset($this->routes[$_SERVER['REQUEST_METHOD'] ?? null])) {
                throw new Exception('Request method is not defined');
            }
            $routesRequestMethod = $this->routes[$_SERVER['REQUEST_METHOD'] ?? null];
            $i = 0;
            foreach ($routesRequestMethod as $route) {
                if ($route->match($_SERVER['REQUEST_URI'])) {
                    return $route->call();
                } else {
                    $i++;
                }
            }
            if (count($routesRequestMethod) === $i) {
                $this->trigger404($this->notFoundCallback);
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
