<?php
namespace App\Core;

use App\Services\Database;

abstract class Migration
{
    /**
     * Instance de la base de données
     * @var Database
     */
    protected $db;
    
    /**
     * Nom de la table des migrations
     * @var string
     */
    protected $migrationsTable = 'migrations';
    
    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->ensureMigrationsTable();
    }
    
    /**
     * S'assure que la table des migrations existe
     * @return void
     */
    protected function ensureMigrationsTable()
    {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS {$this->migrationsTable} (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                batch INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $this->db->query($sql);
        } catch (\Exception $e) {
            die("Erreur lors de la création de la table des migrations: " . $e->getMessage());
        }
    }
    
    /**
     * Méthode abstraite pour les migrations montantes
     * @return void
     */
    abstract public function up();
    
    /**
     * Méthode abstraite pour les migrations descendantes
     * @return void
     */
    abstract public function down();
    
    /**
     * Créer une table
     * @param string $name Nom de la table
     * @param callable $callback Fonction de définition des colonnes
     * @return void
     */
    protected function createTable($name, $callback)
    {
        $schema = new Schema();
        $callback($schema);
        
        $sql = "CREATE TABLE IF NOT EXISTS {$name} (
            {$schema->getColumns()}
        )";
        
        $this->db->query($sql);
    }
    
    /**
     * Supprimer une table
     * @param string $name Nom de la table
     * @return void
     */
    protected function dropTable($name)
    {
        $sql = "DROP TABLE IF EXISTS {$name}";
        $this->db->query($sql);
    }
    
    /**
     * Modifier une table existante
     * @param string $name Nom de la table
     * @param callable $callback Fonction de modification des colonnes
     * @return void
     */
    protected function alterTable($name, $callback)
    {
        $schema = new Schema();
        $callback($schema);
        
        foreach ($schema->getAlterations() as $alteration) {
            $sql = "ALTER TABLE {$name} {$alteration}";
            $this->db->query($sql);
        }
    }
}

class Schema
{
    /**
     * Les colonnes de la table
     * @var array
     */
    protected $columns = [];
    
    /**
     * Les modifications de la table
     * @var array
     */
    protected $alterations = [];
    
    /**
     * Ajouter une colonne d'identifiant
     * @param string $name Nom de la colonne
     * @return $this
     */
    public function id($name = 'id')
    {
        $this->columns[] = "{$name} INT AUTO_INCREMENT PRIMARY KEY";
        return $this;
    }
    
    /**
     * Ajouter une colonne de type INT
     * @param string $name Nom de la colonne
     * @param int $length Longueur (facultatif)
     * @return $this
     */
    public function integer($name, $length = null)
    {
        $type = $length ? "INT({$length})" : "INT";
        $this->columns[] = "{$name} {$type}";
        return $this;
    }
    
    /**
     * Ajouter une colonne de type VARCHAR
     * @param string $name Nom de la colonne
     * @param int $length Longueur (255 par défaut)
     * @return $this
     */
    public function string($name, $length = 255)
    {
        $this->columns[] = "{$name} VARCHAR({$length})";
        return $this;
    }
    
    /**
     * Ajouter une colonne de type TEXT
     * @param string $name Nom de la colonne
     * @return $this
     */
    public function text($name)
    {
        $this->columns[] = "{$name} TEXT";
        return $this;
    }
    
    /**
     * Ajouter une colonne de type BOOLEAN
     * @param string $name Nom de la colonne
     * @return $this
     */
    public function boolean($name)
    {
        $this->columns[] = "{$name} TINYINT(1)";
        return $this;
    }
    
    /**
     * Ajouter une colonne de type DATETIME
     * @param string $name Nom de la colonne
     * @return $this
     */
    public function datetime($name)
    {
        $this->columns[] = "{$name} DATETIME";
        return $this;
    }
    
    /**
     * Ajouter une colonne de type TIMESTAMP
     * @param string $name Nom de la colonne
     * @return $this
     */
    public function timestamp($name)
    {
        $this->columns[] = "{$name} TIMESTAMP";
        return $this;
    }
    
    /**
     * Ajouter des colonnes de timestamp de création/modification
     * @return $this
     */
    public function timestamps()
    {
        $this->columns[] = "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        return $this;
    }
    
    /**
     * Ajouter une colonne de clé étrangère
     * @param string $name Nom de la colonne
     * @param string $reference Table et colonne référencées (ex: "users(id)")
     * @return $this
     */
    public function foreignKey($name, $reference)
    {
        $parts = explode('(', $reference);
        $table = trim($parts[0]);
        $column = trim(str_replace(')', '', $parts[1]));
        
        $this->integer($name);
        $this->columns[] = "FOREIGN KEY ({$name}) REFERENCES {$table}({$column})";
        
        return $this;
    }
    
    /**
     * Définir une colonne comme non NULL
     * @return $this
     */
    public function notNull()
    {
        $lastIndex = count($this->columns) - 1;
        $this->columns[$lastIndex] .= " NOT NULL";
        return $this;
    }
    
    /**
     * Définir une valeur par défaut pour une colonne
     * @param mixed $value Valeur par défaut
     * @return $this
     */
    public function default($value)
    {
        $lastIndex = count($this->columns) - 1;
        
        if (is_null($value)) {
            $this->columns[$lastIndex] .= " DEFAULT NULL";
        } elseif (is_bool($value)) {
            $this->columns[$lastIndex] .= " DEFAULT " . ($value ? "1" : "0");
        } elseif (is_string($value)) {
            $this->columns[$lastIndex] .= " DEFAULT '" . $value . "'";
        } else {
            $this->columns[$lastIndex] .= " DEFAULT " . $value;
        }
        
        return $this;
    }
    
    /**
     * Définir une colonne comme UNIQUE
     * @return $this
     */
    public function unique()
    {
        $lastIndex = count($this->columns) - 1;
        $columnName = explode(' ', $this->columns[$lastIndex])[0];
        $this->columns[] = "UNIQUE KEY ({$columnName})";
        return $this;
    }
    
    /**
     * Ajouter une colonne à une table existante
     * @param string $name Nom de la colonne
     * @param string $type Type de la colonne
     * @return $this
     */
    public function addColumn($name, $type)
    {
        $this->alterations[] = "ADD COLUMN {$name} {$type}";
        return $this;
    }
    
    /**
     * Modifier une colonne existante
     * @param string $name Nom de la colonne
     * @param string $type Type de la colonne
     * @return $this
     */
    public function modifyColumn($name, $type)
    {
        $this->alterations[] = "MODIFY COLUMN {$name} {$type}";
        return $this;
    }
    
    /**
     * Supprimer une colonne
     * @param string $name Nom de la colonne
     * @return $this
     */
    public function dropColumn($name)
    {
        $this->alterations[] = "DROP COLUMN {$name}";
        return $this;
    }
    
    /**
     * Ajouter un index
     * @param string|array $columns Nom(s) de la/les colonne(s)
     * @param string $name Nom de l'index (facultatif)
     * @return $this
     */
    public function addIndex($columns, $name = null)
    {
        $cols = is_array($columns) ? implode(', ', $columns) : $columns;
        $indexName = $name ?: 'idx_' . str_replace(', ', '_', $cols);
        $this->alterations[] = "ADD INDEX {$indexName} ({$cols})";
        return $this;
    }
    
    /**
     * Supprimer un index
     * @param string $name Nom de l'index
     * @return $this
     */
    public function dropIndex($name)
    {
        $this->alterations[] = "DROP INDEX {$name}";
        return $this;
    }
    
    /**
     * Obtenir les colonnes sous forme de chaîne
     * @return string
     */
    public function getColumns()
    {
        return implode(', ', $this->columns);
    }
    
    /**
     * Obtenir les modifications
     * @return array
     */
    public function getAlterations()
    {
        return $this->alterations;
    }
}
