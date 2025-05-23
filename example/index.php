<?php

/*
 * Copyright (c) 2023. Wepesi inc.
 */

use Wepesi\Exemple\UserController;
use Wepesi\Exemple\UserValidation;
use Wepesi\Routing\Router;

require_once __DIR__ . '/../vendor/autoload.php';

$router = new Router();
$router->get('/', function () {
    echo 'home Router';
});
$router->get('/home', function () {
    echo 'Welcom Home';
});

$router->get('/users', [userController::class, 'get_users']);
/**
 * Group
 */

$router->group([
    'pattern' => '/users',
    'middleware' => [userValidation::class, 'validateId']
], function (Router $router) {
    $router->get('/:id/detail', [userController::class, 'get_user_detail'])
        ->middleware([userController::class, 'userExist']);
    $router->get('/:id/delete', 'Wepesi\Controller\UserController#delete_user');
});
/**
 *  API Group
 */
$router->api('/users', function () use ($router) {
    $router->get('/', function () {
        echo json_encode([
            'status' => http_response_code(),
            'message' => 'Welcom to Users API'
        ], true);
        exit();
    });
});
/**
 *  set custom 404 output
 */
$router->set404('**', function () {
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
    print('Not Found : ' . http_response_code());
    exit;
});
//
$router->run();
