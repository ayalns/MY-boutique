<?php
// Modèle User pour la gestion des utilisateurs
class UserModel extends Model {
    protected $table = 'users';
    
    // Trouver un utilisateur par email
    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Vérifier les identifiants de connexion
    public function authenticate($email, $password) {
        $user = $this->findByEmail($email);
        
        if ($user && password_verify($password, $user['mot_de_passe'])) {
            return $user;
        }
        
        return false;
    }
    
    // Créer un nouvel utilisateur
    public function register($userData) {
        // Hasher le mot de passe
        $userData['mot_de_passe'] = password_hash($userData['mot_de_passe'], PASSWORD_DEFAULT);
        
        // Définir le rôle par défaut
        if (!isset($userData['role'])) {
            $userData['role'] = 'client';
        }
        
        return $this->create($userData);
    }
    
    // Mettre à jour le mot de passe
    public function updatePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->update($userId, ['mot_de_passe' => $hashedPassword]);
    }
}
