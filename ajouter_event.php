<?php
session_start();
include('config.php');

// Vérifier si connecté
if (!isset($_SESSION['client_id'])) {
    header("Location: login.php");
    exit;
}

$client_id = $_SESSION['client_id'];
$erreur = $success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);

    // Gestion image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], 'assets/' . $filename);

        $stmt = $pdo->prepare("INSERT INTO events (titre, image, description, submitted_by) 
                               VALUES (:titre, :image, :description, :uid)");
        $stmt->execute([
            'titre' => $titre,
            'image' => $filename,
            'description' => $description,
            'uid' => $client_id
        ]);

        $success = "Événement soumis pour validation.";
    } else {
        $erreur = "Erreur lors de l’upload de l’image.";
    }
}

include('templates/partials/head.php');
include('templates/partials/header.php');
?>

<div class="form-container">
  <h2>Publier un événement</h2>
  <?php if ($erreur): ?><p style="color:red;"><?= $erreur ?></p><?php endif; ?>
  <?php if ($success): ?><p style="color:green;"><?= $success ?></p><?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <label>Titre</label>
    <input type="text" name="titre" required>

    <label>Description</label>
    <textarea name="description" required></textarea>

    <label>Image (PNG, JPG)</label>
    <input type="file" name="image" accept="image/*" required>

    <button type="submit" class="btn">Soumettre</button>
  </form>
</div>

<?php include('templates/partials/footer.php'); ?>
