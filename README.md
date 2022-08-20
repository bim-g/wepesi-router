# wepesi/routing

[![Build Status](https://github.com/bim-g/wepesi-router/actions/workflows/php.yml/badge.svg)](https://github.com/bim-g/wepesi-router/actions) [![Source](http://img.shields.io/badge/source-bimg/router-blue.svg?style=flat-square)](https://github.com/bim-g/wepesi-router) [![Version](https://img.shields.io/packagist/v/wepesi/routing.svg?style=flat-square)](https://packagist.org/packages/wepesi/routing) [![Downloads](https://img.shields.io/packagist/dt/wepesi/routing.svg?style=flat-square)](https://packagist.org/packages/wepesi/routing/stats) [![License](https://img.shields.io/packagist/l/wepesi/routing.svg?style=flat-square)](https://github.com/bim-g/wepesi-router/blob/master/LICENSE)
[![Issues](https://img.shields.io/github/issues/bim-g/wepesi-router?style=flat-square)](http://github.com/bim-g/wepesi-router/issues)

A lightweight and simple object oriented PHP Router.
Built by  _([Boss Ibrahim Mussa](https://www.github.com/bim-g))_ and [Contributors](https://github.com/bim-g/wepesi-router/graphs/contributors)


## Features

- Supports `GET`, `POST`, `PUT`, `DELETE`,`PATCH` request methods
- [Routing shorthands such as `get()`, `post()`, `put()`, …](#routing-shorthands)
- [Static Route Patterns](#route-patterns)
- Dynamic Route Patterns: [Dynamic PCRE-based Route Patterns](#dynamic-pcre-based-route-patterns) or [Dynamic Placeholder-based Route Patterns](#dynamic-placeholder-based-route-patterns)
- [Optional Route Subpatterns](#optional-route-subpatterns)
- [Supports `X-HTTP-Method-Override` header](#overriding-the-request-method)
- [Subrouting / Group Routing](#Subrouting-/-Groupe-Routing)
- [Allowance of `Class@Method` calls](#classmethod-calls)
- [Before Route Middlewares](#before-route-middlewares)
- [Before Router Middlewares / Before App Middlewares](#before-router-middlewares)
- [Works fine in subfolders](#subfolder-support)



## Prerequisites/Requirements

- PHP 7.4 or greater
- [URL Rewriting]([https://gist.github.com/bim-g/5332525](https://github.com/bim-g/wepesi-router/blob/master/.htaccess))



## Installation

Installation is possible using Composer

```shell
composer require wepesi/routing
```

## Demo

A demo is included in the `demo` folder. Serve it using your favorite web server, or using PHP 7.4+'s built-in server by executing `php -S localhost:8080` on the shell. A `.htaccess` for use with Apache is included.

## Usage

Create an instance of `\Wepesi\Routing\Router`, define some routes onto it, and run it.

```php
// Require composer autoloader
require __DIR__ . '/vendor/autoload.php';

// Create Router instance
$router = new \Wepesi\Routing\Router();

// Define routes
// ...

// Run it!
$router->run();
```

### Routing

`Wepesi/routing` supports `GET`, `POST`, `PUT`, `PATCH`, `DELETE` HTTP request methods. Pass in a single request method.

When a route matches against the current URL (e.g. `$_SERVER['REQUEST_URI']`), the attached __route handling function__ will be executed. The route handling function must be a [callable](http://php.net/manual/en/language.types.callable.php). Only the first route matched will be handled. When no matching route is found, a 404 handler will be executed.

### Routing Shorthands

Shorthands for single request methods are provided:

```php
$router->get('pattern', function() { /* ... */ });
$router->post('pattern', function() { /* ... */ });
$router->put('pattern', function() { /* ... */ });
$router->delete('pattern', function() { /* ... */ });
```

Note: Routes must be hooked before `$router->run();` is being called.

### Route Patterns

Route Patterns can be static or dynamic:

- __Static Route Patterns__ contain no dynamic parts and must match exactly against the `path` part of the current URL.
- __Dynamic Route Patterns__ contain dynamic parts that can vary per request. The varying parts are named __subpatterns__ and are defined using either Perl-compatible regular expressions (PCRE) or by using __placeholders__

#### Static Route Patterns

A static route pattern is a regular string representing a URI. It will be compared directly against the `path` part of the current URL.

Examples:

-  `/about`
-  `/contact`

Usage Examples:

```php
// This route handling function will only be executed when visiting http(s)://www.example.org/about
$router->get('/about', function() {
    echo 'About Page Contents';
});
```
#### Dynamic Placeholder-based Route Patterns

This type of Route Patterns are the same as __Dynamic PCRE-based Route Patterns__, but with one difference: they don't use regexes to do the pattern matching but they use the more easy __placeholders__ instead. Placeholders are strings surrounded by collumn, e.g. `:name`.

Examples:

- `/movies/:id`
- `/profile/:username`

Placeholders are easier to use than PRCEs, but offer you less control as they internally get translated to a PRCE that matches any character (`.*`).

```php
$router->get('/movies/:movieId/photos/:photoId', function($movieId, $photoId) {
    echo 'Movie #' . $movieId . ', photo #' . $photoId;
});
```

Note: `the name of the placeholder should match with the name of the parameter that is passed into the route handling function.`

#### Dynamic PCRE-based Route Patterns

This type of Route Patterns contain dynamic parts which can vary per request. The varying parts are named __subpatterns__ and are defined using regular expressions.

Usage Examples:

```php
// This route handling function will only be executed when visiting http(s)://www.example.org/movies/3
$router->get('/movies/:id', function($id) {
    echo 'Get a movie by ID:'.$id;
})->with('id','[0-9]+');
```

Commonly used PCRE-based subpatterns within Dynamic Route Patterns are:

- `\d+` = One or more digits (0-9)
- `\w+` = One or more word characters (a-z 0-9 _)
- `[a-z0-9_-]+` = One or more word characters (a-z 0-9 _) and the dash (-)
- `.*` = Any character (including `/`), zero or more
- `[^/]+` = Any character but `/`, one or more

When multiple subpatterns are defined, the resulting __route handling parameters__ are passed into the route handling 'with` function  in the order they are defined in:

```php
// http(s)://www.example.org/articles/12-nyiragongo-volcano`
$router->get('/artilces/:id-:name', function($id, $title) {
    echo 'Articles #' . $id . ', title #' . $title;
})
->with('id','[0-9]+')
->with('title','[a-z\0-9]+');
```
This will be like a kind of validation of your parameters.

### Subrouting / Groupe Routing

Use `$router->group($baseroute, $fn)` to group a collection of routes in to a subroute pattern. The subroute pattern is prefixed into all following routes defined in the scope. e.g. Mounting a callback `$fn` onto `/movies` will prefix `/movies` onto all following routes.

```php
$router->group('/movies', function() use ($router) {

    // will result in '/movies/'
    $router->get('/', function() {
        echo 'movies overview';
    });

    // will result in '/movies/id'
    $router->get('/:id', function($id) {
        echo 'movie id ' . $id;
    });

});
```

Nesting of subroutes is possible, just define a second `$router->group()` in the callable that's already contained within a preceding `$router->group()`.

```php
$router->group('/articles', function() use ($router) {

    // will result in '/articles/'
    $router->get('/', function() {
        echo 'articles overview';
    });
    
    //
	$router->group('/themes', function() use ($router) {	
	    // will result in '/articles/themes'
	    $router->get('/', function() {
	        echo 'Articles themes overview';
	    });	
	    // will result in '/articles/themes/4'
	    $router->get('/:id', function($id) {
	        echo 'Articles themes detail id: ' . $id;
	    });
	
	});

});
```


### `Class#Method` calls

We can route to the class action like so:

```php
$router->get('/users/:id', '\Wepesi\Controller\UserController#get_users_detail');
```
or
```php
$router->get('/users/:id', [\Wepesi\Controller\UserController::class,'get_users_detail']);
```

When a request matches the specified route URI, the `get_users` method on the `UserController` class will be executed. The defined route parameters will be passed to the class method.

The method can be static(not-recommend)  or non-static (recommend). In case of a non-static method, a new instance of the class will be created.

```php
$router->get('/users/profile', \Wepesi\Controller\Users::profile());

```
Note: In case you are using static method, dont pass as string or in array.
### Before Route Middleware

`wepesi/routing` supports __Before Route Middlewares__, which are executed before the route handling is processed.

```php
$router->get('/articles/:id', function($id) {
    echo "article id is:".$id;
})->middleware(function($id){
	if(!filter_var($id,FILTER_VALIDATE_INT)){
	    echo "you should provide an integer";
	    exit;
	}
});
```
Route middlewares are route specific, one or middleware can be set and will be executed before route function.

```php
$router->get('/admin/:id', function($id) {
    echo "admin id is:".$id;
})->middleware(function($id){
	print_r("First middleware");
})->middleware(function($id){
	print_r("Second middleware");
})->middleware(function($id){
	print_r("Last middleware before Route function");
});
```

### Overriding the request method

Use `X-HTTP-Method-Override` to override the HTTP Request Method. Only works when the original Request Method is `POST`. Allowed values for `X-HTTP-Method-Override` are `PUT`, `DELETE`, or `PATCH`.


## Integration with other libraries

Integrate other libraries with `wepesi/routing` by making good use of the `use` keyword to pass dependencies into the handling functions.

```php
$view = new \Wepesi\View();

$router->get('/', function() use ($view ) {
    $view->assign('email','ibmussafb@gmail.com');
    $view->assign('github','bim-g');
    $view->display("/profile.php");
});

$router->run();
```

Given this structure it is still possible to manipulate the output from within the After Router Middleware

## A note on working with PUT

There's no such thing as `$_PUT` in PHP. One must fake it:

```php
$router->put('/movies/:username', function($username) {
    // Fake $_PUT
    $_PUT  = array();
    parse_str(file_get_contents('php://input'), $_PUT);
    // ...
});
```

## License

`wepesi/routing` is released under the Apache-2.0 license. See the enclosed `LICENSE` for details.
