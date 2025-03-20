<?php
namespace App\Controllers;

use App\Models\User;
use App\Services\Mailer;

class AuthController
{
    private $user;
    private $mailer;
    
    public function __construct()
    {
        $this->user = new User();
        $this->mailer = new Mailer();
    }
    
    /**
     * Affiche le formulaire de connexion
     */
    public function showLoginForm()
    {
        require BASE_PATH . '/views/auth/login.php';
    }
    
    /**
     * Traitement de la connexion
     */
    public function login()
    {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        $user = $this->user->authenticate($email, $password);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + 3600 * 24 * 30, '/');
                $this->user->update($user['id'], ['remember_token' => $token]);
            }
            
            redirect('/home');
        } else {
            $_SESSION['error'] = "Email ou mot de passe incorrect.";
            redirect('/login');
        }
    }
    
    /**
     * Affiche le formulaire d'inscription
     */
    public function showRegisterForm()
    {
        require BASE_PATH . '/views/auth/register.php';
    }
    
    /**
     * Traitement de l'inscription
     */
    public function register()
    {
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $password_confirmation = $_POST['password_confirmation'] ?? '';
        
        // Validation
        $errors = [];
        
        if (empty($name)) $errors[] = "Le nom est requis.";
        if (empty($email)) $errors[] = "L'email est requis.";
        if (empty($password)) $errors[] = "Le mot de passe est requis.";
        if ($password !== $password_confirmation) $errors[] = "Les mots de passe ne correspondent pas.";
        
        if (count($errors) > 0) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = ['name' => $name, 'email' => $email];
            redirect('/register');
            return;
        }
        
        // Vérifier si l'email existe déjà
        $existingUser = $this->user->findBy('email', $email);
        if ($existingUser) {
            $_SESSION['error'] = "Cet email est déjà utilisé.";
            $_SESSION['old'] = ['name' => $name, 'email' => $email];
            redirect('/register');
            return;
        }
        
        // Créer l'utilisateur
        $userId = $this->user->create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);
        
        if ($userId) {
            $_SESSION['success'] = "Compte créé avec succès. Vous pouvez vous connecter.";
            redirect('/login');
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la création du compte.";
            redirect('/register');
        }
    }
    
    /**
     * Déconnexion
     */
    public function logout()
    {
        session_destroy();
        setcookie('remember_token', '', time() - 3600, '/');
        redirect('/login');
    }
    
    /**
     * Affiche le formulaire de mot de passe oublié
     */
    public function showForgotForm()
    {
        require BASE_PATH . '/views/auth/forgot-password.php';
    }
    
    /**
     * Traitement de la demande de réinitialisation
     */
    public function forgotPassword()
    {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        
        // Vérifier si l'utilisateur existe
        $user = $this->user->findBy('email', $email);
        
        if ($user) {
            // Génération d'un token
            $token = bin2hex(random_bytes(32));
            $this->user->update($user['id'], ['remember_token' => $token]);
            
            // Envoi d'un email avec le lien de réinitialisation
            $resetLink = url("/reset-password/{$token}");
            $emailSent = $this->mailer->sendPasswordReset($email, $resetLink, $user['name']);
            
            if ($emailSent) {
                $_SESSION['success'] = "Un lien de réinitialisation a été envoyé à votre adresse email.";
            } else {
                // Email non envoyé, mais ne pas révéler l'information
                $_SESSION['success'] = "Si un compte est associé à cet email, un lien de réinitialisation vous a été envoyé.";
                // Log pour le débug
                error_log("Échec de l'envoi d'email à {$email}");
            }
        } else {
            // Ne pas révéler si l'email existe ou non
            $_SESSION['success'] = "Si un compte est associé à cet email, un lien de réinitialisation vous a été envoyé.";
        }
        
        redirect('/forgot-password');
    }
    
    /**
     * Affiche le formulaire de réinitialisation du mot de passe
     */
    public function showResetForm($token)
    {
        $user = $this->user->findBy('remember_token', $token);
        
        if (!$user) {
            $_SESSION['error'] = "Ce lien de réinitialisation est invalide ou a expiré.";
            redirect('/forgot-password');
        }
        
        require BASE_PATH . '/views/auth/reset-password.php';
    }
    
    /**
     * Traitement de la réinitialisation du mot de passe
     */
    public function resetPassword()
    {
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $password_confirmation = $_POST['password_confirmation'] ?? '';
        
        if ($password !== $password_confirmation) {
            $_SESSION['error'] = "Les mots de passe ne correspondent pas.";
            redirect("/reset-password/{$token}");
            return;
        }
        
        $user = $this->user->findBy('remember_token', $token);
        
        if (!$user) {
            $_SESSION['error'] = "Ce lien de réinitialisation est invalide ou a expiré.";
            redirect('/forgot-password');
            return;
        }
        
        $this->user->update($user['id'], [
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'remember_token' => null
        ]);
        
        $_SESSION['success'] = "Votre mot de passe a été réinitialisé avec succès. Vous pouvez vous connecter.";
        redirect('/login');
    }
    
    /**
     * Redirection vers Google pour authentification
     */
    public function redirectToGoogle()
    {
        global $auth;
        // Implémentation à faire avec une librairie OAuth
        $clientId = $auth['google']['client_id'];
        $redirectUri = url($auth['google']['redirect']);
        
        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'email profile',
            'access_type' => 'online',
        ];
        
        $url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
        redirect($url);
    }
    
    /**
     * Traite le retour d'authentification Google
     */
    public function handleGoogleCallback()
    {
        global $auth;
        // Implémentation à faire avec une librairie OAuth
        
        // Simulons une authentification réussie pour l'exemple
        $_SESSION['success'] = "Authentification Google réussie!";
        redirect('/home');
    }
    
    /**
     * Redirection vers Facebook pour authentification
     */
    public function redirectToFacebook()
    {
        global $auth;
        // Implémentation à faire avec une librairie OAuth
        $clientId = $auth['facebook']['client_id'];
        $redirectUri = url($auth['facebook']['redirect']);
        
        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'email',
        ];
        
        $url = 'https://www.facebook.com/v12.0/dialog/oauth?' . http_build_query($params);
        redirect($url);
    }
    
    /**
     * Traite le retour d'authentification Facebook
     */
    public function handleFacebookCallback()
    {
        global $auth;
        // Implémentation à faire avec une librairie OAuth
        
        // Simulons une authentification réussie pour l'exemple
        $_SESSION['success'] = "Authentification Facebook réussie!";
        redirect('/home');
    }
}
