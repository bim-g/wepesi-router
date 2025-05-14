<?php

namespace Wepesi\Routing\Providers\Contracts;

/**
 * Interface RouterGateWayContract
 * @package Wepesi\Routing\Providers\Contracts
 * @author Wepesi inc.
 * @license Apache-2.0
 * 
 * @template TMiddleware
 */
interface RouteGateWayContract
{
    public function match(?string $url): bool;
    public function with(string $param, string $regex): RouteGateWayContract;
    public function getMatch();
    public function getUrl(array $params): string;
    public function middleware($middleware): RouteGateWayContract;
}
