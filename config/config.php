<?php
// Fichier de configuration principal
define('BASE_URL', '/');

// Connexion à la base de données
function getDbConnection() {
    $host = 'localhost';
    $dbname = 'my_boutique';
    $username = 'root';
    $password = '';
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données: " . $e->getMessage());
    }
}

// Fonction pour nettoyer les entrées utilisateur
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Vérifier si l'utilisateur est admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
