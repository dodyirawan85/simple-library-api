<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/key', function () {
    return \Illuminate\Support\Str::random(32);
});

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('/login', 'AuthController@login');
    $router->post('/register', 'AuthController@register');
    $router->get('/users', ['middleware' => 'auth', 'uses' => 'AuthController@user']);
});

$router->group(['middleware' => 'auth'], function ($router) {
    // * put all endpoint that need authentication here
});

$router->group(['prefix' => 'category'], function ($router) {
    $router->get('/', 'CategoryController@index');
    $router->post('/', 'CategoryController@store');
    $router->get('/{id}', 'CategoryController@show');
    $router->put('/{id}', 'CategoryController@update');
    $router->delete('/{id}', 'CategoryController@destroy');
});

$router->group(['prefix' => 'books'], function ($router) {
    $router->get('/', 'BookController@index');
    $router->post('/', 'BookController@store');
    $router->get('/{id}', 'BookController@show');
    $router->put('/{id}', 'BookController@update');
    $router->delete('/{id}', 'BookController@destroy');
});
