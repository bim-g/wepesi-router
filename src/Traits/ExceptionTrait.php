<?php

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