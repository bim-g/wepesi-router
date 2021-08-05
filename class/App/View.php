<?php

namespace Wepesi\App\Core;

class View
{
    private $data=[];
    private $render=false;

    function __construct($filename)
    {
        $file= checkFileExtension($filename);
        if (is_file(ROOT . "view/" . $file)) {
            $this->render=ROOT . "view/" . $file;
        }else{
            echo "<h3>404 not found</h3>";
        }
    }

    function assign($variable,$value){
        $this->data[$variable]=$value;
    }
    function __destruct()
    {
        extract($this->data);
        include($this->render);
    }
}