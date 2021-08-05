<?php

namespace Wepesi\App\Core;
    class  Router{

        private  $_url;
        private  $routes=[];
        private  $_nameRoute=[];

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
        
        function get(string $path, $collable,$name=null){
            return $this->add($path,$collable,$name,"GET");
        }

        function post(string $path, $collable,$name=null){
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
            try{
                if(!isset($this->_nameRoute[$name])){
                    throw new \Exception('No route match');
                }
                return  $this->_nameRoute[$name]->geturl($params);
            }catch(\Exception $ex){
                return $ex->getMessage();
            }            
        }

        function run(){ 
            try{
                if(!isset($this->routes[$_SERVER['REQUEST_METHOD']])){
                    throw new \Exception('Request method is not defined ');
                }
                $routesRequestMethod= $this->routes[$_SERVER['REQUEST_METHOD']];
                $i=0;
                foreach($routesRequestMethod as $route){
                    if($route->match($this->_url)){
                        return $route->call();
                    }else{
                        $i++;
                    }                                       
                }
                if(count($routesRequestMethod)===$i){
                    throw new \Exception('<h3>404 not found</h3>');
                }
            }catch(\Exception $ex){
                echo $ex->getMessage();
            }
        }        
    }

?>