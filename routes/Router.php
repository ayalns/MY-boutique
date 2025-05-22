<?php
// Routeur principal de l'application

// Inclure la configuration
require_once __DIR__ . '/../config/config.php';

class Router {
    private $routes = [];
    
    // Ajouter une route
    public function add($method, $path, $controller, $action) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }
    
    // Dispatcher les requêtes
    public function dispatch() {
        // Récupérer la méthode HTTP et l'URL demandée
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];
        
        // Extraire le chemin de l'URL (sans les paramètres de requête)
        $path = parse_url($uri, PHP_URL_PATH);
        
        // Supprimer le préfixe du chemin de base si nécessaire
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        if ($basePath !== '/' && strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }
        
        // Assurer que le chemin commence par /
        if (empty($path)) {
            $path = '/';
        }
        
        // Rechercher une route correspondante
        foreach ($this->routes as $route) {
            // Convertir le chemin de la route en expression régulière
            $pattern = $this->convertRouteToRegex($route['path']);
            
            // Vérifier si la méthode et le chemin correspondent
            if ($route['method'] === $method && preg_match($pattern, $path, $matches)) {
                // Extraire les paramètres de l'URL
                array_shift($matches); // Supprimer la correspondance complète
                
                // Instancier le contrôleur
                $controllerName = $route['controller'];
                $controller = new $controllerName();
                
                // Appeler l'action avec les paramètres
                call_user_func_array([$controller, $route['action']], $matches);
                return;
            }
        }
        
        // Aucune route correspondante trouvée
        $this->notFound();
    }
    
    // Convertir un chemin de route en expression régulière
    private function convertRouteToRegex($route) {
        // Échapper les caractères spéciaux
        $route = preg_quote($route, '/');
        
        // Remplacer les paramètres {param} par des groupes de capture
        $route = preg_replace('/\\\{([a-zA-Z0-9_]+)\\\}/', '([^/]+)', $route);
        
        // Ajouter les délimiteurs et les ancres
        return '/^' . $route . '$/';
    }
    
    // Gérer les routes non trouvées
    private function notFound() {
        header("HTTP/1.0 404 Not Found");
        include __DIR__ . '/../views/errors/404.php';
        exit;
    }
}

// Créer une instance du routeur
$router = new Router();

// Définir les routes
$router->add('GET', '/', 'HomeController', 'index');
$router->add('GET', '/shop', 'ShopController', 'index');
$router->add('GET', '/product/{id}', 'ProductController', 'show');
$router->add('GET', '/cart', 'CartController', 'index');
$router->add('GET', '/login', 'AuthController', 'showLogin');
$router->add('POST', '/login', 'AuthController', 'login');
$router->add('GET', '/register', 'AuthController', 'showRegister');
$router->add('POST', '/register', 'AuthController', 'register');
$router->add('GET', '/logout', 'AuthController', 'logout');
$router->add('GET', '/profile', 'UserController', 'profile');
$router->add('POST', '/profile/update', 'UserController', 'updateProfile');
$router->add('POST', '/profile/password', 'UserController', 'changePassword');

// Routes AJAX
$router->add('POST', '/api/cart/add', 'CartController', 'addToCart');
$router->add('POST', '/api/cart/remove', 'CartController', 'removeFromCart');
$router->add('POST', '/api/cart/update', 'CartController', 'updateCart');
$router->add('POST', '/api/review/submit', 'ReviewController', 'submitReview');
$router->add('POST', '/api/review/edit', 'ReviewController', 'editReview');
$router->add('POST', '/api/review/delete', 'ReviewController', 'deleteReview');

// Routes admin
$router->add('GET', '/admin', 'AdminController', 'dashboard');
$router->add('GET', '/admin/products', 'AdminController', 'products');
$router->add('GET', '/admin/users', 'AdminController', 'users');
$router->add('GET', '/admin/events', 'AdminController', 'events');

// Dispatcher les requêtes
$router->dispatch();
