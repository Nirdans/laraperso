<?php
/**
 * Point d'entrée principal de l'application
 */

// Définir le chemin de base
define('BASE_PATH', __DIR__);

// Charger les configurations
require_once BASE_PATH . '/config/config.php';

// Charger les fonctions helper
require_once BASE_PATH . '/helpers/functions.php';

// Démarrer la session
session_start();

// Charger le système de routage
require_once BASE_PATH . '/bootstrap/app.php';

// Traiter la requête
$router = new App\Core\Router();
$router->dispatch();
