<?php

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

$router->get('/books', 'BooksController@index');
$router->get('/books/{id:[\d]+}', [
    'as' => 'books.show',
    'uses' => 'BooksController@show'
]);
$router->post('/books', 'BooksController@store');
$router->put('/books/{id:[\d]+}', 'BooksController@update');
$router->delete('/books/{id:[\d]+}', 'BooksController@destroy');

$router->group(['prefix' => '/authors'], function () use ($router) {
    $router->get('/', 'AuthorsController@index');
    $router->post('/', 'AuthorsController@store');
    $router->get('/{id:[\d]+}', ['as' => 'authors.show','uses' => 'AuthorsController@show']);
    $router->put('/{id:[\d]+}', 'AuthorsController@update');
    $router->delete('/{id:[\d]+}', 'AuthorsController@destroy');
});
