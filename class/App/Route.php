<?php

namespace Wepesi\App\Core;

    class Route{
        private $_path;
        private $_collable;
        private $_matches=[];
        private $_params=[];

        function __construct($path,$collable){
            $this->_path=trim($path,"/");
            $this->_collable= $collable; 
        }
        
        function match($url){
            $url=trim($url,"/");
            $path=preg_replace_callback('#:([\w]+)#',[$this,'paramMatch'],$this->_path);
            $regex="#^$path$#i";
            if(!preg_match($regex,$url,$matches)){
                return false;
            }
            array_shift($matches);
            $this->_matches=$matches;
            return true;
        }

        function call(){
            try{
                // get the class_name and the methode to be call
                if (is_string($this->_collable)) {
                    $params = explode("#", $this->_collable);
                    $class=$params[0];$method=$params[1];
                    if (!class_exists($class,true)) {
                        throw new \Exception("class : <b> $class</b> is not defined.");
                    }
                    $class_instance = new $class;                        
                    if(!method_exists($class_instance,$method)) {
                        throw new \Exception("method :<b> $method</b> does not belong the class : <b> $class</b>.");
                    }     
                    call_user_func_array([$class_instance, $method], $this->_matches);
                } else {
                    return call_user_func_array($this->_collable, $this->_matches);
                }
            }catch(\Exception $ex){
                echo $ex->getMessage();
            }            
        }
        private function paramMatch($match){
            // 
            if(isset($this->_params[$match[1]])){
                return "(".$this->_params[$match[1]].")";
            }
            return "([^/]+)";
        }
        function with($param,$regex){
            $this->_params[$param]=str_replace('(','(?:',$regex);
            return $this;
        }
        function getmatch(){
            return $this->_matches;
        }
        function geturl($params){
            $path=$this->_path;
            foreach($params as $k=>$v){
                $path=str_replace(":$k",$v,$path);
            }
            return $path;
        }
    }