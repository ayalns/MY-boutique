<?php
// Contrôleur pour l'authentification
class AuthController extends Controller {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
    }
    
    // Afficher le formulaire de connexion
    public function showLogin() {
        $this->render('auth/login');
    }
    
    // Traiter la connexion
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }
        
        $email = sanitize($_POST['email']);
        $password = $_POST['mot_de_passe'];
        
        $user = $this->userModel->authenticate($email, $password);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $this->redirect('/');
        } else {
            // Stocker le message d'erreur en session
            $_SESSION['error'] = "Identifiants incorrects.";
            $this->redirect('/login');
        }
    }
    
    // Afficher le formulaire d'inscription
    public function showRegister() {
        $this->render('auth/register');
    }
    
    // Traiter l'inscription
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/register');
        }
        
        $userData = [
            'nom' => sanitize($_POST['nom']),
            'prenom' => sanitize($_POST['prenom']),
            'adresse' => sanitize($_POST['adresse']),
            'email' => sanitize($_POST['email']),
            'mot_de_passe' => $_POST['mot_de_passe'],
            'carte_bancaire' => isset($_POST['carte']) ? sanitize($_POST['carte']) : null
        ];
        
        // Vérifier si l'email existe déjà
        if ($this->userModel->findByEmail($userData['email'])) {
            $_SESSION['error'] = "Cet email est déjà utilisé.";
            $this->redirect('/register');
        }
        
        // Créer l'utilisateur
        $userId = $this->userModel->register($userData);
        
        if ($userId) {
            $_SESSION['user_id'] = $userId;
            $_SESSION['role'] = 'client';
            $this->redirect('/');
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de l'inscription.";
            $this->redirect('/register');
        }
    }
    
    // Déconnexion
    public function logout() {
        session_destroy();
        $this->redirect('/login');
    }
}