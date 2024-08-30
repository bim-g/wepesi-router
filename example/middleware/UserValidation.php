<?php
/*
 * Copyright (c) 2023. Wepesi inc.
 */

namespace Example\Middleware;

class UserValidation
{
    function detail_user($id){
        if(!filter_var($id,FILTER_VALIDATE_INT)){
            echo "you should provide an integer";
            exit;
        }
    }

    function validateId($id){
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            echo 'your id should be an integer';
            exit;
        }
    }
}