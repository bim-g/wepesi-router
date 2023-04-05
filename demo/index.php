<?php
use Wepesi\Controller\UserController;
use Wepesi\Middlleware\UserValidation;
use Wepesi\Routing\Router;

$router = new Router();
$router->get('/',function(){
    echo 'home Router';
});
$router->get('/home',function (){
    echo 'Welcom Home';
});
/**
 * Group
 */
$router->group('/users', function () use ($router) {
    $router->get('/', [userController::class,'get_users']);
    $router->get('/:id', [userController::class, 'get_user_detail'])
        ->middleware([userValidation::class,'detail_user']);

    $router->group('/admin', function () use ($router) {
        $router->get('/delete/:id', 'Wepesi\Controller\UserController#delete_user')
            ->middleware([userValidation::class,'detail_user']);
    });
});

$router->set404('**',function(){
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
    print('Not Found : ' . http_response_code());
    exit;
});
$router->run();