<?php
// Modèle pour les commandes et achats
class OrderModel extends Model {
    protected $table = 'commandes';
    
    // Récupérer les commandes d'un utilisateur
    public function getUserOrders($userId) {
        $stmt = $this->pdo->prepare("
            SELECT c.*, COUNT(cd.id) as total_items, SUM(p.prix * cd.quantite) as total_price
            FROM {$this->table} c
            JOIN commande_details cd ON c.id = cd.commande_id
            JOIN produits p ON cd.produit_id = p.id
            WHERE c.user_id = :user_id
            GROUP BY c.id
            ORDER BY c.date_creation DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Récupérer les détails d'une commande
    public function getOrderDetails($orderId) {
        $stmt = $this->pdo->prepare("
            SELECT cd.*, p.nom, p.prix, p.image
            FROM commande_details cd
            JOIN produits p ON cd.produit_id = p.id
            WHERE cd.commande_id = :order_id
        ");
        $stmt->execute(['order_id' => $orderId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Créer une nouvelle commande
    public function createOrder($userId, $cartItems, $shippingAddress, $paymentMethod) {
        // Commencer une transaction
        $this->pdo->beginTransaction();
        
        try {
            // Insérer la commande principale
            $stmt = $this->pdo->prepare("
                INSERT INTO {$this->table} (user_id, adresse_livraison, methode_paiement, statut, date_creation)
                VALUES (:user_id, :adresse_livraison, :methode_paiement, 'en_attente', NOW())
            ");
            $stmt->execute([
                'user_id' => $userId,
                'adresse_livraison' => $shippingAddress,
                'methode_paiement' => $paymentMethod
            ]);
            
            $orderId = $this->pdo->lastInsertId();
            
            // Insérer les détails de la commande
            foreach ($cartItems as $item) {
                $stmt = $this->pdo->prepare("
                    INSERT INTO commande_details (commande_id, produit_id, quantite, prix_unitaire)
                    VALUES (:commande_id, :produit_id, :quantite, :prix_unitaire)
                ");
                $stmt->execute([
                    'commande_id' => $orderId,
                    'produit_id' => $item['product']['id'],
                    'quantite' => $item['quantity'],
                    'prix_unitaire' => $item['product']['prix']
                ]);
            }
            
            // Valider la transaction
            $this->pdo->commit();
            
            return $orderId;
        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            $this->pdo->rollBack();
            return false;
        }
    }
    
    // Mettre à jour le statut d'une commande
    public function updateOrderStatus($orderId, $status) {
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET statut = :statut WHERE id = :id");
        return $stmt->execute([
            'id' => $orderId,
            'statut' => $status
        ]);
    }
}