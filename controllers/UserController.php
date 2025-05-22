<?php
// Contrôleur pour les utilisateurs
class UserController extends Controller {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
    }
    
    // Afficher le profil de l'utilisateur
    public function profile() {
        // Vérifier que l'utilisateur est connecté
        $this->requireLogin();
        
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->find($userId);
        
        if (!$user) {
            $this->redirect('/login');
        }
        
        $this->render('user/profile', ['user' => $user]);
    }
    
    // Afficher le formulaire de modification du profil
    public function showEditProfile() {
        // Vérifier que l'utilisateur est connecté
        $this->requireLogin();
        
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->find($userId);
        
        if (!$user) {
            $this->redirect('/login');
        }
        
        $this->render('user/edit', ['user' => $user]);
    }
    
    // Traiter la modification du profil
    public function updateProfile() {
        // Vérifier que l'utilisateur est connecté
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/profile');
        }
        
        $userId = $_SESSION['user_id'];
        
        $userData = [
            'nom' => sanitize($_POST['nom']),
            'prenom' => sanitize($_POST['prenom']),
            'adresse' => sanitize($_POST['adresse']),
            'email' => sanitize($_POST['email']),
            'carte_bancaire' => isset($_POST['carte']) ? sanitize($_POST['carte']) : null
        ];
        
        // Vérifier si l'email existe déjà pour un autre utilisateur
        $existingUser = $this->userModel->findByEmail($userData['email']);
        if ($existingUser && $existingUser['id'] != $userId) {
            $_SESSION['error'] = "Cet email est déjà utilisé par un autre compte.";
            $this->redirect('/profile/edit');
        }
        
        // Mettre à jour le profil
        $success = $this->userModel->update($userId, $userData);
        
        if ($success) {
            $_SESSION['success'] = "Votre profil a été mis à jour avec succès.";
            $this->redirect('/profile');
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la mise à jour de votre profil.";
            $this->redirect('/profile/edit');
        }
    }
    
    // Afficher le formulaire de changement de mot de passe
    public function showChangePassword() {
        // Vérifier que l'utilisateur est connecté
        $this->requireLogin();
        
        $this->render('user/password');
    }
    
    // Traiter le changement de mot de passe
    public function changePassword() {
        // Vérifier que l'utilisateur est connecté
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/profile');
        }
        
        $userId = $_SESSION['user_id'];
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        
        // Vérifier que le mot de passe actuel est correct
        $user = $this->userModel->find($userId);
        if (!password_verify($currentPassword, $user['mot_de_passe'])) {
            $_SESSION['error'] = "Le mot de passe actuel est incorrect.";
            $this->redirect('/profile/password');
        }
        
        // Vérifier que les nouveaux mots de passe correspondent
        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = "Les nouveaux mots de passe ne correspondent pas.";
            $this->redirect('/profile/password');
        }
        
        // Mettre à jour le mot de passe
        $success = $this->userModel->updatePassword($userId, $newPassword);
        
        if ($success) {
            $_SESSION['success'] = "Votre mot de passe a été mis à jour avec succès.";
            $this->redirect('/profile');
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la mise à jour de votre mot de passe.";
            $this->redirect('/profile/password');
        }
    }
    
    // Afficher le formulaire de suppression de compte
    public function showDeleteAccount() {
        // Vérifier que l'utilisateur est connecté
        $this->requireLogin();
        
        $this->render('user/delete');
    }
    
    // Traiter la suppression de compte
    public function deleteAccount() {
        // Vérifier que l'utilisateur est connecté
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/profile');
        }
        
        $userId = $_SESSION['user_id'];
        $password = $_POST['password'];
        
        // Vérifier que le mot de passe est correct
        $user = $this->userModel->find($userId);
        if (!password_verify($password, $user['mot_de_passe'])) {
            $_SESSION['error'] = "Le mot de passe est incorrect.";
            $this->redirect('/profile/delete');
        }
        
        // Supprimer le compte
        $success = $this->userModel->delete($userId);
        
        if ($success) {
            // Déconnecter l'utilisateur
            session_destroy();
            session_start();
            $_SESSION['success'] = "Votre compte a été supprimé avec succès.";
            $this->redirect('/');
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la suppression de votre compte.";
            $this->redirect('/profile');
        }
    }
    
    // Afficher l'historique des achats
    public function orders() {
        // Vérifier que l'utilisateur est connecté
        $this->requireLogin();
        
        $userId = $_SESSION['user_id'];
        
        // Charger le modèle des commandes
        $orderModel = new OrderModel();
        $orders = $orderModel->getUserOrders($userId);
        
        $this->render('user/orders', ['orders' => $orders]);
    }
}
