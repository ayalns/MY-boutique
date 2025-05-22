<?php
session_start();

// Vérification que l'ID du produit est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: shop.php');
    exit;
}

$id = (int) $_GET['id'];

// Initialiser le panier si vide
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Ajouter ou incrémenter la quantité du produit
if (isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id]++;
} else {
    $_SESSION['cart'][$id] = 1;
}

// Rediriger vers le panier ou retour à la boutique
header("Location: cart.php");
exit;
