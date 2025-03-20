<?php
/**
 * Script de test pour l'envoi d'email
 */

// Définir le chemin de base
define('BASE_PATH', __DIR__);

// Charger les configurations
require_once BASE_PATH . '/config/config.php';

// Charger les fonctions helper
require_once BASE_PATH . '/helpers/functions.php';

// Charger manuellement les classes nécessaires
require_once BASE_PATH . '/app/Services/Mailer.php';

// Adresse email à tester
$testEmail = 'votre-email@example.com'; // Remplacez par votre adresse email réelle

// Créer une instance de Mailer
$mailer = new App\Services\Mailer();

// Envoyer un email de test
$subject = 'Test d\'envoi d\'email depuis votre framework';
$body = '
<html>
<head>
    <title>Test d\'email</title>
</head>
<body>
    <h1>Ceci est un test</h1>
    <p>Si vous recevez cet email, votre configuration SMTP fonctionne correctement.</p>
    <p>Date et heure du test: ' . date('Y-m-d H:i:s') . '</p>
</body>
</html>
';

echo "<h1>Test d'envoi d'email</h1>";
echo "<p>Tentative d'envoi d'un email à : {$testEmail}</p>";

$result = $mailer->send($testEmail, $subject, $body);

if ($result) {
    echo "<p style='color:green;'>✓ Email envoyé avec succès ! Vérifiez votre boîte de réception.</p>";
} else {
    echo "<p style='color:red;'>✗ Échec de l'envoi de l'email. Vérifiez votre configuration SMTP et les logs pour plus de détails.</p>";
}

// Afficher la configuration SMTP actuelle
echo "<h2>Configuration SMTP actuelle</h2>";
echo "<ul>";
echo "<li>Hôte : {$smtp['host']}</li>";
echo "<li>Port : {$smtp['port']}</li>";
echo "<li>Utilisateur : {$smtp['user']}</li>";
echo "<li>Chiffrement : {$smtp['encryption']}</li>";
echo "<li>Email d'expéditeur : {$smtp['from_email']}</li>";
echo "<li>Nom d'expéditeur : {$smtp['from_name']}</li>";
echo "</ul>";

echo "<p>Après avoir testé, n'oubliez pas de supprimer ce fichier pour des raisons de sécurité.</p>";
