<?php
/**
 * Exemple d'utilisation du composant Respect\Validation
 * https://github.com/Respect/Validation
 * 
 * @author Sandrin DOSSOU <contact@sandrindossou.com>
 */

// Définir le chemin de base
define('BASE_PATH', dirname(__DIR__));

// Charger l'autoloader de Composer
require BASE_PATH . '/vendor/autoload.php';

// Importer le namespace pour la validation
use Respect\Validation\Validator as v;

// Exemple simple de validation
$email = 'test@example.com';
$isValidEmail = v::email()->validate($email);

echo "<h1>Exemples de validation avec Respect/Validation</h1>";

echo "<h2>Validation d'email</h2>";
echo "Email: {$email}<br>";
echo "Valide: " . ($isValidEmail ? 'Oui' : 'Non') . "<br><br>";

// Exemple de validation de mot de passe
$password = 'Password123!';
$isValidPassword = v::length(8, null)
    ->uppercase()
    ->lowercase()
    ->digit()
    ->specialChar()
    ->validate($password);

echo "<h2>Validation de mot de passe</h2>";
echo "Mot de passe: {$password}<br>";
echo "Valide: " . ($isValidPassword ? 'Oui' : 'Non') . "<br>";
echo "Doit contenir: au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial<br><br>";

// Validation d'un formulaire
echo "<h2>Validation d'un formulaire</h2>";

$formData = [
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'age' => '30',
    'website' => 'https://example.com',
    'comment' => 'Ceci est un commentaire'
];

// Définir les règles de validation
$rules = [
    'name' => v::notEmpty()->alpha(' ')->length(2, 100),
    'email' => v::notEmpty()->email(),
    'age' => v::notEmpty()->numeric()->between(18, 120),
    'website' => v::optional(v::url()),
    'comment' => v::notEmpty()->length(3, 500)
];

// Valider le formulaire
$errors = [];
foreach ($rules as $field => $rule) {
    try {
        $rule->assert($formData[$field] ?? null);
    } catch (\Respect\Validation\Exceptions\ValidationException $e) {
        $errors[$field] = $e->getMessage();
    }
}

// Afficher les résultats
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>Champ</th><th>Valeur</th><th>Résultat</th></tr>";

foreach ($formData as $field => $value) {
    echo "<tr>";
    echo "<td>{$field}</td>";
    echo "<td>{$value}</td>";
    echo "<td>" . (isset($errors[$field]) ? "<span style='color:red'>{$errors[$field]}</span>" : "<span style='color:green'>Valide</span>") . "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<p>Vous pouvez intégrer ce type de validation dans vos contrôleurs ou créer une classe ValidationService dans votre framework.</p>";
