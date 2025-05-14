<?php

namespace Wepesi\Routing\Providers\Contracts;

use Closure;

interface RoutingContract
{
    public function get(string $path, $callable, ?string $name = null);
    public function post(string $path, $callable, ?string $name = null);
    public function put(string $path, $callable, ?string $name = null);
    public function delete(string $path, $callable, ?string $name = null);
    public function register(string $path, $callable, string $method, ?string $name = null);
    public function set404($match_fn, $callable = null);
    public function api($base_route, Closure $callable);
    public function group($base_route, Closure $callable): void;
    public function run();
}
