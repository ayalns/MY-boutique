<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$review_id  = (int)$_GET['id'];
$product_id = (int)$_GET['product'];

$del = $pdo->prepare("
  DELETE FROM product_reviews
  WHERE id = :id AND user_id = :u
");
$del->execute([
  'id' => $review_id,
  'u'  => $_SESSION['user_id']
]);

header("Location: product.php?id=$product_id");
exit;
