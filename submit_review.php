<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
$commentaire = trim($_POST['commentaire']);
$note = isset($_POST['note']) && is_numeric($_POST['note']) ? (int) $_POST['note'] : null;

// Validation
if (!$product_id || $commentaire === '' || $note < 1 || $note > 5) {
  header("Location: product.php?id=" . $product_id);
  exit;
}

// Optional: prevent duplicate review by the same user
$stmt = $pdo->prepare("SELECT id FROM product_reviews WHERE user_id = ? AND product_id = ?");
$stmt->execute([$user_id, $product_id]);
if ($stmt->fetch()) {
  // Already reviewed — redirect or show a message
  header("Location: product.php?id=" . $product_id);
  exit;
}

// Insert review
$stmt = $pdo->prepare("
  INSERT INTO product_reviews (user_id, product_id, commentaire, note)
  VALUES (:user_id, :product_id, :commentaire, :note)
");

$stmt->execute([
  'user_id' => $user_id,
  'product_id' => $product_id,
  'commentaire' => $commentaire,
  'note' => $note
]);

header("Location: product.php?id=" . $product_id);
exit;

