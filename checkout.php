<?php
session_start();
require __DIR__ . '/config.php';

// Rediriger si le panier est vide ou si l'utilisateur n'est pas connecté
if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'];
$total = 0;
$order_items = [];

// Calculer total et vérifier les produits
foreach ($cart as $pid => $qty) {
    $stmt = $pdo->prepare("SELECT id, name, price FROM products WHERE id = ?");
    $stmt->execute([$pid]);
    $product = $stmt->fetch();
    if ($product) {
        $subtotal = $product['price'] * $qty;
        $total += $subtotal;
        $order_items[] = [
            'product_id' => $product['id'],
            'quantity' => $qty,
            'price' => $product['price'],
            'subtotal' => $subtotal
        ];
    }
}

// Créer la commande
$stmt = $pdo->prepare("INSERT INTO orders (user_id, total, created_at) VALUES (?, ?, NOW())");
$stmt->execute([$user_id, $total]);
$order_id = $pdo->lastInsertId();

// Ajouter les articles à la commande
foreach ($order_items as $item) {
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $order_id,
        $item['product_id'],
        $item['quantity'],
        $item['price']
    ]);
}

// Vider le panier
unset($_SESSION['cart']);

include('templates/partials/head.php');
include('templates/partials/header.php');
?>

<section class="confirmation-page">
  <h2>Merci pour votre commande !</h2>
  <p>Votre commande n°<?= $order_id ?> a été enregistrée avec succès.</p>
  <p>Total payé : <strong>€<?= number_format($total, 2, ',', ' ') ?></strong></p>
  <a href="shop.php" class="btn-outline" style="margin-top: 20px; display:inline-block;">Retour à la boutique</a>
  <a href="mes_achats.php" class="btn-outline" style="margin-top: 10px; display:inline-block;">Voir mes commandes</a>
</section>

<?php include('templates/partials/footer.php'); ?>

