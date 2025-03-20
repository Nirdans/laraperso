<?php
namespace App\Models;

use App\Services\Database;

abstract class Model
{
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    
    protected $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function all()
    {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db->select($sql);
    }
    
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->selectOne($sql, [$id]);
    }
    
    public function findBy($column, $value)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ?";
        return $this->db->selectOne($sql, [$value]);
    }
    
    public function where($column, $value)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ?";
        return $this->db->select($sql, [$value]);
    }
    
    public function create($data)
    {
        $filteredData = array_intersect_key($data, array_flip($this->fillable));
        return $this->db->insert($this->table, $filteredData);
    }
    
    public function update($id, $data)
    {
        $filteredData = array_intersect_key($data, array_flip($this->fillable));
        return $this->db->update(
            $this->table, 
            $filteredData, 
            "{$this->primaryKey} = ?", 
            [$id]
        );
    }
    
    public function delete($id)
    {
        return $this->db->delete($this->table, "{$this->primaryKey} = ?", [$id]);
    }
    
    public function query($sql, $params = [])
    {
        return $this->db->select($sql, $params);
    }
}
