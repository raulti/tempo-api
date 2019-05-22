<?php

$router->get('/', function () use ($router) {
    return $router->app->version();
});

//Rotas para o parse
$router->post('upload',['uses' => 'ParserLogController@upload']);



