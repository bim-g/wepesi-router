<?php


namespace Wepesi\Middlleware;


class UserValidation
{
    function detail_user($id){
        if(!filter_var($id,FILTER_VALIDATE_INT)){
            echo "you should provide an integer";
            exit;
        }
    }
}