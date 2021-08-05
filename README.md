# WEPESI_ROUTER

This is a simple php module that will help you write clear route in php application

# INTRODUCTION
wepesi is a simple mini-framawork that help to create simple wep application using OOP logic.
it provide :
- routing
- validation for more : https://github.com/bim-g/wepesi_validation
- token (generator & validator)
- ORM design for mySQL for more : https://github.com/bim-g/Wepesi-ORM
- session management
- ...
you can find more about comple module on : https://github.com/kivudesign/Wepesi-Quick

# INTEGRATION

create an instance of the router, that will help implemet request method, assume that you should call the method run at the end of all the method to take into consideration all the route define.

```php
    $router=new Router();
    $router->get("/",function(){
        echo "<h3>welcome to WEPESI ROUTING</h3>";
    });
    $router->run()
```

* GET: while define a get method, you provide the route name to reach and the action to execute. that action can be a `callable` function or method of a class is define on the class folder.

    ```php
        $router->get('/',function(){
            echo "welcome to the root";
        });

        $router->get("/hello","user#sayHello")
    ```
    as you can seen in the example below, we want to reach the route `localhost/hello` and the action will be `hello` a method define on the class user `user#sayHello`.
    in case this method is not define that class it will not passe.