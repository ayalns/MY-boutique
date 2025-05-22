<?php
// Modèle de base
abstract class Model {
    protected $pdo;
    protected $table;
    
    public function __construct() {
        $this->pdo = getDbConnection();
    }
    
    // Récupérer tous les enregistrements
    public function all() {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Récupérer un enregistrement par ID
    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Créer un nouvel enregistrement
    public function create($data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})");
        $stmt->execute($data);
        
        return $this->pdo->lastInsertId();
    }
    
    // Mettre à jour un enregistrement
    public function update($id, $data) {
        $setClause = '';
        foreach ($data as $key => $value) {
            $setClause .= "{$key} = :{$key}, ";
        }
        $setClause = rtrim($setClause, ', ');
        
        $data['id'] = $id;
        
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET {$setClause} WHERE id = :id");
        return $stmt->execute($data);
    }
    
    // Supprimer un enregistrement
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}