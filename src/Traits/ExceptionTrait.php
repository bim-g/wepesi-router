<?php
/*
 * Copyright (c) 2023. Wepesi inc.
 */

namespace Wepesi\Routing\Traits;

trait ExceptionTrait
{
    private function dumper($object){
        print('<pre>');
        print_r($object);
        print('</pre>');
        exit(0);
    }
}