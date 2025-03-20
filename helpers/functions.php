<?php
/**
 * Fonctions utilitaires globales
 */

/**
 * Redirige vers une URL
 * @param string $path Chemin relatif ou URL complète
 * @return void
 */
function redirect($path)
{
    global $current;
    
    if (filter_var($path, FILTER_VALIDATE_URL)) {
        header("Location: {$path}");
    } else {
        $path = ltrim($path, '/');
        header("Location: {$current['domain']}/{$path}");
    }
    exit;
}

/**
 * Génère une URL complète à partir d'un chemin relatif
 * @param string $path Chemin relatif
 * @return string URL complète
 */
function url($path = '')
{
    global $current;
    $path = ltrim($path, '/');
    return $current['domain'] . '/' . $path;
}

/**
 * Génère une URL pour un asset (CSS, JS, image)
 * @param string $path Chemin relatif du fichier
 * @return string URL complète vers l'asset
 */
function asset($path)
{
    global $current;
    $path = ltrim($path, '/');
    // Retourne directement le chemin absolu pour les assets
    return $current['domain'] . '/assets/' . $path;
}

/**
 * Échappe les caractères spéciaux HTML
 * @param string $string Chaîne à échapper
 * @return string Chaîne échappée
 */
function e($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Vérifie si l'utilisateur est connecté
 * @return bool
 */
function auth()
{
    return isset($_SESSION['user_id']);
}

/**
 * Obtient l'utilisateur connecté
 * @return array|null
 */
function current_user()
{
    if (auth()) {
        $user = new App\Models\User();
        return $user->find($_SESSION['user_id']);
    }
    return null;
}

/**
 * Vérifie si une requête est en AJAX
 * @return bool
 */
function is_ajax()
{
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

/**
 * Génère un jeton CSRF
 * @return string
 */
function csrf_token()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie si le jeton CSRF est valide
 * @return bool
 */
function csrf_check()
{
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
        return false;
    }
    
    return $_POST['csrf_token'] === $_SESSION['csrf_token'];
}

/**
 * Affiche un message flash
 * @param string $type Type de message (success, error, warning, info)
 * @return string HTML du message
 */
function flash_message($type)
{
    if (isset($_SESSION[$type])) {
        $message = $_SESSION[$type];
        unset($_SESSION[$type]);
        return '<div class="alert alert-' . $type . '">' . e($message) . '</div>';
    }
    return '';
}

/**
 * Affiche les erreurs de validation
 * @return string HTML des erreurs
 */
function validation_errors()
{
    if (isset($_SESSION['errors']) && is_array($_SESSION['errors'])) {
        $errors = $_SESSION['errors'];
        unset($_SESSION['errors']);
        
        $html = '<div class="alert alert-danger"><ul>';
        foreach ($errors as $error) {
            $html .= '<li>' . e($error) . '</li>';
        }
        $html .= '</ul></div>';
        
        return $html;
    }
    return '';
}

/**
 * Récupère une ancienne valeur de formulaire
 * @param string $key Clé
 * @param string $default Valeur par défaut
 * @return string
 */
function old($key, $default = '')
{
    if (isset($_SESSION['old'][$key])) {
        $value = $_SESSION['old'][$key];
        return e($value);
    }
    return e($default);
}
