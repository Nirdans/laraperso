<?php
namespace App\Models;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'remember_token',
        'email_verified_at',
        'google_id',
        'facebook_id',
        'profile_image'
    ];
    
    /**
     * Crée la table users si elle n'existe pas
     */
    public static function createTable()
    {
        $db = \App\Services\Database::getInstance();
        
        $columns = [
            'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
            'name' => 'VARCHAR(255) NOT NULL',
            'email' => 'VARCHAR(255) NOT NULL UNIQUE',
            'password' => 'VARCHAR(255) NOT NULL',
            'remember_token' => 'VARCHAR(255) NULL',
            'email_verified_at' => 'TIMESTAMP NULL',
            'google_id' => 'VARCHAR(255) NULL',
            'facebook_id' => 'VARCHAR(255) NULL',
            'profile_image' => 'VARCHAR(255) NULL',
            'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ];
        
        return $db->createTable('users', $columns);
    }
    
    /**
     * Vérifie si les identifiants sont valides
     */
    public function authenticate($email, $password)
    {
        $user = $this->findBy('email', $email);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
}
