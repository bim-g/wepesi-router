<?php

namespace Wepesi\Routing;

trait ExecuteRouteTrait
{
    protected function callControllerMiddleware($callable, bool $is_middleware = false,array $matches = []): void
    {
        try {
            if (is_string($callable) || is_array($callable)) {
                $params = is_string($callable) ? explode('#', $callable) : $callable;
                if (count($params) != 2) {
                    throw new \Exception('Error : on class/method is not well defined');
                }

                $class_name = $params[0];
                $class_method = $params[1];

                $reflexion = new \ReflectionClass($class_name);
                if($reflexion->isInstantiable()){
                    throw new \Exception("Error : class $class_name is not instantiable");
                }
                $class_object = $reflexion->newInstance();

                if (!method_exists($class_object, $class_method)) {
                    throw new \Exception("method : $class_method does not belong the class : $class_name.");
                }
                call_user_func_array([$class_object, $class_method], $matches);
            } else {
                if (isset($callable) && is_callable($callable, true)) {
                    call_user_func_array($callable, $matches);
                }
            }
            return;
        } catch (\Exception $ex) {
            print('<pre>');
            print_r($ex);
            print('</pre>');
            exit();
        }
    }
}