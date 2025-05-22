<?php
// Contrôleur pour le panier
class CartController extends Controller {
    private $cartModel;
    private $productModel;
    
    public function __construct() {
        $this->cartModel = new CartModel();
        $this->productModel = new ProductModel();
    }
    
    // Afficher le panier
    public function index() {
        $cartDetails = $this->cartModel->getCartDetails();
        $this->render('cart/index', ['cart' => $cartDetails]);
    }
    
    // Ajouter un produit au panier (AJAX)
    public function addToCart() {
        // Vérifier si la requête est AJAX
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        
        if (!$productId) {
            if ($isAjax) {
                $this->json(['success' => false, 'message' => 'ID de produit invalide']);
            } else {
                $this->redirect('/shop');
            }
        }
        
        // Vérifier que le produit existe
        $product = $this->productModel->find($productId);
        if (!$product) {
            if ($isAjax) {
                $this->json(['success' => false, 'message' => 'Produit introuvable']);
            } else {
                $this->redirect('/shop');
            }
        }
        
        // Ajouter au panier
        $cartDetails = $this->cartModel->addToCart($productId, $quantity);
        
        if ($isAjax) {
            $this->json([
                'success' => true,
                'message' => 'Produit ajouté au panier',
                'totalItems' => $cartDetails['totalItems'],
                'totalPrice' => $cartDetails['totalPrice']
            ]);
        } else {
            $this->redirect('/cart');
        }
    }
    
    // Supprimer un produit du panier (AJAX)
    public function removeFromCart() {
        // Vérifier si la requête est AJAX
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        
        if (!$productId) {
            if ($isAjax) {
                $this->json(['success' => false, 'message' => 'ID de produit invalide']);
            } else {
                $this->redirect('/cart');
            }
        }
        
        // Supprimer du panier
        $cartDetails = $this->cartModel->removeFromCart($productId);
        
        if ($isAjax) {
            $this->json([
                'success' => true,
                'message' => 'Produit supprimé du panier',
                'totalItems' => $cartDetails['totalItems'],
                'totalPrice' => $cartDetails['totalPrice'],
                'isEmpty' => $cartDetails['isEmpty']
            ]);
        } else {
            $this->redirect('/cart');
        }
    }
    
    // Mettre à jour la quantité d'un produit (AJAX)
    public function updateCart() {
        // Vérifier si la requête est AJAX
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
        
        if (!$productId) {
            if ($isAjax) {
                $this->json(['success' => false, 'message' => 'ID de produit invalide']);
            } else {
                $this->redirect('/cart');
            }
        }
        
        // Mettre à jour le panier
        $cartDetails = $this->cartModel->updateCartItem($productId, $quantity);
        
        if ($isAjax) {
            $this->json([
                'success' => true,
                'message' => 'Panier mis à jour',
                'totalItems' => $cartDetails['totalItems'],
                'totalPrice' => $cartDetails['totalPrice'],
                'isEmpty' => $cartDetails['isEmpty']
            ]);
        } else {
            $this->redirect('/cart');
        }
    }
}
