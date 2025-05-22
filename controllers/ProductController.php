<?php
// Contrôleur pour les produits
class ProductController extends Controller {
    private $productModel;
    
    public function __construct() {
        $this->productModel = new ProductModel();
    }
    
    // Afficher la liste des produits
    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 12; // Nombre de produits par page
        
        $products = $this->productModel->getProducts($page, $limit);
        $totalProducts = $this->productModel->countProducts();
        $totalPages = ceil($totalProducts / $limit);
        
        $this->render('product/index', [
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }
    
    // Afficher un produit spécifique
    public function show($id) {
        $product = $this->productModel->find($id);
        
        if (!$product) {
            $this->redirect('/shop');
        }
        
        $reviews = $this->productModel->getProductReviews($id);
        $averageRating = $this->productModel->getAverageRating($id);
        
        $this->render('product/show', [
            'product' => $product,
            'reviews' => $reviews,
            'averageRating' => $averageRating
        ]);
    }
    
    // Rechercher des produits
    public function search() {
        $keyword = isset($_GET['q']) ? sanitize($_GET['q']) : '';
        
        if (empty($keyword)) {
            $this->redirect('/shop');
        }
        
        $products = $this->productModel->searchProducts($keyword);
        
        $this->render('product/search', [
            'products' => $products,
            'keyword' => $keyword
        ]);
    }
}
