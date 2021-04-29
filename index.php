<?php

require_once './vendor/autoload.php';
require_once './source/Core/Headers.php';

use CoffeeCode\Router\Router;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

/**
 * ROUTE CONFIG
 */
$router = new Router(env('BASE_URL'), '@');

/**
 * ROUTES
 */
$router->namespace('Source\Controllers')->group('user');
$router->get('/', 'UserController@index');
$router->get('/{id}', 'UserController@getById');
$router->post('/', 'UserController@create');
$router->put('/{id}', 'UserController@update');
$router->delete('/{id}', 'UserController@delete');

$router->dispatch();
