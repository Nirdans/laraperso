<?php

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';
require BASE_PATH . '/bootstrap/app.php';

// Démarrer la session
session_start();

// Charger le router
$router = new \App\Core\Router();
require BASE_PATH . '/routes/web.php';

// Dispatcher la requête
$router->dispatch();
