<?php

use App\Http\Controllers\ServerController;
use Illuminate\Support\Facades\Route;

/** @var Router $router */
$router->group(['prefix' => 'api'], function () use ($router) {
    // Servers routes
    $router->get('/servers', 'ServerController@getFilteredServers'); // Get a list of servers based on filters
    $router->get('/servers/json-file-generate[/{fileName}]', 'ServerController@loadDataIntoJson'); // Trigger JSON file generation
});