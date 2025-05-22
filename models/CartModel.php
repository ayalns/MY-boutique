<?php
// Modèle pour le panier
class CartModel extends Model {
    protected $table = 'cart_items';
    
    // Ajouter un produit au panier
    public function addToCart($userId, $productId, $quantity = 1) {
        // Vérifier si le produit existe déjà dans le panier
        $stmt = $this->pdo->prepare("
            SELECT * FROM {$this->table} 
            WHERE user_id = :user_id AND product_id = :product_id
        ");
        $stmt->execute([
            'user_id' => $userId,
            'product_id' => $productId
        ]);
        $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($cartItem) {
            // Mettre à jour la quantité
            $newQuantity = $cartItem['quantity'] + $quantity;
            return $this->update($cartItem['id'], ['quantity' => $newQuantity]);
        } else {
            // Ajouter un nouvel élément au panier
            return $this->create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'date_creation' => date('Y-m-d H:i:s')
            ]);
        }
    }
    
    // Mettre à jour la quantité d'un élément du panier
    public function updateCartItem($cartItemId, $quantity) {
        return $this->update($cartItemId, ['quantity' => $quantity]);
    }
    
    // Supprimer un élément du panier
    public function removeFromCart($cartItemId) {
        return $this->delete($cartItemId);
    }
    
    // Vider le panier d'un utilisateur
    public function clearCart($userId) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE user_id = :user_id");
        return $stmt->execute(['user_id' => $userId]);
    }
    
    // Récupérer le contenu du panier d'un utilisateur
    public function getCartItems($userId) {
        $stmt = $this->pdo->prepare("
            SELECT ci.*, p.nom, p.prix, p.image
            FROM {$this->table} ci
            JOIN produits p ON ci.product_id = p.id
            WHERE ci.user_id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Calculer le total du panier
    public function getCartTotal($userId) {
        $stmt = $this->pdo->prepare("
            SELECT SUM(ci.quantity * p.prix) as total
            FROM {$this->table} ci
            JOIN produits p ON ci.product_id = p.id
            WHERE ci.user_id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);
        
        return $stmt->fetchColumn() ?: 0;
    }
    
    // Récupérer les détails du panier (nombre d'articles et total)
    public function getCartDetails($userId = null) {
        // Si aucun ID utilisateur n'est fourni, utiliser la session
        if (!$userId && isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
        }
        
        // Si toujours pas d'ID utilisateur, utiliser le panier de session
        if (!$userId && isset($_SESSION['cart'])) {
            $totalItems = 0;
            $totalPrice = 0;
            
            foreach ($_SESSION['cart'] as $productId => $quantity) {
                $totalItems += $quantity;
                
                // Récupérer le prix du produit
                $stmt = $this->pdo->prepare("SELECT prix FROM produits WHERE id = :id");
                $stmt->execute(['id' => $productId]);
                $price = $stmt->fetchColumn();
                
                $totalPrice += $price * $quantity;
            }
            
            return [
                'totalItems' => $totalItems,
                'totalPrice' => $totalPrice
            ];
        }
        
        // Utilisateur connecté avec ID
        if ($userId) {
            $stmt = $this->pdo->prepare("
                SELECT SUM(ci.quantity) as total_items, SUM(ci.quantity * p.prix) as total_price
                FROM {$this->table} ci
                JOIN produits p ON ci.product_id = p.id
                WHERE ci.user_id = :user_id
            ");
            $stmt->execute(['user_id' => $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'totalItems' => (int)$result['total_items'] ?: 0,
                'totalPrice' => (float)$result['total_price'] ?: 0
            ];
        }
        
        // Aucun panier trouvé
        return [
            'totalItems' => 0,
            'totalPrice' => 0
        ];
    }
}
