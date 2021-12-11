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

$router->get('/', [
    "as" => "about", "uses" => 'AboutController@test'
]);

$router->get('/api/resources', [
    "as" => "get.schemas", "uses" => 'SchemaController@list'
]);
$router->post('/api/resources', [
    "as" => "add.schema", "uses" => 'SchemaController@add'
]);

$router->put('/api/resources/{schema}', [
    "as" => "update.schema", "uses" => 'SchemaController@update'
]);

$router->delete('/api/resources/{schema}', [
    "as" => "delete.schema", "uses" => 'SchemaController@delete'
]);
////////////////////////////////////////////////////////////
$router->post('/api/'.config('app.api_version')."/{schema}", [
    "as" => "add.model", "uses" => 'ModelController@add'
]);

