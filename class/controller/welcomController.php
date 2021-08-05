<?php
use Wepesi\App\Core\Input;
class welcomController{
    function welcom(){
        echo "Welcom home to the controller";
    }

    function register(){
        if(!Input::exists()){
            echo "no data is available";
            exit;
        }
        $name=Input::get('name');
        $age=Input::get('age');
        echo " name :$name <br> age :$age ";
    }
}