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
            if(is_string($this->_collable)){
                // get teh class_name and the methode to be call
                $params=explode("#",$this->_collable);
                // Controller::useController($params[0]);
                $controller_file_name= new $params[0];
                call_user_func_array([$controller_file_name,$params[1]],$this->_matches);
            }else{
                return call_user_func_array($this->_collable,$this->_matches);
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