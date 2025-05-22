<?php
session_start();
require 'config.php';

// ✅ Restrict access to admin only (e.g. user_id 1 or role = 'admin')
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "⛔ Accès réservé à l’administrateur.";
    exit;
}

// 🔢 Total des ventes en euros
$totalVentes = $pdo->query("
    SELECT SUM(prix_unitaire * quantite) FROM order_items
")->fetchColumn();

// 🔢 Nombre total de commandes
$totalCommandes = $pdo->query("
    SELECT COUNT(*) FROM orders
")->fetchColumn();

// 🏆 Top 5 produits les plus vendus
$topProduits = $pdo->query("
    SELECT p.name AS nom, SUM(oi.quantite) AS total
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    GROUP BY p.name
    ORDER BY total DESC
    LIMIT 5
")->fetchAll();

include 'templates/partials/head.php';
include 'templates/partials/header.php';
?>

<div class="form-container">
  <h2 style="text-align: center;">📊 Tableau de bord administrateur</h2>

  <p><strong>Total des ventes :</strong> <?= number_format($totalVentes, 2, ',', ' ') ?> €</p>
  <p><strong>Nombre de commandes :</strong> <?= $totalCommandes ?></p>

  <h3 style="margin-top: 20px;">🏆 Top 5 produits vendus</h3>
  <ul style="padding-left: 20px;">
    <?php if (!empty($topProduits)): ?>
      <?php foreach ($topProduits as $produit): ?>
        <li><?= htmlspecialchars($produit['nom']) ?> – <?= $produit['total'] ?> vendus</li>
      <?php endforeach; ?>
    <?php else: ?>
      <li>Aucun produit vendu pour le moment.</li>
    <?php endif; ?>
  </ul>

  <hr style="margin: 30px 0;">

  <li><a href="admin_evenements.php" class="btn-outline">Valider ou refuser des événements</a></li>
  <a href="admin_produits.php" class="btn-outline" style="margin-left: 10px;">🛍 Gérer les produits</a>
</div>

<?php include 'templates/partials/footer.php'; ?>
