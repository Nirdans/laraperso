<?php
namespace App\Services;

class Session
{
    /**
     * Instance unique de la classe (pattern Singleton)
     * @var Session|null
     */
    private static $instance = null;
    
    /**
     * Indique si la session est démarrée
     * @var bool
     */
    private $started = false;
    
    /**
     * Nom de la session
     * @var string
     */
    private $name = 'sandrin_session';
    
    /**
     * Durée de vie de la session en secondes (2 heures par défaut)
     * @var int
     */
    private $lifetime = 72000000;
    
    /**
     * Constructeur privé (pattern Singleton)
     */
    private function __construct()
    {
        // Empêcher l'instanciation directe
    }
    
    /**
     * Obtient l'instance unique de la classe
     * @return Session
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Démarrer la session avec des paramètres sécurisés
     * @return bool
     */
    public function start()
    {
        if ($this->started) {
            return true;
        }
        
        // Définir les options de la session
        $cookieParams = session_get_cookie_params();
        session_set_cookie_params([
            'lifetime' => $this->lifetime,
            'path' => '/',
            'domain' => $cookieParams['domain'],
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        
        // Définir un nom de session personnalisé pour éviter les attaques par défaut
        session_name($this->name);
        
        // Démarrer la session
        $started = session_start();
        $this->started = $started;
        
        if ($started) {
            // Régénérer l'ID de session pour éviter la fixation de session
            if (!isset($_SESSION['_initialized'])) {
                session_regenerate_id(true);
                $_SESSION['_initialized'] = true;
                $_SESSION['_created'] = time();
                $_SESSION['_ip'] = $_SERVER['REMOTE_ADDR'];
                $_SESSION['_user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? null;
            }
            
            // Vérifier les informations de sécurité de la session
            if ($this->shouldRegenerateSession()) {
                session_regenerate_id(true);
                $_SESSION['_created'] = time();
            }
            
            // Vérifier si la session est piratée
            if ($this->isSessionHijacked()) {
                $this->destroy();
                $this->start();
                return false;
            }
        }
        
        return $started;
    }
    
    /**
     * Détermine s'il faut régénérer l'ID de session (toutes les 30 minutes)
     * @return bool
     */
    private function shouldRegenerateSession()
    {
        if (!isset($_SESSION['_created'])) {
            return true;
        }
        
        return ($_SESSION['_created'] + 1800) < time();
    }
    
    /**
     * Vérifie si la session a été détournée en comparant l'IP et l'User Agent
     * @return bool
     */
    private function isSessionHijacked()
    {
        if (!isset($_SESSION['_ip']) || !isset($_SESSION['_user_agent'])) {
            return false;
        }
        
        return $_SESSION['_ip'] !== $_SERVER['REMOTE_ADDR'] ||
               ($_SESSION['_user_agent'] !== null && 
                $_SESSION['_user_agent'] !== ($_SERVER['HTTP_USER_AGENT'] ?? null));
    }
    
    /**
     * Obtient une valeur de la session
     * @param string $key Clé
     * @param mixed $default Valeur par défaut
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $this->ensureStarted();
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Définit une valeur dans la session
     * @param string $key Clé
     * @param mixed $value Valeur
     * @return $this
     */
    public function set($key, $value)
    {
        $this->ensureStarted();
        $_SESSION[$key] = $value;
        return $this;
    }
    
    /**
     * Vérifie si une clé existe dans la session
     * @param string $key Clé
     * @return bool
     */
    public function has($key)
    {
        $this->ensureStarted();
        return isset($_SESSION[$key]);
    }
    
    /**
     * Supprime une valeur de la session
     * @param string $key Clé
     * @return $this
     */
    public function remove($key)
    {
        $this->ensureStarted();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
        return $this;
    }
    
    /**
     * Obtient et supprime une valeur de la session (flash)
     * @param string $key Clé
     * @param mixed $default Valeur par défaut
     * @return mixed
     */
    public function flash($key, $default = null)
    {
        $this->ensureStarted();
        $value = $_SESSION[$key] ?? $default;
        $this->remove($key);
        return $value;
    }
    
    /**
     * Définit un message flash
     * @param string $type Type de message (success, error, warning, info)
     * @param string $message Message
     * @return $this
     */
    public function setFlash($type, $message)
    {
        $this->ensureStarted();
        $_SESSION[$type] = $message;
        return $this;
    }
    
    /**
     * Définir les anciennes valeurs pour les formulaires
     * @param array $values Valeurs
     * @return $this
     */
    public function setOld(array $values)
    {
        $this->ensureStarted();
        $_SESSION['old'] = $values;
        return $this;
    }
    
    /**
     * Définit les erreurs de validation
     * @param array $errors Erreurs
     * @return $this
     */
    public function setErrors(array $errors)
    {
        $this->ensureStarted();
        $_SESSION['errors'] = $errors;
        return $this;
    }
    
    /**
     * Supprime toutes les données de la session
     * @return $this
     */
    public function clear()
    {
        $this->ensureStarted();
        $_SESSION = [];
        return $this;
    }
    
    /**
     * Détruire complètement la session
     * @return bool
     */
    public function destroy()
    {
        $this->ensureStarted();
        
        // Supprimer toutes les données de la session
        $_SESSION = [];
        
        // Supprimer le cookie de session
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
        
        // Détruire la session
        $destroyed = session_destroy();
        $this->started = false;
        
        return $destroyed;
    }
    
    /**
     * S'assure que la session est démarrée
     * @return void
     */
    private function ensureStarted()
    {
        if (!$this->started) {
            $this->start();
        }
    }
}
