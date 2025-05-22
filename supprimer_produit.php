<?php
session_start();
require 'config.php';

// Vérifie que l'utilisateur est IT/Commercial
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'it') {
    echo "Accès refusé.";
    exit;
}

// Vérifie l'ID du produit
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID invalide.";
    exit;
}

$product_id = (int) $_GET['id'];

// Vérifie que le produit existe
$stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    echo "Produit introuvable.";
    exit;
}

// Supprime le produit
$delete = $pdo->prepare("DELETE FROM products WHERE id = ?");
$delete->execute([$product_id]);

// Supprimer l'image du dossier (optionnel)
if ($product['image'] && file_exists("assets/" . $product['image'])) {
    unlink("assets/" . $product['image']);
}

// Redirige avec message
header("Location: produits_it.php?supp=ok");
exit;
