<?php

namespace Wepesi\Routing\Providers;

use ReflectionClass;
use Exception;

abstract class RouteProvider
{
    protected function callControllerMiddleware($callable, bool $is_middleware = false, array $matches = []): void
    {
        try {
            if (is_string($callable) || is_array($callable)) {
                $params = is_string($callable) ? explode('#', $callable) : $callable;
                if (count($params) != 2) {
                    throw new Exception('Error : on class/method is not well defined');
                    exit;
                }

                $class_name = $params[0];
                $class_method = $params[1];

                // Check if the class exists
                if (!class_exists($class_name)) {
                    throw new Exception("Class '$class_name' does not exist");
                    exit;
                }

                // Check if the class is instantiable
                $reflection = new ReflectionClass($class_name);
                if (!$reflection->isInstantiable()) {
                    throw new Exception("Error : class $class_name is not instantiable");
                }

                // Check if the method exists
                $class_object = $reflection->newInstance();
                if (!method_exists($class_object, $class_method)) {
                    throw new Exception("method : $class_method does not belong the class : $class_name.");
                    exit;
                }
                call_user_func_array([$class_object, $class_method], $matches);
            } elseif (isset($callable) && is_callable($callable, true)) {
                call_user_func_array($callable, $matches);
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
