<?php
session_start();
require 'config.php';

// Accès réservé au personnel IT
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'it') {
    echo "Accès refusé.";
    exit;
}

// Message de feedback
$feedback = '';
if (isset($_GET['ajout'])) $feedback = "Produit ajouté avec succès.";
if (isset($_GET['modif'])) $feedback = "Produit modifié avec succès.";
if (isset($_GET['supp']))  $feedback = "Produit supprimé avec succès.";

// 🔎 Liste des produits
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$produits = $stmt->fetchAll();

include 'templates/partials/head.php';
include 'templates/partials/header.php';
?>

<div class="container">
  <h2 style="text-align:center;">Gestion des Produits</h2>

  <div style="text-align: right; margin-bottom: 20px;">
    <a href="ajouter_produit.php" class="btn">+ Ajouter un produit</a>
  </div>

  <?php if ($feedback): ?>
    <p style="color: green; text-align:center;"><?= $feedback ?></p>
  <?php endif; ?>

  <table class="cart-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Prix</th>
        <th>Stock</th>
        <th>Image</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($produits as $p): ?>
        <tr>
          <td><?= $p['id'] ?></td>
          <td><?= htmlspecialchars($p['nom']) ?></td>
          <td><?= number_format($p['prix'], 2) ?> €</td>
          <td><?= $p['stock'] ?></td>
          <td>
            <?php if ($p['image']): ?>
              <img src="assets/<?= htmlspecialchars($p['image']) ?>" style="max-width: 60px;">
            <?php endif; ?>
          </td>
          <td>
            <a href="modifier_produit.php?id=<?= $p['id'] ?>" class="btn-outline">Modifier</a>
            <a href="supprimer_produit.php?id=<?= $p['id'] ?>" class="btn-outline" style="color:red;" onclick="return confirm('Supprimer ce produit ?');">Supprimer</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php include 'templates/partials/footer.php'; ?>
