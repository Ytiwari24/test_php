<?php

include_once('../controller/UserController.php');

// Example route
Router::get('/users', 'UserController@getAllUsers');
Router::post('/users', 'UserController@createUser');
Router::put('/users/{id}', 'UserController@updateUser');
Router::delete('/users/{id}', 'UserController@deleteUser');
