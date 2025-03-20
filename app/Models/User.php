<?php
namespace App\Models;

class User extends Model
{
    /**
     * Nom de la table
     * @var string
     */
    protected $table = 'users';
    
    /**
     * Liste des champs remplissables
     * @var array
     */
    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'remember_token', 
        'google_id', 
        'facebook_id',
        'profile_image'
    ];
    
    /**
     * Liste des casts
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    /**
     * Champs non retournés dans les tableaux
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];
    
    /**
     * Crée la table des utilisateurs
     * @return void
     */
    public static function createTable()
    {
        $db = Database::getInstance();
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            remember_token VARCHAR(255) NULL,
            google_id VARCHAR(255) NULL,
            facebook_id VARCHAR(255) NULL,
            profile_image VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        $db->query($sql);
    }
    
    /**
     * Authentifie un utilisateur
     * @param string $email Email
     * @param string $password Mot de passe
     * @return array|false
     */
    public function authenticate($email, $password)
    {
        $user = $this->findBy('email', $email);
        
        if (!$user) {
            return false;
        }
        
        if (password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Trouve un utilisateur par son token de rappel
     * @param string $token Token
     * @return array|false
     */
    public function findByRememberToken($token)
    {
        return $this->findBy('remember_token', $token);
    }
    
    /**
     * Trouve un utilisateur par son ID Google
     * @param string $googleId ID Google
     * @return array|false
     */
    public function findByGoogleId($googleId)
    {
        return $this->findBy('google_id', $googleId);
    }
    
    /**
     * Trouve un utilisateur par son ID Facebook
     * @param string $facebookId ID Facebook
     * @return array|false
     */
    public function findByFacebookId($facebookId)
    {
        return $this->findBy('facebook_id', $facebookId);
    }
    
    /**
     * Vérifie si l'email existe déjà
     * @param string $email Email
     * @return bool
     */
    public function emailExists($email)
    {
        return $this->findBy('email', $email) !== false;
    }
    
    /**
     * Crée un utilisateur
     * @param array $data Données
     * @return int|bool ID de l'utilisateur ou false
     */
    public function create($data)
    {
        // Hasher le mot de passe s'il n'est pas déjà hashé
        if (isset($data['password']) && strlen($data['password']) < 60) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        return parent::create($data);
    }
    
    /**
     * Met à jour un utilisateur
     * @param int $id ID
     * @param array $data Données
     * @return int Nombre de lignes affectées
     */
    public function update($id, $data)
    {
        // Hasher le mot de passe s'il est défini et n'est pas déjà hashé
        if (isset($data['password']) && !empty($data['password']) && strlen($data['password']) < 60) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } elseif (isset($data['password']) && empty($data['password'])) {
            // Si le mot de passe est vide, le supprimer pour ne pas le mettre à jour
            unset($data['password']);
        }
        
        return parent::update($id, $data);
    }
}
