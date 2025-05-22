<?php
session_start();

// Vérification de connexion
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?message=connexion_requise');
    exit();
}

// Vérification des données envoyées
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération sécurisée des données
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

    if ($product_id > 0) {
        if ($quantity > 0) {
            // Mise à jour de la quantité
            $_SESSION['cart'][$product_id] = $quantity;
        } else {
            // Si quantité <= 0, suppression du produit du panier
            unset($_SESSION['cart'][$product_id]);
        }
    }
}

// Retour à la page du panier après la mise à jour
header('Location: cart.php');
exit();
