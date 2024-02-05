<?php

// Autoload classes using Composer
require_once 'vendor/autoload.php';

// Include Router class
require_once 'Router.php';

// Include user routes
include_once('routes/user_routes.php');

// Run the router
Router::route($_SERVER['REQUEST_METHOD']);
