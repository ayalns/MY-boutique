<?php
session_start();

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?message=connexion_requise');
    exit();
}

// Vérification de la validité du produit à supprimer
if (isset($_GET['product_id'])) {
    $product_id = (int)$_GET['product_id'];

    // Suppression du produit s'il existe dans le panier
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
}

// Redirection vers la page du panier après suppression
header('Location: cart.php');
exit();
