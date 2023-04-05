<?php
use Wepesi\Controller\UserController;
use Wepesi\Middleware\UserValidation;
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
    $router->group([
        'pattern'=>'/group',
        'middleware' => [userValidation::class,'detail_user']
    ],function () use($router){
        $router->get('/:id/detail', [userController::class, 'get_user_detail'])
            ->middleware([userController::class, 'userExist']);
        $router->get('/:id/delete', 'Wepesi\Controller\UserController#delete_user');
    });
});
/**
 *  set custom 404 output
 */
$router->set404('**',function(){
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
    print('Not Found : ' . http_response_code());
    exit;
});
//
$router->run();