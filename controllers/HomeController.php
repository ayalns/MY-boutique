<?php
// Contrôleur pour la page d'accueil
class HomeController extends Controller {
    private $productModel;
    private $eventModel;
    
    public function __construct() {
        $this->productModel = new ProductModel();
        $this->eventModel = new EventModel();
    }
    
    // Afficher la page d'accueil
    public function index() {
        // Récupérer les produits en vedette
        $featuredProducts = $this->productModel->getProducts(1, 6);
        
        // Récupérer les événements à venir
        $upcomingEvents = $this->eventModel->getUpcomingEvents();
        
        $this->render('home/index', [
            'featuredProducts' => $featuredProducts,
            'upcomingEvents' => $upcomingEvents
        ]);
    }
}