<?php
/**
 * Fichier de configuration principal
 */

// Environnement (development, production)
define('ENVIRONMENT', 'development');

// Configuration de base
$config = [
    'development' => [
        'domain' => 'http://localhost/composant/empty',
        'display_errors' => true,
        'debug' => true,
    ],
    'production' => [
        'domain' => 'https://mondomaine.com',
        'display_errors' => false,
        'debug' => false,
    ]
];

// Configuration de la base de données
$database = [
    'development' => [
        'host' => 'localhost',
        'name' => 'ma_base_locale',
        'user' => 'root',
        'password' => '',
        'port' => 3306,
        'charset' => 'utf8mb4',
    ],
    'production' => [
        'host' => 'mysql.mondomaine.com',
        'name' => 'ma_base_prod',
        'user' => 'utilisateur_prod',
        'password' => 'mot_de_passe_securise',
        'port' => 3306,
        'charset' => 'utf8mb4',
    ],
];

// Configuration SMTP
$smtp = [
    'host' => 'smtp.gmail.com',
    'user' => 'votre-email@gmail.com',
    'password' => 'votre-mot-de-passe',
    'port' => 587,
    'encryption' => 'tls',
    'from_email' => 'no-reply@mondomaine.com',
    'from_name' => 'Mon Application',
];

// Configuration des fournisseurs d'authentification
$auth = [
    'google' => [
        'client_id' => 'votre-client-id',
        'client_secret' => 'votre-client-secret',
        'redirect' => '/auth/google/callback',
    ],
    'facebook' => [
        'client_id' => 'votre-app-id',
        'client_secret' => 'votre-app-secret',
        'redirect' => '/auth/facebook/callback',
    ],
];

// Configuration du token JWT
$jwt = [
    'key' => 'votre-clé-secrète-très-longue-et-sécurisée',
    'expiration' => 3600, // en secondes (1 heure)
];

// Charger les variables en fonction de l'environnement
$current = $config[ENVIRONMENT];
$db = $database[ENVIRONMENT];

// Paramètres d'affichage d'erreurs
ini_set('display_errors', $current['display_errors'] ? 1 : 0);
error_reporting($current['display_errors'] ? E_ALL : 0);
