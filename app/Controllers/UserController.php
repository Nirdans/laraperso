<?php
namespace App\Controllers;

use App\Models\User;

class UserController
{
    private $user;
    
    public function __construct()
    {
        // Vérifier si l'utilisateur est connecté
        if (!auth()) {
            $_SESSION['error'] = "Vous devez être connecté pour accéder à cette page.";
            redirect('/login');
            exit;
        }
        
        $this->user = new User();
    }
    
    /**
     * Affiche la liste des utilisateurs
     */
    public function index()
    {
        $users = $this->user->all('created_at', 'DESC');
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
        if (!empty($email) && $this->user->emailExists($email)) {
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
            'password' => $password
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
     * @param int $id ID de l'utilisateur
     */
    public function edit($id)
    {
        $user = $this->user->find($id);
        
        if (!$user) {
            $_SESSION['error'] = "Utilisateur non trouvé.";
            redirect('/users');
            return;
        }
        
        require BASE_PATH . '/views/users/edit.php';
    }
    
    /**
     * Met à jour un utilisateur
     * @param int $id ID de l'utilisateur
     */
    public function update($id)
    {
        $user = $this->user->find($id);
        
        if (!$user) {
            $_SESSION['error'] = "Utilisateur non trouvé.";
            redirect('/users');
            return;
        }
        
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        
        // Validation
        $errors = [];
        
        if (empty($name)) $errors[] = "Le nom est requis.";
        if (empty($email)) $errors[] = "L'email est requis.";
        
        // Vérifier si l'email existe déjà (pour un autre utilisateur)
        if (!empty($email) && $email !== $user['email'] && $this->user->emailExists($email)) {
            $errors[] = "Cet email est déjà utilisé.";
        }
        
        if (count($errors) > 0) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = ['name' => $name, 'email' => $email];
            redirect('/users/' . $id . '/edit');
            return;
        }
        
        // Mise à jour des données
        $data = [
            'name' => $name,
            'email' => $email,
        ];
        
        // Ajouter le mot de passe uniquement s'il est défini
        if (!empty($password)) {
            $data['password'] = $password;
        }
        
        $updated = $this->user->update($id, $data);
        
        if ($updated) {
            $_SESSION['success'] = "Utilisateur mis à jour avec succès.";
        } else {
            $_SESSION['error'] = "Aucune modification n'a été effectuée.";
        }
        
        redirect('/users');
    }
    
    /**
     * Supprime un utilisateur
     * @param int $id ID de l'utilisateur
     */
    public function destroy($id)
    {
        // Vérifier si l'utilisateur est l'utilisateur connecté
        if ($id == $_SESSION['user_id']) {
            $_SESSION['error'] = "Vous ne pouvez pas supprimer votre propre compte.";
            redirect('/users');
            return;
        }
        
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
        $user = current_user();
        require BASE_PATH . '/views/users/profile.php';
    }
    
    /**
     * Met à jour le profil de l'utilisateur connecté
     */
    public function updateProfile()
    {
        $user = current_user();
        
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';
        
        // Validation
        $errors = [];
        
        if (empty($name)) $errors[] = "Le nom est requis.";
        if (empty($email)) $errors[] = "L'email est requis.";
        
        // Vérifier si l'email existe déjà (pour un autre utilisateur)
        if (!empty($email) && $email !== $user['email'] && $this->user->emailExists($email)) {
            $errors[] = "Cet email est déjà utilisé.";
        }
        
        // Validation du mot de passe si un nouveau mot de passe est défini
        if (!empty($newPassword)) {
            if (empty($currentPassword)) {
                $errors[] = "Le mot de passe actuel est requis.";
            } elseif (!password_verify($currentPassword, $user['password'])) {
                $errors[] = "Le mot de passe actuel est incorrect.";
            }
            
            if ($newPassword !== $passwordConfirmation) {
                $errors[] = "Les nouveaux mots de passe ne correspondent pas.";
            }
        }
        
        if (count($errors) > 0) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = ['name' => $name, 'email' => $email];
            redirect('/profile');
            return;
        }
        
        // Mise à jour des données
        $data = [
            'name' => $name,
            'email' => $email,
        ];
        
        // Ajouter le nouveau mot de passe s'il est défini
        if (!empty($newPassword)) {
            $data['password'] = $newPassword;
        }
        
        $updated = $this->user->update($user['id'], $data);
        
        if ($updated) {
            // Mettre à jour les informations de session
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            
            $_SESSION['success'] = "Votre profil a été mis à jour avec succès.";
        } else {
            $_SESSION['error'] = "Aucune modification n'a été effectuée.";
        }
        
        redirect('/profile');
    }
}
