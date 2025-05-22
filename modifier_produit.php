<?php
session_start();
require 'config.php';

// 🔒 Vérification de rôle
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'it') {
    echo "Accès refusé.";
    exit;
}

// 🔎 Vérifier l'ID du produit
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Produit introuvable.";
    exit;
}
$product_id = (int)$_GET['id'];

// 📦 Récupérer le produit
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    echo "Produit introuvable.";
    exit;
}

// ✅ Traitement de mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $description = trim($_POST['description']);
    $prix = floatval($_POST['prix']);
    $stock = intval($_POST['stock']);
    $image = $product['image']; // Par défaut, garder l'ancienne image

    // 🖼 Nouvelle image ?
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = 'assets/';
        $filename = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetFile = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $image = $filename;
        }
    }

    // Mise à jour SQL
    $upd = $pdo->prepare("UPDATE products SET nom = :nom, description = :description, prix = :prix, stock = :stock, image = :image WHERE id = :id");
    $upd->execute([
        'nom' => $nom,
        'description' => $description,
        'prix' => $prix,
        'stock' => $stock,
        'image' => $image,
        'id' => $product_id
    ]);

    header("Location: produits_it.php?modif=ok");
    exit;
}

include 'templates/partials/head.php';
include 'templates/partials/header.php';
?>

<div class="form-container">
  <h2>Modifier le produit</h2>

  <form method="POST" enctype="multipart/form-data">
    <div>
      <label>Nom *</label>
      <input type="text" name="nom" value="<?= htmlspecialchars($product['nom']) ?>" required>
    </div>
    <div>
      <label>Description</label>
      <textarea name="description" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
    </div>
    <div>
      <label>Prix (€) *</label>
      <input type="number" step="0.01" name="prix" value="<?= htmlspecialchars($product['prix']) ?>" required>
    </div>
    <div>
      <label>Stock disponible *</label>
      <input type="number" name="stock" value="<?= htmlspecialchars($product['stock']) ?>" required>
    </div>
    <div>
      <label>Image actuelle :</label><br>
      <?php if ($product['image']): ?>
        <img src="assets/<?= htmlspecialchars($product['image']) ?>" alt="" style="max-width:150px;"><br><br>
      <?php endif; ?>
      <label>Nouvelle image (facultatif)</label>
      <input type="file" name="image" accept="image/*">
    </div>
    <button type="submit" class="btn">Enregistrer les modifications</button>
    <a href="produits_it.php" class="btn-outline">Annuler</a>
  </form>
</div>

<?php include 'templates/partials/footer.php'; ?>
