<?php

namespace Wepesi\App\Core;

class Input
{
    static function exists($method="post"){
        return !empty($_POST) || !empty($_GET);
    }
    static function get($item){
        $object_data=null;
        if(json_decode(file_get_contents("php://input"), true)){
            $object_data=(array)(json_decode(file_get_contents("php://input"), true));
        }
        if(isset($_POST[$item])){
            return $_POST[$item];
        }else if(isset($_GET[$item])){
            return $_GET[$item];
        }else if(isset($object_data[$item])){
            return $object_data[$item];
        }
        return null;
    }
}