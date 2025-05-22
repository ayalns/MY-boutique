<?php
session_start();
require 'config.php';

// Restriction d'accès
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'it') {
    echo "Accès réservé au personnel IT/Commercial.";
    exit;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $description = trim($_POST['description']);
    $prix = floatval($_POST['prix']);
    $stock = intval($_POST['stock']);
    $image = '';

    // Upload de l'image
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = 'assets/';
        $filename = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetFile = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $image = $filename;
        }
    }

    // Insertion dans la BDD
    if ($nom && $prix > 0 && $stock >= 0) {
        $stmt = $pdo->prepare("INSERT INTO products (nom, description, prix, stock, image) 
                               VALUES (:nom, :description, :prix, :stock, :image)");
        $stmt->execute([
            'nom' => $nom,
            'description' => $description,
            'prix' => $prix,
            'stock' => $stock,
            'image' => $image
        ]);
        header("Location: produits_it.php?ajout=ok");
        exit;
    } else {
        $erreur = "Veuillez remplir tous les champs correctement.";
    }
}

include 'templates/partials/head.php';
include 'templates/partials/header.php';
?>

<div class="form-container">
  <h2>Ajouter un produit</h2>

  <?php if (!empty($erreur)): ?>
    <p style="color:red;"><?= $erreur ?></p>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <div>
      <label>Nom du produit *</label>
      <input type="text" name="nom" required>
    </div>
    <div>
      <label>Description</label>
      <textarea name="description" rows="4"></textarea>
    </div>
    <div>
      <label>Prix (€) *</label>
      <input type="number" step="0.01" name="prix" required>
    </div>
    <div>
      <label>Stock disponible *</label>
      <input type="number" name="stock" required>
    </div>
    <div>
      <label>Image (JPG/PNG)</label>
      <input type="file" name="image" accept="image/*">
    </div>
    <button type="submit" class="btn">Ajouter le produit</button>
  </form>
</div>

<?php include 'templates/partials/footer.php'; ?>
