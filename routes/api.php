<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return get_json_response([
        'success' => true,
        'status' => 200,
        'message' => null,
        'data' => [
            'version' => $router->app->version(),
            'doc' => url('/api/documentation')
        ],
        'exceptions' => null,
    ]);
});

$router->group([
    'prefix' => 'auth'
], function() use ($router) {
    $router->post('/login', ['uses' => 'AuthController@authenticate']);
    $router->post('/register', ['uses' => 'AuthController@register']);
});