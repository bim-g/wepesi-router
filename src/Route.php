<?php

namespace Wepesi\Routing;

use Wepesi\Routing\Traits\ExecuteRouteTrait;
use Wepesi\Routing\Traits\ExceptionTrait;

/**
 *
 */
class Route{
    private string $_path;
    private $callable;
    private array $_matches;
    private array $_params;
    private array $_get_params, $middleware_tab;
    private bool $middleware_exist;
    use ExecuteRouteTrait;
    use ExceptionTrait;
    /**
     * @return void
     */
    function __construct(string $path, $callable,$middleware = null)
    {
        $this->_path = trim($path, '/');
        $this->callable = $callable;
        $this->_matches = [];
        $this->_params = [];
        $this->_get_params = [];
        $this->middleware_tab = $middleware ?? [];
        $this->middleware_exist = false;
    }

    /**
     * @param string|null $url
     * @return bool
     */
    function match(?string $url): bool
    {
        $url = trim($url, '/');
        $path = preg_replace_callback('#:([\w]+)#', [$this, 'paramMatch'], $this->_path);
        $regex = "#^$path$#i";
        if (!preg_match($regex, $url, $matches)) {
            return false;
        }
        // remove the url path on the array key
        array_shift($matches);
        array_shift($_GET);
        $this->_matches = $matches;
        foreach ($matches as $key => $val) {
            $_GET[$this->_get_params[$key]] = $val;
        }
        return true;
    }

    /**
     *
     */
    function call()
    {
        try {
            if (count($this->middleware_tab) > 0) {
                $this->middleware_exist = false;
                foreach ($this->middleware_tab as $middleware) {
                    $this->callControllerMiddleware($middleware, true,$this->_matches);
                }
                $this->middleware_tab = [];
            }
            $this->callControllerMiddleware($this->callable,false,$this->_matches);
        } catch (\Exception $ex) {
            $this->dumper($ex);
        }
    }

    /**
     * @param array $match
     * @return string
     */
    private function paramMatch(array $match): string
    {
        if (isset($this->_params[$match[1]])) {
            return '(' . $this->_params[$match[1]] . ')';
        }
        $this->_get_params[] = $match[1];
        return '([^/]+)';
    }

    /**
     * @param $param
     * @param $regex
     * @return $this
     */
    function with($param, $regex): Route
    {
        $this->_params[$param] = str_replace('(', '(?:', $regex);
        return $this;
    }

    /**
     * @return array
     */
    function getmatch(): array
    {
        return $this->_matches;
    }

    /**
     * @param $params
     * @return array|string|string[]
     */
    function getUrl($params)
    {
        $path = $this->_path;
        foreach ($params as $k => $v) {
            $path = str_replace(":$k", $v, $path);
        }
        return $path;
    }

    /**
     * @param  $middleware
     * @return $this
     */
    function middleware($middleware): Route
    {
        $this->middleware_tab[] = $middleware;
        return $this;
    }
}