<?php
use Wepesi\App\Core\Router;
$route=new Router();
/**
 * call the route 
 */
$route->get("/",function(){
    echo "<h3>welcome to WEPESI ROUTING</h3>";
});
$route->get("/welcom", "welcomController#welcom");
$route->get("/welcom/:name",function($name){
    echo "your name is : $name";
});
$route->run();