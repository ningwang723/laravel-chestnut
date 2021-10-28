<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::middleware(['web'])->prefix("chestnut")
    ->group(function () {
        Route::namespace("Chestnut\Auth")->group(function () {
            Route::middleware('auth:sanctum')->get('profile', 'LoginController@me');

            Route::post('login', 'LoginController@login');


            Route::post('logout', 'LoginController@logout');
            Route::post('refresh', 'LoginController@refresh');
        });

        Route::middleware("auth:sanctum")->post('statistic', function (Request $request) {
            $statistic = new $request->statistic;

            $data = $statistic->calculate($request);

            return [
                'errno' => 0,
                'data' => $data
            ];
        });

        Route::get('/settings', function () {
            $data = app("shell")->jsonSerialize();

            return ['code' => 200, 'message' => 'request success', 'data' => $data];
        });
    });

Route::middleware(['api'])->prefix("api")->group(function () {
    Route::post('wechat_login', 'LoginController@wechat_login');
});
