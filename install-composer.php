<?php
/**
 * Script d'installation et configuration de Composer
 * Exécutez ce script pour installer Composer et les dépendances
 * 
 * @author Sandrin DOSSOU <contact@sandrindossou.com>
 */

// Définir le chemin de base
define('BASE_PATH', __DIR__);

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

// Vérifie si l'extension allow_url_fopen est activée
if (!ini_get('allow_url_fopen')) {
    display_message("L'extension 'allow_url_fopen' n'est pas activée. Impossible de télécharger Composer.", 'error');
    exit(1);
}

// Vérifie si Composer est déjà installé
$composerPath = BASE_PATH . '/composer.phar';
if (!file_exists($composerPath)) {
    display_message("Installation de Composer...", 'info');
    
    // Télécharger le fichier d'installation de Composer
    $installer = file_get_contents('https://getcomposer.org/installer');
    if ($installer === false) {
        display_message("Erreur lors du téléchargement de Composer.", 'error');
        exit(1);
    }
    
    // Sauvegarder le fichier d'installation
    file_put_contents('composer-setup.php', $installer);
    
    // Vérifier la signature du fichier
    display_message("Vérification de l'intégrité du téléchargement...", 'info');
    $expected = trim(file_get_contents('https://composer.github.io/installer.sig'));
    $actual = hash_file('sha384', 'composer-setup.php');
    
    if ($expected !== $actual) {
        unlink('composer-setup.php');
        display_message("Le téléchargement est corrompu. Signature invalide.", 'error');
        exit(1);
    }
    
    // Installer Composer
    display_message("Installation de Composer dans le répertoire du projet...", 'info');
    
    ob_start();
    $result = system('php composer-setup.php --quiet', $returnCode);
    $output = ob_get_clean();
    
    unlink('composer-setup.php');
    
    if ($returnCode !== 0) {
        display_message("Erreur lors de l'installation de Composer: " . $output, 'error');
        exit(1);
    }
    
    display_message("Composer installé avec succès!", 'success');
} else {
    display_message("Composer est déjà installé.", 'success');
}

// Installer les dépendances
display_message("Installation des dépendances...", 'info');

$command = "php composer.phar install --no-interaction";
ob_start();
system($command, $returnCode);
$output = ob_get_clean();

if ($returnCode !== 0) {
    display_message("Erreur lors de l'installation des dépendances: " . $output, 'error');
    exit(1);
}

display_message("Les dépendances ont été installées avec succès!", 'success');

// Générer l'autoloader optimisé
display_message("Optimisation de l'autoloader...", 'info');

$command = "php composer.phar dump-autoload -o";
ob_start();
system($command, $returnCode);
$output = ob_get_clean();

if ($returnCode !== 0) {
    display_message("Erreur lors de l'optimisation de l'autoloader: " . $output, 'error');
} else {
    display_message("Autoloader optimisé avec succès!", 'success');
}

// Installation terminée
display_message("\nConfiguration de Composer terminée!", 'success');
display_message("Vous pouvez maintenant utiliser les composants installés via Composer.", 'info');
display_message("Pour ajouter de nouveaux packages, exécutez: php composer.phar require nom-du-package", 'info');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation de Composer | Sandrin DOSSOU</title>
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
        code {
            background-color: #f5f5f5;
            padding: 2px 4px;
            border-radius: 3px;
        }
        pre {
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <h1>Installation de Composer</h1>
    
    <div class="next-steps">
        <h2>Prochaines étapes</h2>
        <ol>
            <li>Mettez à jour votre code pour utiliser les composants installés</li>
            <li>Ajoutez d'autres dépendances selon vos besoins:
                <pre>php composer.phar require vendor/package</pre>
            </li>
            <li>Pour les environnements de production, considérez l'installation de Composer globalement</li>
        </ol>
        
        <h3>Exemple d'utilisation du composant PHPMailer</h3>
        <pre>
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = 'smtp.example.com';
$mail->SMTPAuth = true;
$mail->Username = 'user@example.com';
$mail->Password = 'secret';
$mail->setFrom('from@example.com', 'Sender Name');
$mail->addAddress('recipient@example.com', 'Recipient Name');
$mail->Subject = 'Test email using PHPMailer';
$mail->Body = 'This is the HTML message body';
$mail->send();
        </pre>
        
        <h3>Exemple d'utilisation du composant Respect\Validation</h3>
        <p>Voir l'exemple complet dans <code>/examples/validation-example.php</code></p>
        <pre>
use Respect\Validation\Validator as v;

$email = 'user@example.com';
$isValid = v::email()->validate($email);
        </pre>
        
        <a href="<?= $_SERVER['HTTP_REFERER'] ?? '/composant/empty/' ?>" class="btn">Retourner à l'application</a>
    </div>
    
    <div class="author" style="margin-top: 50px; text-align: center; font-size: 0.9em; color: #666;">
        <p>Framework développé par <a href="https://www.sandrindossou.com/" target="_blank" style="color: #4e73df; text-decoration: none;">Sandrin DOSSOU</a></p>
    </div>
</body>
</html>
