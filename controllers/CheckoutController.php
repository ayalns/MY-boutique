<?php
// Contrôleur pour le paiement et la finalisation des commandes
class CheckoutController extends Controller {
    private $cartModel;
    private $orderModel;
    private $userModel;
    
    public function __construct() {
        $this->cartModel = new CartModel();
        $this->orderModel = new OrderModel();
        $this->userModel = new UserModel();
    }
    
    // Afficher la page de paiement
    public function index() {
        // Vérifier que l'utilisateur est connecté
        $this->requireLogin();
        
        // Récupérer le contenu du panier
        $cartDetails = $this->cartModel->getCartDetails();
        
        // Vérifier que le panier n'est pas vide
        if ($cartDetails['isEmpty']) {
            $_SESSION['error'] = "Votre panier est vide.";
            $this->redirect('/cart');
        }
        
        // Récupérer les informations de l'utilisateur
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->find($userId);
        
        $this->render('checkout/index', [
            'cart' => $cartDetails,
            'user' => $user
        ]);
    }
    
    // Traiter la commande
    public function processOrder() {
        // Vérifier que l'utilisateur est connecté
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/checkout');
        }
        
        $userId = $_SESSION['user_id'];
        
        // Récupérer le contenu du panier
        $cartDetails = $this->cartModel->getCartDetails();
        
        // Vérifier que le panier n'est pas vide
        if ($cartDetails['isEmpty']) {
            $_SESSION['error'] = "Votre panier est vide.";
            $this->redirect('/cart');
        }
        
        // Récupérer les informations de livraison
        $shippingAddress = sanitize($_POST['adresse_livraison']);
        $paymentMethod = sanitize($_POST['methode_paiement']);
        
        // Créer la commande
        $orderId = $this->orderModel->createOrder(
            $userId,
            $cartDetails['items'],
            $shippingAddress,
            $paymentMethod
        );
        
        if ($orderId) {
            // Vider le panier
            $this->cartModel->clearCart();
            
            // Rediriger vers la page de confirmation
            $this->redirect('/checkout/confirmation/' . $orderId);
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la création de votre commande.";
            $this->redirect('/checkout');
        }
    }
    
    // Afficher la page de confirmation
    public function confirmation($orderId) {
        // Vérifier que l'utilisateur est connecté
        $this->requireLogin();
        
        $userId = $_SESSION['user_id'];
        
        // Récupérer les détails de la commande
        $orderDetails = $this->orderModel->getOrderDetails($orderId);
        
        // Vérifier que la commande existe et appartient à l'utilisateur
        if (empty($orderDetails)) {
            $this->redirect('/profile/orders');
        }
        
        $this->render('checkout/confirmation', [
            'orderId' => $orderId,
            'orderDetails' => $orderDetails
        ]);
    }
    
    // Afficher la page de succès de commande
    public function success() {
        // Vérifier que l'utilisateur est connecté
        $this->requireLogin();
        
        $this->render('checkout/success');
    }
}
