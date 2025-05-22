<?php
// Modèle Product pour la gestion des produits
class ProductModel extends Model {
    protected $table = 'produits';
    
    // Récupérer les produits avec pagination
    public function getProducts($page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} ORDER BY id DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Compter le nombre total de produits
    public function countProducts() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM {$this->table}");
        return $stmt->fetchColumn();
    }
    
    // Récupérer les produits en vedette
    public function getFeaturedProducts($limit = 4) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} ORDER BY RAND() LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Rechercher des produits
    public function searchProducts($keyword) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE nom LIKE :keyword OR description LIKE :keyword");
        $stmt->execute(['keyword' => "%{$keyword}%"]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Récupérer les avis d'un produit
    public function getProductReviews($productId) {
        $stmt = $this->pdo->prepare("
            SELECT r.*, u.nom, u.prenom 
            FROM product_reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.product_id = :product_id
            ORDER BY r.date_creation DESC
        ");
        $stmt->execute(['product_id' => $productId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Calculer la note moyenne d'un produit
    public function getAverageRating($productId) {
        $stmt = $this->pdo->prepare("SELECT AVG(note) FROM product_reviews WHERE product_id = :product_id");
        $stmt->execute(['product_id' => $productId]);
        
        return round($stmt->fetchColumn(), 1);
    }
}
