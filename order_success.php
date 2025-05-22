<?php
// order_success.php
session_start();
require __DIR__ . '/config.php';
$id = (int)($_GET['order_id']??0);
if(!$id) header('Location: shop.php');
$order = $pdo->prepare("SELECT * FROM orders WHERE id=? AND user_id=?");
$order->execute([$id,$_SESSION['user_id']]);
if(!$order->fetch()) header('Location: shop.php');
include __DIR__ . '/templates/partials/header.php';
?>
<main class="container">
  <h1>Merci pour votre commande !</h1>
  <p>Numéro de commande : <?=htmlspecialchars($id)?></p>
  <a href="orders.php" class="btn">Voir mon historique de commandes</a>
</main>
<?php include __DIR__ . '/templates/partials/footer.php'; ?>
