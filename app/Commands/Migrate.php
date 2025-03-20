<?php
namespace App\Commands;

use App\Core\Migration;
use App\Services\Database;

class Migrate
{
    /**
     * Instance de la base de données
     * @var \App\Services\Database
     */
    protected $db;
    
    /**
     * Répertoire des migrations
     * @var string
     */
    protected $migrationsPath;
    
    /**
     * Table des migrations
     * @var string
     */
    protected $migrationsTable = 'migrations';
    
    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->migrationsPath = BASE_PATH . '/database/migrations/';
        $this->ensureMigrationsDirectory();
    }
    
    /**
     * Exécute toutes les migrations en attente
     * @return void
     */
    public function run()
    {
        $this->ensureMigrationsTable();
        
        $migrations = $this->getPendingMigrations();
        
        if (empty($migrations)) {
            echo "Aucune migration en attente.\n";
            return;
        }
        
        $batch = $this->getNextBatchNumber();
        
        foreach ($migrations as $migration) {
            $this->runUp($migration, $batch);
        }
        
        echo "Migrations terminées.\n";
    }
    
    /**
     * Annule la dernière migration exécutée
     * @return void
     */
    public function rollback($steps = 1)
    {
        $this->ensureMigrationsTable();
        
        $migrations = $this->getLastBatchMigrations($steps);
        
        if (empty($migrations)) {
            echo "Rien à annuler.\n";
            return;
        }
        
        foreach ($migrations as $migration) {
            $this->runDown($migration);
        }
        
        echo "Rollback terminé.\n";
    }
    
    /**
     * Réinitialise toutes les migrations
     * @return void
     */
    public function reset()
    {
        $this->ensureMigrationsTable();
        
        $migrations = $this->getAllMigrations();
        
        if (empty($migrations)) {
            echo "Aucune migration à réinitialiser.\n";
            return;
        }
        
        foreach (array_reverse($migrations) as $migration) {
            $this->runDown($migration);
        }
        
        echo "Reset terminé.\n";
    }
    
    /**
     * Actualise toutes les migrations (reset + run)
     * @return void
     */
    public function refresh()
    {
        $this->reset();
        $this->run();
        
        echo "Refresh terminé.\n";
    }
    
    /**
     * Exécute une migration spécifique
     * @param string $migrationName Nom de la migration
     * @param int $batch Numéro du batch
     * @return void
     */
    protected function runUp($migrationName, $batch)
    {
        $file = $this->migrationsPath . $migrationName . '.php';
        
        if (!file_exists($file)) {
            echo "Migration {$migrationName} introuvable.\n";
            return false;
        }
        
        require_once $file;
        
        $class = $this->getMigrationClass($migrationName);
        $migration = new $class();
        
        echo "Migration {$migrationName} en cours...\n";
        $migration->up();
        
        $this->db->insert($this->migrationsTable, [
            'migration' => $migrationName,
            'batch' => $batch
        ]);
        
        echo "Migration {$migrationName} terminée.\n";
        
        return true;
    }
    
    /**
     * Annule une migration spécifique
     * @param array $migration Données de la migration
     * @return void
     */
    protected function runDown($migration)
    {
        $file = $this->migrationsPath . $migration['migration'] . '.php';
        
        if (!file_exists($file)) {
            echo "Migration {$migration['migration']} introuvable.\n";
            return false;
        }
        
        require_once $file;
        
        $class = $this->getMigrationClass($migration['migration']);
        $instance = new $class();
        
        echo "Annulation de la migration {$migration['migration']} en cours...\n";
        $instance->down();
        
        $this->db->delete($this->migrationsTable, ['id' => $migration['id']]);
        
        echo "Annulation de la migration {$migration['migration']} terminée.\n";
        
        return true;
    }
    
    /**
     * Récupère toutes les migrations qui n'ont pas encore été exécutées
     * @return array
     */
    protected function getPendingMigrations()
    {
        $files = $this->getMigrationFiles();
        $executed = $this->getExecutedMigrations();
        
        return array_diff($files, $executed);
    }
    
    /**
     * Récupère les migrations du dernier batch
     * @param int $steps Nombre de batchs à annuler
     * @return array
     */
    protected function getLastBatchMigrations($steps = 1)
    {
        $query = "SELECT * FROM {$this->migrationsTable} WHERE batch IN 
                 (SELECT batch FROM {$this->migrationsTable} ORDER BY batch DESC LIMIT ?)
                 ORDER BY id DESC";
                 
        return $this->db->query($query, [$steps]);
    }
    
    /**
     * Récupère toutes les migrations exécutées
     * @return array
     */
    protected function getAllMigrations()
    {
        return $this->db->query("SELECT * FROM {$this->migrationsTable} ORDER BY batch ASC, id ASC");
    }
    
    /**
     * Récupère les noms des fichiers de migration
     * @return array
     */
    protected function getMigrationFiles()
    {
        $files = glob($this->migrationsPath . '*.php');
        return array_map(function ($file) {
            return pathinfo($file, PATHINFO_FILENAME);
        }, $files);
    }
    
    /**
     * Récupère les noms des migrations déjà exécutées
     * @return array
     */
    protected function getExecutedMigrations()
    {
        $migrations = $this->db->query("SELECT migration FROM {$this->migrationsTable}");
        return array_column($migrations, 'migration');
    }
    
    /**
     * Récupère le prochain numéro de batch
     * @return int
     */
    protected function getNextBatchNumber()
    {
        $lastBatch = $this->db->query("SELECT MAX(batch) as batch FROM {$this->migrationsTable}");
        return isset($lastBatch[0]) ? ($lastBatch[0]['batch'] + 1) : 1;
    }
    
    /**
     * S'assure que la table des migrations existe
     * @return void
     */
    protected function ensureMigrationsTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->migrationsTable} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            batch INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $this->db->query($sql);
    }
    
    /**
     * S'assure que le répertoire des migrations existe
     * @return void
     */
    protected function ensureMigrationsDirectory()
    {
        if (!is_dir($this->migrationsPath)) {
            mkdir($this->migrationsPath, 0755, true);
        }
    }
    
    /**
     * Obtient le nom de la classe de migration à partir du nom du fichier
     * @param string $migrationName Nom de la migration
     * @return string
     */
    protected function getMigrationClass($migrationName)
    {
        $parts = explode('_', $migrationName);
        array_shift($parts); // Retirer le timestamp
        
        $className = '';
        foreach ($parts as $part) {
            $className .= ucfirst($part);
        }
        
        return $className . 'Migration';
    }
    
    /**
     * Crée une nouvelle migration
     * @param string $name Nom de la migration
     * @return string Le nom du fichier créé
     */
    public function create($name)
    {
        $timestamp = date('Y_m_d_His');
        $filename = $timestamp . '_' . strtolower($name);
        $filepath = $this->migrationsPath . $filename . '.php';
        
        $className = '';
        $parts = explode('_', $name);
        foreach ($parts as $part) {
            $className .= ucfirst($part);
        }
        
        $stub = <<<PHP
<?php

use App\Core\Migration;

class {$className}Migration extends Migration
{
    /**
     * Exécuter la migration
     * @return void
     */
    public function up()
    {
        \$this->createTable('table_name', function (\$table) {
            \$table->id();
            \$table->string('name');
            \$table->timestamps();
        });
    }
    
    /**
     * Annuler la migration
     * @return void
     */
    public function down()
    {
        \$this->dropTable('table_name');
    }
}
PHP;
        
        file_put_contents($filepath, $stub);
        
        echo "Migration créée: {$filepath}\n";
        
        return $filename;
    }
}
