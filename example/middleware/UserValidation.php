<?php
/*
 * Copyright (c) 2023. Wepesi inc.
 */

namespace Wepesi\Middleware;

class UserValidation
{
    function detail_user($id){
        if(!filter_var($id,FILTER_VALIDATE_INT)){
            echo "you should provide an integer";
            exit;
        }
    }

    function validateUser(){
        // implement here your validation
    }
}