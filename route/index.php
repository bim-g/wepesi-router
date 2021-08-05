<?php
use Wepesi\App\Core\Router;

$route=new Router();
$route->get("/",function(){
    echo "<h3>welcome to WEPESI ROUTING</h3>";
});
$route->get("/welcom", "welcomController#welcom");
$route->run();