<?php
namespace App\Models;

use App\Services\Database;

abstract class Model
{
    /**
     * Instance de la base de données
     * @var \App\Services\Database
     */
    protected $db;
    
    /**
     * Nom de la table
     * @var string
     */
    protected $table;
    
    /**
     * Nom de la clé primaire
     * @var string
     */
    protected $primaryKey = 'id';
    
    /**
     * Indique si le modèle utilise des timestamps
     * @var bool
     */
    protected $timestamps = true;
    
    /**
     * Liste des champs remplissables
     * @var array
     */
    protected $fillable = [];
    
    /**
     * Liste des champs protégés
     * @var array
     */
    protected $guarded = ['id'];
    
    /**
     * Liste des casts
     * @var array
     */
    protected $casts = [];
    
    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
        
        if (!$this->table) {
            // Déduire le nom de la table à partir du nom de la classe
            $className = get_class($this);
            $parts = explode('\\', $className);
            $className = end($parts);
            $this->table = strtolower($className) . 's';
        }
    }
    
    /**
     * Trouve un enregistrement par ID
     * @param int $id ID
     * @return array|bool
     */
    public function find($id)
    {
        return $this->db->getById($this->table, $id, $this->primaryKey);
    }
    
    /**
     * Trouve un enregistrement par une colonne
     * @param string $column Nom de la colonne
     * @param mixed $value Valeur
     * @return array|bool
     */
    public function findBy($column, $value)
    {
        return $this->db->getFirstBy($this->table, $column, $value);
    }
    
    /**
     * Trouve tous les enregistrements correspondant à un critère
     * @param string $column Nom de la colonne
     * @param mixed $value Valeur
     * @return array
     */
    public function where($column, $value)
    {
        return $this->db->getBy($this->table, $column, $value);
    }
    
    /**
     * Récupère tous les enregistrements
     * @param string $orderBy Champ de tri
     * @param string $order Direction du tri (ASC, DESC)
     * @return array
     */
    public function all($orderBy = null, $order = 'ASC')
    {
        return $this->db->getAll($this->table, $orderBy, $order);
    }
    
    /**
     * Crée un enregistrement
     * @param array $data Données
     * @return int|bool ID de l'enregistrement créé ou false
     */
    public function create($data)
    {
        // Filtrer les champs en fonction des fillable et guarded
        $data = $this->filterFields($data);
        
        // Ajouter les timestamps
        if ($this->timestamps) {
            $now = date('Y-m-d H:i:s');
            $data['created_at'] = $now;
            $data['updated_at'] = $now;
        }
        
        // Cast des valeurs
        $data = $this->castValues($data);
        
        return $this->db->insert($this->table, $data);
    }
    
    /**
     * Met à jour un enregistrement
     * @param int $id ID
     * @param array $data Données
     * @return int Nombre de lignes affectées
     */
    public function update($id, $data)
    {
        // Filtrer les champs en fonction des fillable et guarded
        $data = $this->filterFields($data);
        
        // Ajouter le timestamp de mise à jour
        if ($this->timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        // Cast des valeurs
        $data = $this->castValues($data);
        
        return $this->db->update($this->table, $data, [$this->primaryKey => $id]);
    }
    
    /**
     * Supprime un enregistrement
     * @param int $id ID
     * @return int Nombre de lignes affectées
     */
    public function delete($id)
    {
        return $this->db->delete($this->table, [$this->primaryKey => $id]);
    }
    
    /**
     * Filtre les champs en fonction des fillable et guarded
     * @param array $data Données
     * @return array
     */
    protected function filterFields($data)
    {
        if (!empty($this->fillable)) {
            return array_intersect_key($data, array_flip($this->fillable));
        }
        
        if (!empty($this->guarded)) {
            return array_diff_key($data, array_flip($this->guarded));
        }
        
        return $data;
    }
    
    /**
     * Cast les valeurs en fonction des types définis
     * @param array $data Données
     * @return array
     */
    protected function castValues($data)
    {
        foreach ($this->casts as $field => $type) {
            if (isset($data[$field])) {
                switch ($type) {
                    case 'int':
                    case 'integer':
                        $data[$field] = (int) $data[$field];
                        break;
                    case 'bool':
                    case 'boolean':
                        $data[$field] = (bool) $data[$field];
                        break;
                    case 'float':
                    case 'double':
                        $data[$field] = (float) $data[$field];
                        break;
                    case 'string':
                        $data[$field] = (string) $data[$field];
                        break;
                    case 'array':
                        if (is_string($data[$field])) {
                            $data[$field] = json_decode($data[$field], true);
                        }
                        break;
                    case 'json':
                        if (is_array($data[$field])) {
                            $data[$field] = json_encode($data[$field]);
                        }
                        break;
                }
            }
        }
        
        return $data;
    }
    
    /**
     * Ajoute des clauses WHERE à une requête SQL de base
     * @param string $sql Requête SQL de base
     * @param array $where Conditions WHERE
     * @param array $params Paramètres
     * @return array Requête SQL et paramètres
     */
    protected function addWhereToSql($sql, $where, $params = [])
    {
        if (!empty($where)) {
            $whereClauses = [];
            
            foreach ($where as $column => $value) {
                if (is_array($value)) {
                    // Si la valeur est un tableau (opérateur et valeur)
                    $operator = $value[0];
                    $val = $value[1];
                    $whereClauses[] = "{$column} {$operator} ?";
                    $params[] = $val;
                } else {
                    // Sinon, on utilise l'égalité
                    $whereClauses[] = "{$column} = ?";
                    $params[] = $value;
                }
            }
            
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }
        
        return [$sql, $params];
    }
}
