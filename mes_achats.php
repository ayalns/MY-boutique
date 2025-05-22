<?php
session_start();
require __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Récupérer les commandes de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

include('templates/partials/head.php');
include('templates/partials/header.php');
?>

<div class="container">
  <h2>Mes Commandes</h2>

  <?php if (empty($orders)): ?>
    <p>Vous n'avez pas encore passé de commande.</p>
  <?php else: ?>
    <?php foreach ($orders as $order): ?>
      <div style="border:1px solid #ddd; padding:15px; margin-bottom:20px; border-radius:8px;">
        <h4>Commande #<?= $order['id'] ?> — <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></h4>
        <p><strong>Total :</strong> €<?= number_format((float)$order['total'], 2, ',', ' ') ?></p>

        <ul>
          <?php
          $stmt_items = $pdo->prepare("
            SELECT p.name, oi.quantity, oi.price
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
          ");
          $stmt_items->execute([$order['id']]);
          $items = $stmt_items->fetchAll();

          foreach ($items as $item):
          ?>
            <li><?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?> — €<?= number_format($item['price'], 2, ',', ' ') ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php include('templates/partials/footer.php'); ?>
