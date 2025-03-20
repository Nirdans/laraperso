<?php
/**
 * Fichier de configuration d'exemple
 * Copiez ce fichier vers config.php et modifiez-le selon vos besoins
 */

// Environnement (development, production)
define('ENVIRONMENT', 'development');

// Configuration de base
$config = [
    'development' => [
        'domain' => 'http://localhost/votre-projet',
        'display_errors' => true,
        'debug' => true,
    ],
    'production' => [
        'domain' => 'https://votre-domaine.com',
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
        'host' => 'mysql.votre-domaine.com',
        'name' => 'ma_base_prod',
        'user' => 'votre_utilisateur',
        'password' => 'mot_de_passe_sécurisé',
        'port' => 3306,
        'charset' => 'utf8mb4',
    ],
];

// Configuration SMTP
$smtp = [
    'host' => 'smtp.gmail.com',
    'user' => 'votre-email@gmail.com',
    'password' => 'votre-mot-de-passe-app',
    'port' => 587,
    'encryption' => 'tls',
    'from_email' => 'no-reply@votre-domaine.com',
    'from_name' => 'Votre Application',
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

// Configuration des assets
$assets = [
    'paths' => [
        'public' => '/public',
        'assets' => '/public/assets',
        'uploads' => '/public/uploads',
    ],
    'upload_directory' => BASE_PATH . '/public/uploads',
];

// Charger les variables en fonction de l'environnement
$current = $config[ENVIRONMENT];
$db = $database[ENVIRONMENT];

// Paramètres d'affichage d'erreurs
ini_set('display_errors', $current['display_errors'] ? 1 : 0);
error_reporting($current['display_errors'] ? E_ALL : 0);
