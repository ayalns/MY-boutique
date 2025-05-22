<?php
// Contrôleur de base
abstract class Controller {
    // Méthode pour rendre une vue
    protected function render($view, $data = []) {
        // Extraire les données pour les rendre disponibles dans la vue
        extract($data);
        
        // Inclure l'en-tête
        include __DIR__ . '/../views/partials/header.php';
        
        // Inclure la vue demandée
        include __DIR__ . '/../views/' . $view . '.php';
        
        // Inclure le pied de page
        include __DIR__ . '/../views/partials/footer.php';
    }
    
    // Méthode pour renvoyer une réponse JSON (pour les requêtes AJAX)
    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    // Méthode pour rediriger vers une autre page
    protected function redirect($url) {
        header("Location: " . $url);
        exit;
    }
    
    // Méthode pour vérifier si l'utilisateur est connecté
    protected function requireLogin() {
        if (!isLoggedIn()) {
            $this->redirect('/login');
        }
    }
    
    // Méthode pour vérifier si l'utilisateur est admin
    protected function requireAdmin() {
        if (!isAdmin()) {
            $this->redirect('/');
        }
    }
}
