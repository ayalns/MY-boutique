<?php
session_start();
include('config.php');

// Récupération des catégories distinctes
$categories = $pdo->query("SELECT DISTINCT categorie FROM products")
                ->fetchAll(PDO::FETCH_COLUMN);

// Filtre actif
$filtre = isset($_GET['categorie']) ? $_GET['categorie'] : null;

// Récupération des produits (filtrés ou non)
if ($filtre && in_array($filtre, $categories)) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE categorie = :cat");
    $stmt->execute(['cat' => $filtre]);
} else {
    $stmt = $pdo->query("SELECT * FROM products");
}
$produits = $stmt->fetchAll();

include('templates/partials/head.php');
include('templates/partials/header.php');
?>

<div class="container boutique">
  <h2>Notre Boutique</h2>

  <!-- Filtres par catégorie -->
  <div class="categories">
    <a href="shop.php" class="<?= !$filtre ? 'active' : '' ?>">Tous</a>
    <?php foreach ($categories as $cat): ?>
      <a href="shop.php?categorie=<?= urlencode($cat) ?>" class="<?= ($filtre === $cat) ? 'active' : '' ?>">
        <?= htmlspecialchars($cat) ?>
      </a>
    <?php endforeach; ?>
  </div>

  <!-- Grille des produits -->
  <div class="product-grid">
    <?php foreach ($produits as $p): ?>
      <div class="product-card">
        <img src="assets/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
        <h4><?= htmlspecialchars($p['name']) ?></h4>
        <p><?= number_format($p['price'], 2) ?> €</p>
        <a href="add_to_cart.php?id=<?= $p['id'] ?>" class="btn">Ajouter au panier</a>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php include('templates/partials/footer.php'); ?>
