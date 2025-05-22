<?php
// Modèle Event pour la gestion des événements
class EventModel extends Model {
    protected $table = 'events';
    
    // Récupérer tous les événements
    public function getAllEvents() {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table} ORDER BY date_evenement ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Récupérer les événements à venir
    public function getUpcomingEvents($limit = 3) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM {$this->table} 
            WHERE date_evenement >= CURDATE() 
            ORDER BY date_evenement ASC 
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Récupérer un événement spécifique
    public function getEvent($id) {
        return $this->find($id);
    }
    
    // Créer un nouvel événement
    public function createEvent($data) {
        // Ajouter la date de création
        $data['date_creation'] = date('Y-m-d H:i:s');
        
        // Créer l'événement et retourner l'ID
        $eventId = $this->create($data);
        return (int)$eventId;
    }
    
    // Mettre à jour un événement
    public function updateEvent($id, $data) {
        // Ajouter la date de modification
        $data['date_modification'] = date('Y-m-d H:i:s');
        
        return $this->update($id, $data);
    }
    
    // Supprimer un événement
    public function deleteEvent($id, $userId = null) {
        if ($userId) {
            // Si un ID utilisateur est fourni, vérifier qu'il est le créateur
            $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id AND user_id = :user_id");
            return $stmt->execute(['id' => $id, 'user_id' => $userId]);
        } else {
            // Sinon, supprimer sans vérification (admin)
            return $this->delete($id);
        }
    }
    
    // Récupérer les événements créés par un utilisateur
    public function getUserEvents($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY date_evenement DESC");
        $stmt->execute(['user_id' => $userId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}