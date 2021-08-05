<?php

namespace Wepesi\App\Core;

use FFI\Exception;
    class  Router{

        private  $_url;
        private  $routes=[];
        private  $_nameRoute=[];
        // private static $route=null;

        function __construct()
        {
            $this->_url=$this->getMethodeUrl();
        }

        private function getMethodeUrl(){
            foreach($_GET as $url) return $url;
        }

        function geturl(){
            return $this->_url;
        }
        
        function get($path, $collable,$name=null){
            return $this->add($path,$collable,$name,"GET");
        }

        function post($path, $collable,$name=null){
           return $this->add($path,$collable,$name,"POST");
        }

        private function add($path,$collable,$name,$methode){
            $route = new Route($path, $collable);
            $this->routes[$methode][] = $route;

            if(is_string($collable) && $name==null){
                $name=$collable;
            }

            if($name){
                $this->_nameRoute[$name]=$route;
            }
            return $route;
        }

        function url($name,$params=[]){
            if(!isset($this->_nameRoute[$name])){
                throw new \Exception('No route match');
            }
            return  $this->_nameRoute[$name]->geturl($params);
        }

        function run(){ 
            if(!isset($this->routes[$_SERVER['REQUEST_METHOD']])){
                throw new \Exception('Request method is not defined ');
            }
            foreach($this->routes[$_SERVER['REQUEST_METHOD']] as $route){
                if($route->match($this->_url)){
                    return $route->call();
                }
            }
        }        
    }

?>