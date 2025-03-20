<?php
namespace App\Controllers;

use App\Models\User;

class UserController
{
    private $user;
    
    public function __construct()
    {
        $this->user = new User();
        
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            redirect('/login');
        }
    }
    
    /**
     * Liste des utilisateurs (CRUD exemple)
     */
    public function index()
    {
        $users = $this->user->all();
        require BASE_PATH . '/views/users/index.php';
    }
    
    /**
     * Affiche le formulaire de création d'un utilisateur
     */
    public function create()
    {
        require BASE_PATH . '/views/users/create.php';
    }
    
    /**
     * Enregistre un nouvel utilisateur
     */
    public function store()
    {
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        
        // Validation
        $errors = [];
        
        if (empty($name)) $errors[] = "Le nom est requis.";
        if (empty($email)) $errors[] = "L'email est requis.";
        if (empty($password)) $errors[] = "Le mot de passe est requis.";
        
        // Vérifier si l'email existe déjà
        $existingUser = $this->user->findBy('email', $email);
        if ($existingUser) {
            $errors[] = "Cet email est déjà utilisé.";
        }
        
        if (count($errors) > 0) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = ['name' => $name, 'email' => $email];
            redirect('/users/create');
            return;
        }
        
        // Créer l'utilisateur
        $userId = $this->user->create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);
        
        if ($userId) {
            $_SESSION['success'] = "Utilisateur créé avec succès.";
            redirect('/users');
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la création de l'utilisateur.";
            redirect('/users/create');
        }
    }
    
    /**
     * Affiche le formulaire d'édition d'un utilisateur
     */
    public function edit($id)
    {
        $user = $this->user->find($id);
        
        if (!$user) {
            $_SESSION['error'] = "Utilisateur non trouvé.";
            redirect('/users');
        }
        
        require BASE_PATH . '/views/users/edit.php';
    }
    
    /**
     * Met à jour un utilisateur
     */
    public function update($id)
    {
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? null;
        
        // Validation
        $errors = [];
        
        if (empty($name)) $errors[] = "Le nom est requis.";
        if (empty($email)) $errors[] = "L'email est requis.";
        
        // Vérifier si l'utilisateur existe
        $existingUser = $this->user->find($id);
        if (!$existingUser) {
            $_SESSION['error'] = "Utilisateur non trouvé.";
            redirect('/users');
            return;
        }
        
        // Vérifier si l'email existe déjà pour un autre utilisateur
        $emailUser = $this->user->findBy('email', $email);
        if ($emailUser && $emailUser['id'] != $id) {
            $errors[] = "Cet email est déjà utilisé par un autre utilisateur.";
        }
        
        if (count($errors) > 0) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = ['name' => $name, 'email' => $email];
            redirect("/users/{$id}/edit");
            return;
        }
        
        // Préparer les données à mettre à jour
        $data = [
            'name' => $name,
            'email' => $email
        ];
        
        // Mettre à jour le mot de passe si fourni
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        
        // Mettre à jour l'utilisateur
        $updated = $this->user->update($id, $data);
        
        if ($updated) {
            $_SESSION['success'] = "Utilisateur mis à jour avec succès.";
            redirect('/users');
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la mise à jour de l'utilisateur.";
            redirect("/users/{$id}/edit");
        }
    }
    
    /**
     * Supprime un utilisateur
     */
    public function destroy($id)
    {
        // Vérifier si l'utilisateur existe
        $existingUser = $this->user->find($id);
        if (!$existingUser) {
            $_SESSION['error'] = "Utilisateur non trouvé.";
            redirect('/users');
            return;
        }
        
        // Empêcher la suppression de son propre compte
        if ($id == $_SESSION['user_id']) {
            $_SESSION['error'] = "Vous ne pouvez pas supprimer votre propre compte.";
            redirect('/users');
            return;
        }
        
        // Supprimer l'utilisateur
        $deleted = $this->user->delete($id);
        
        if ($deleted) {
            $_SESSION['success'] = "Utilisateur supprimé avec succès.";
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la suppression de l'utilisateur.";
        }
        
        redirect('/users');
    }
    
    /**
     * Affiche le profil de l'utilisateur connecté
     */
    public function profile()
    {
        $user = $this->user->find($_SESSION['user_id']);
        
        if (!$user) {
            $_SESSION['error'] = "Utilisateur non trouvé.";
            redirect('/logout');
            return;
        }
        
        require BASE_PATH . '/views/users/profile.php';
    }
    
    /**
     * Met à jour le profil de l'utilisateur connecté
     */
    public function updateProfile()
    {
        $id = $_SESSION['user_id'];
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $password_confirmation = $_POST['password_confirmation'] ?? '';
        
        // Validation
        $errors = [];
        $user = $this->user->find($id);
        
        if (!$user) {
            $_SESSION['error'] = "Utilisateur non trouvé.";
            redirect('/logout');
            return;
        }
        
        if (empty($name)) $errors[] = "Le nom est requis.";
        if (empty($email)) $errors[] = "L'email est requis.";
        
        // Vérifier si l'email existe déjà pour un autre utilisateur
        $emailUser = $this->user->findBy('email', $email);
        if ($emailUser && $emailUser['id'] != $id) {
            $errors[] = "Cet email est déjà utilisé par un autre utilisateur.";
        }
        
        // Si l'utilisateur veut changer son mot de passe
        if (!empty($new_password)) {
            if (empty($current_password)) {
                $errors[] = "Le mot de passe actuel est requis.";
            } elseif (!password_verify($current_password, $user['password'])) {
                $errors[] = "Le mot de passe actuel est incorrect.";
            }
            
            if ($new_password !== $password_confirmation) {
                $errors[] = "Les nouveaux mots de passe ne correspondent pas.";
            }
        }
        
        if (count($errors) > 0) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = ['name' => $name, 'email' => $email];
            redirect('/profile');
            return;
        }
        
        // Préparer les données à mettre à jour
        $data = [
            'name' => $name,
            'email' => $email
        ];
        
        // Mettre à jour le mot de passe si fourni
        if (!empty($new_password)) {
            $data['password'] = password_hash($new_password, PASSWORD_DEFAULT);
        }
        
        // Mettre à jour l'utilisateur
        $updated = $this->user->update($id, $data);
        
        if ($updated) {
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['success'] = "Profil mis à jour avec succès.";
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la mise à jour du profil.";
        }
        
        redirect('/profile');
    }
}
