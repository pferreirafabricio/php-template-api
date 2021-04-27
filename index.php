<?php

require_once './vendor/autoload.php';
require_once './source/Core/Headers.php';

use CoffeeCode\Router\Router;

/**
 * ROUTE CONFIG
 */
$router = new Router(CONF_BASE_URL, '@');

/**
 * ROUTES
 */
$router->get('/hello', fn () => phpinfo());

$router->namespace('Source\Controllers')->group('user');
$router->get('/', 'UserController@index');
$router->get('/{id}', 'UserController@getById');
$router->post('/', 'UserController@create');
$router->put('/{id}', 'UserController@update');
$router->delete('/{id}', 'UserController@delete');

$router->dispatch();
