<?php

require_once './vendor/autoload.php';
require_once './source/Core/Headers.php';

use CoffeeCode\Router\Router;

// $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
// $dotenv->load();

/**
 * ROUTE CONFIG
 */
$router = new Router('http://localhost', '@');

// phpinfo();
/**
 * ROUTES
 */
$router->get('/hello', fn () => 'hellos');

$router->namespace('Source\Controllers')->group('user');
$router->get('/', 'UserController@index');
$router->get('/{id}', 'UserController@getById');
$router->post('/', 'UserController@create');
$router->put('/{id}', 'UserController@update');
$router->delete('/{id}', 'UserController@delete');

$router->dispatch();
