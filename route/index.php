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
$route->get("/register",function(){
    $link=WEB_ROOT."register";
    echo <<< frm
    <h3>Registration Form</h3>
    <form action=$link method="post">
        <input type="text" name="name" placeholder="enter your name"><br>
        <input type="age" name="age" placeholder="enter your age"><br>
        <input type="reset" value="cancel"><br>
        <input type="submit" value="register"><br>
    </form>
frm;

});
$route->post("/register","welcomController#register");
$route->run();