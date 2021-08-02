<?php

/** @var \Laravel\Lumen\Routing\Router $router */
$router = app('router');

$router
    ->group(['prefix' => 'api/auth', 'namespace' => 'Chestnut\Auth'], function ($router) {
        $router->post('login', 'LoginController@login');
        $router->post('wechat_login', 'LoginController@wechat_login');
        $router->post('logout', 'LoginController@logout');
        $router->post('refresh', 'LoginController@refresh');
        $router->post('wechat_refresh', 'LoginController@wechat_refresh');
        $router->post('me', 'LoginController@me');
    });

$router->group(['prefix' => 'api'], function ($router) {
    $router->group(['middleware' => 'auth:chestnut'], function ($router) {
        $router->get('/repositories', function () {
            return array_merge(['errno' => 0, 'message' => 'request success'], app('shell')->toArray());
        });
    });

    $router->get('/settings', function () {
        $data = [];
        if (!empty(app('auth')->guard("chestnut")->user())) {
            $data = app("shell")->jsonSerialize();
        }

        $data = array_merge($data, [
            "appName"     => env("APP_NAME", "CHESTNUT"),
            "description" => env("DESCRIPTION", "Chestnut Resource Manage System"),
            "routePrefix" => env("CHESTNUT_ROUTE_PREFIX", ""),
        ]);

        return ['code' => 200, 'message' => 'request success', 'data' => $data];
    });
});
