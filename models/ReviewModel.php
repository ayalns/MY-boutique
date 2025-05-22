<?php
// Modèle pour les avis et commentaires
class ReviewModel extends Model {
    protected $table = 'product_reviews';
    
    // Récupérer les avis d'un produit
    public function getProductReviews($productId) {
        $stmt = $this->pdo->prepare("
            SELECT r.*, u.nom, u.prenom 
            FROM {$this->table} r
            JOIN users u ON r.user_id = u.id
            WHERE r.product_id = :product_id
            ORDER BY r.date_creation DESC
        ");
        $stmt->execute(['product_id' => $productId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Récupérer les avis d'un utilisateur
    public function getUserReviews($userId) {
        $stmt = $this->pdo->prepare("
            SELECT r.*, p.nom as product_name
            FROM {$this->table} r
            JOIN produits p ON r.product_id = p.id
            WHERE r.user_id = :user_id
            ORDER BY r.date_creation DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Vérifier si un utilisateur a déjà donné son avis sur un produit
    public function hasUserReviewed($userId, $productId) {
        $stmt = $this->pdo->prepare("
            SELECT id FROM {$this->table} 
            WHERE user_id = :user_id AND product_id = :product_id
        ");
        $stmt->execute([
            'user_id' => $userId,
            'product_id' => $productId
        ]);
        
        return $stmt->fetch() ? true : false;
    }
    
    // Ajouter un avis
    public function addReview($userId, $productId, $commentaire, $note) {
        // Vérifier si l'utilisateur a déjà donné son avis
        if ($this->hasUserReviewed($userId, $productId)) {
            return false;
        }
        
        $data = [
            'user_id' => $userId,
            'product_id' => $productId,
            'commentaire' => $commentaire,
            'note' => $note,
            'date_creation' => date('Y-m-d H:i:s')
        ];
        
        return $this->create($data);
    }
    
    // Récupérer un avis spécifique
    public function getReview($reviewId) {
        $stmt = $this->pdo->prepare("
            SELECT r.*, u.nom, u.prenom, p.nom as product_name
            FROM {$this->table} r
            JOIN users u ON r.user_id = u.id
            JOIN produits p ON r.product_id = p.id
            WHERE r.id = :id
        ");
        $stmt->execute(['id' => $reviewId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Mettre à jour un avis
    public function updateReview($reviewId, $userId, $commentaire, $note) {
        // Vérifier que l'avis appartient à l'utilisateur
        $stmt = $this->pdo->prepare("SELECT id FROM {$this->table} WHERE id = :id AND user_id = :user_id");
        $stmt->execute([
            'id' => $reviewId,
            'user_id' => $userId
        ]);
        
        if (!$stmt->fetch()) {
            return false;
        }
        
        $data = [
            'commentaire' => $commentaire,
            'note' => $note,
            'date_modification' => date('Y-m-d H:i:s')
        ];
        
        return $this->update($reviewId, $data);
    }
    
    // Supprimer un avis
    public function deleteUserReview($reviewId, $userId) {
        // Vérifier que l'avis appartient à l'utilisateur
        $stmt = $this->pdo->prepare("SELECT id FROM {$this->table} WHERE id = :id AND user_id = :user_id");
        $stmt->execute([
            'id' => $reviewId,
            'user_id' => $userId
        ]);
        
        if (!$stmt->fetch()) {
            return false;
        }
        
        return $this->delete($reviewId);
    }
    
    // Calculer la note moyenne d'un produit
    public function getAverageRating($productId) {
        $stmt = $this->pdo->prepare("SELECT AVG(note) FROM {$this->table} WHERE product_id = :product_id");
        $stmt->execute(['product_id' => $productId]);
        
        return round($stmt->fetchColumn(), 1);
    }
}
