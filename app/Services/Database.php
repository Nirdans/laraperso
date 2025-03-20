<?php
namespace App\Services;

class Database
{
    /**
     * Instance unique de la classe (pattern Singleton)
     * @var Database|null
     */
    private static $instance = null;
    
    /**
     * Instance PDO
     * @var \PDO
     */
    private $pdo;
    
    /**
     * Historique des requêtes exécutées (pour le débogage)
     * @var array
     */
    private $queries = [];
    
    /**
     * Constructeur privé (pattern Singleton)
     */
    private function __construct()
    {
        global $db;
        
        try {
            $dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['name']};charset={$db['charset']}";
            $options = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->pdo = new \PDO($dsn, $db['user'], $db['password'], $options);
        } catch (\PDOException $e) {
            die("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }
    
    /**
     * Obtient l'instance unique de la classe
     * @return Database
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Exécute une requête SQL
     * @param string $sql Requête SQL
     * @param array $params Paramètres
     * @return array|int Résultats de la requête ou nombre de lignes affectées
     */
    public function query($sql, $params = [])
    {
        $this->logQuery($sql, $params);
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        // Si la requête est un SELECT, retourner les résultats
        if (strpos(strtoupper($sql), 'SELECT') === 0) {
            return $stmt->fetchAll();
        }
        
        // Sinon, retourner le nombre de lignes affectées
        return $stmt->rowCount();
    }
    
    /**
     * Insère des données dans une table
     * @param string $table Nom de la table
     * @param array $data Données à insérer
     * @return int|bool ID de la dernière ligne insérée ou false en cas d'échec
     */
    public function insert($table, $data)
    {
        $columns = array_keys($data);
        $values = array_values($data);
        $placeholders = array_fill(0, count($columns), '?');
        
        $sql = "INSERT INTO {$table} (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $this->logQuery($sql, $values);
        
        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute($values);
        
        return $success ? $this->pdo->lastInsertId() : false;
    }
    
    /**
     * Met à jour des données dans une table
     * @param string $table Nom de la table
     * @param array $data Données à mettre à jour
     * @param array $where Conditions
     * @return int Nombre de lignes affectées
     */
    public function update($table, $data, $where)
    {
        $set = [];
        $values = [];
        
        foreach ($data as $column => $value) {
            $set[] = "{$column} = ?";
            $values[] = $value;
        }
        
        $whereClauses = [];
        foreach ($where as $column => $value) {
            $whereClauses[] = "{$column} = ?";
            $values[] = $value;
        }
        
        $sql = "UPDATE {$table} SET " . implode(', ', $set) . 
               " WHERE " . implode(' AND ', $whereClauses);
        
        $this->logQuery($sql, $values);
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);
        
        return $stmt->rowCount();
    }
    
    /**
     * Supprime des données d'une table
     * @param string $table Nom de la table
     * @param array $where Conditions
     * @return int Nombre de lignes affectées
     */
    public function delete($table, $where)
    {
        $whereClauses = [];
        $values = [];
        
        foreach ($where as $column => $value) {
            $whereClauses[] = "{$column} = ?";
            $values[] = $value;
        }
        
        $sql = "DELETE FROM {$table} WHERE " . implode(' AND ', $whereClauses);
        
        $this->logQuery($sql, $values);
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);
        
        return $stmt->rowCount();
    }
    
    /**
     * Commence une transaction
     * @return bool
     */
    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }
    
    /**
     * Valide une transaction
     * @return bool
     */
    public function commit()
    {
        return $this->pdo->commit();
    }
    
    /**
     * Annule une transaction
     * @return bool
     */
    public function rollBack()
    {
        return $this->pdo->rollBack();
    }
    
    /**
     * Récupère toutes les lignes d'une table
     * @param string $table Nom de la table
     * @param string $orderBy Champ de tri
     * @param string $order Direction du tri (ASC, DESC)
     * @param int $limit Limite
     * @param int $offset Offset
     * @return array
     */
    public function getAll($table, $orderBy = null, $order = 'ASC', $limit = null, $offset = null)
    {
        $sql = "SELECT * FROM {$table}";
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy} {$order}";
        }
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
            
            if ($offset) {
                $sql .= " OFFSET {$offset}";
            }
        }
        
        $this->logQuery($sql);
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère une ligne d'une table par son ID
     * @param string $table Nom de la table
     * @param int $id ID
     * @param string $idColumn Nom de la colonne ID
     * @return array|false
     */
    public function getById($table, $id, $idColumn = 'id')
    {
        $sql = "SELECT * FROM {$table} WHERE {$idColumn} = ?";
        
        $this->logQuery($sql, [$id]);
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        
        return $stmt->fetch();
    }
    
    /**
     * Récupère des lignes d'une table par une colonne
     * @param string $table Nom de la table
     * @param string $column Nom de la colonne
     * @param mixed $value Valeur
     * @return array
     */
    public function getBy($table, $column, $value)
    {
        $sql = "SELECT * FROM {$table} WHERE {$column} = ?";
        
        $this->logQuery($sql, [$value]);
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$value]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère une ligne d'une table par une colonne
     * @param string $table Nom de la table
     * @param string $column Nom de la colonne
     * @param mixed $value Valeur
     * @return array|false
     */
    public function getFirstBy($table, $column, $value)
    {
        $sql = "SELECT * FROM {$table} WHERE {$column} = ? LIMIT 1";
        
        $this->logQuery($sql, [$value]);
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$value]);
        
        return $stmt->fetch();
    }
    
    /**
     * Compte le nombre de lignes d'une table
     * @param string $table Nom de la table
     * @param array $where Conditions
     * @return int
     */
    public function count($table, $where = [])
    {
        $sql = "SELECT COUNT(*) AS count FROM {$table}";
        $params = [];
        
        if (!empty($where)) {
            $whereClauses = [];
            foreach ($where as $column => $value) {
                $whereClauses[] = "{$column} = ?";
                $params[] = $value;
            }
            
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }
        
        $this->logQuery($sql, $params);
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return (int) $stmt->fetch()['count'];
    }
    
    /**
     * Vérifie si une table existe
     * @param string $table Nom de la table
     * @return bool
     */
    public function tableExists($table)
    {
        global $db;
        
        $sql = "SELECT COUNT(*) AS count FROM information_schema.tables 
                WHERE table_schema = ? AND table_name = ?";
        
        $this->logQuery($sql, [$db['name'], $table]);
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$db['name'], $table]);
        
        return (int) $stmt->fetch()['count'] > 0;
    }
    
    /**
     * Récupère l'objet PDO
     * @return \PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }
    
    /**
     * Enregistre une requête dans l'historique
     * @param string $sql Requête SQL
     * @param array $params Paramètres
     * @return void
     */
    private function logQuery($sql, $params = [])
    {
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
            $this->queries[] = [
                'sql' => $sql,
                'params' => $params,
                'time' => microtime(true)
            ];
        }
    }
    
    /**
     * Récupère l'historique des requêtes
     * @return array
     */
    public function getQueries()
    {
        return $this->queries;
    }
}
