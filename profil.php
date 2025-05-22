<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Récupération des données utilisateur
$stmt = $pdo->prepare("SELECT nom, prenom, email, adresse, carte_bancaire FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $adresse = $_POST['adresse'];
    $carte = $_POST['carte'];

    $update = $pdo->prepare("UPDATE users SET nom=?, prenom=?, email=?, adresse=?, carte_bancaire=? WHERE id=?");
    $update->execute([$nom, $prenom, $email, $adresse, $carte, $user_id]);

    $message = "Profil mis à jour avec succès.";
}

include 'templates/partials/head.php';
include 'templates/partials/header.php';
?>

<div class="form-container profile-form">
  <h2>Mon Profil</h2>

  <?php if (!empty($message)): ?>
    <p style="color:green;text-align:center;"><?= $message ?></p>
  <?php endif; ?>

  <form method="POST">
    <div>
      <label>Nom</label>
      <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>
    </div>
    <div>
      <label>Prénom</label>
      <input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required>
    </div>
    <div>
      <label>Email</label>
      <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
    </div>
    <div>
      <label>Adresse</label>
      <textarea name="adresse" required><?= htmlspecialchars($user['adresse']) ?></textarea>
    </div>
    <div>
      <label>Carte bancaire</label>
      <input type="text" name="carte" value="<?= htmlspecialchars($user['carte_bancaire']) ?>">
    </div>
    <button type="submit">Mettre à jour</button>
  </form>
</div>

<?php include 'templates/partials/footer.php'; ?>


