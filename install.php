<?php
/**
 * Script d'installation de l'application
 * Créer les tables nécessaires et le premier utilisateur administrateur
 */

// Définir le chemin de base
define('BASE_PATH', __DIR__);

// Charger les configurations
require_once BASE_PATH . '/config/config.php';

// Charger les fonctions helper
require_once BASE_PATH . '/helpers/functions.php';

// Charger les classes nécessaires explicitement pour éviter les problèmes d'autoloading
require_once BASE_PATH . '/app/Services/Database.php';  // Changé de Services à services
require_once BASE_PATH . '/app/Models/Model.php';       // Changé de Models à models
require_once BASE_PATH . '/app/Models/User.php';        // Changé de Models à models

// Fonction pour afficher un message d'installation
function display_message($message, $type = 'info')
{
    $color = 'black';
    switch ($type) {
        case 'success':
            $color = 'green';
            break;
        case 'error':
            $color = 'red';
            break;
        case 'warning':
            $color = 'orange';
            break;
    }
    echo "<div style='color: {$color}; margin: 10px 0;'>{$message}</div>";
    flush();
}

// Vérifier si la base de données est accessible
try {
    $dsn = "mysql:host={$db['host']};port={$db['port']}";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, $db['user'], $db['password'], $options);
    display_message("✓ Connexion au serveur MySQL réussie", "success");
} catch (PDOException $e) {
    display_message("✗ Impossible de se connecter au serveur MySQL: " . $e->getMessage(), "error");
    exit;
}

// Vérifier si la base de données existe
try {
    $pdo->query("USE `{$db['name']}`");
    display_message("✓ Base de données '{$db['name']}' accessible", "success");
} catch (PDOException $e) {
    display_message("La base de données '{$db['name']}' n'existe pas, tentative de création...", "warning");
    try {
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db['name']}` CHARACTER SET {$db['charset']} COLLATE {$db['charset']}_general_ci");
        display_message("✓ Base de données '{$db['name']}' créée avec succès", "success");
        $pdo->query("USE `{$db['name']}`");
    } catch (PDOException $e2) {
        display_message("✗ Impossible de créer la base de données: " . $e2->getMessage(), "error");
        exit;
    }
}

// Créer les tables nécessaires
display_message("Création des tables nécessaires...", "info");

// Créer la table users
try {
    $user = new \App\Models\User();
    $user::createTable();
    display_message("✓ Table 'users' créée/mise à jour avec succès", "success");
} catch (Exception $e) {
    display_message("✗ Erreur lors de la création de la table 'users': " . $e->getMessage(), "error");
}

// Créer un utilisateur administrateur par défaut si aucun n'existe
try {
    $users = $user->all();
    
    if (empty($users)) {
        $adminId = $user->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => password_hash('admin123', PASSWORD_DEFAULT)
        ]);
        
        if ($adminId) {
            display_message("✓ Utilisateur administrateur créé avec succès", "success");
            display_message("   Email: admin@example.com", "info");
            display_message("   Mot de passe: admin123", "info");
            display_message("   N'oubliez pas de changer ce mot de passe après votre première connexion !", "warning");
        } else {
            display_message("✗ Erreur lors de la création de l'utilisateur administrateur", "error");
        }
    } else {
        display_message("✓ Des utilisateurs existent déjà, aucun administrateur par défaut n'a été créé", "info");
    }
} catch (Exception $e) {
    display_message("✗ Erreur lors de la vérification/création de l'utilisateur administrateur: " . $e->getMessage(), "error");
}

// Vérifier si les dossiers nécessaires existent et sont accessibles en écriture
$directories = [
    'assets/img',
    'assets/uploads',
    'logs',
    'cache'
];

display_message("Vérification des dossiers...", "info");

foreach ($directories as $directory) {
    $fullPath = BASE_PATH . '/' . $directory;
    if (!file_exists($fullPath)) {
        if (mkdir($fullPath, 0755, true)) {
            display_message("✓ Dossier '{$directory}' créé avec succès", "success");
        } else {
            display_message("✗ Impossible de créer le dossier '{$directory}'", "error");
        }
    } else {
        if (is_writable($fullPath)) {
            display_message("✓ Dossier '{$directory}' existe et est accessible en écriture", "success");
        } else {
            display_message("✗ Le dossier '{$directory}' existe mais n'est pas accessible en écriture", "error");
        }
    }
}

// Installation terminée
display_message("\nInstallation terminée !", "success");
display_message("Vous pouvez maintenant accéder à votre application à l'adresse: {$current['domain']}", "info");
display_message("N'oubliez pas de supprimer le fichier 'install.php' pour des raisons de sécurité.", "warning");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation de l'application</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .next-steps {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            margin-top: 30px;
        }
        .btn {
            display: inline-block;
            background-color: #4e73df;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 10px;
        }
        .btn:hover {
            background-color: #2e59d9;
        }
    </style>
</head>
<body>
    <h1>Installation de l'application</h1>
    
    <div class="next-steps">
        <h2>Prochaines étapes</h2>
        <ol>
            <li>Connectez-vous avec l'utilisateur administrateur créé</li>
            <li>Changez le mot de passe administrateur</li>
            <li>Configurez les paramètres de l'application dans <code>config/config.php</code></li>
            <li>Supprimez le fichier <code>install.php</code></li>
        </ol>
        
        <a href="<?= $current['domain'] ?>" class="btn">Accéder à l'application</a>
    </div>
</body>
</html>
