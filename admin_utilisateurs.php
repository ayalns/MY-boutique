<?php
session_start();
require 'config.php';

// Accès réservé à l'administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "Accès refusé.";
    exit;
}

// Suppression utilisateur
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id_to_delete = (int) $_GET['delete'];

    // Ne pas supprimer l'admin connecté lui-même
    if ($id_to_delete !== $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id_to_delete]);
        header("Location: admin_utilisateurs.php?deleted=1");
        exit;
    }
}

// Récupérer tous les utilisateurs sauf admin en cours
$stmt = $pdo->prepare("SELECT id, nom, prenom, email, role FROM users WHERE id != ?");
$stmt->execute([$_SESSION['user_id']]);
$users = $stmt->fetchAll();

include 'templates/partials/head.php';
include 'templates/partials/header.php';
?>

<div class="container">
  <h2 style="text-align:center;">Gestion des utilisateurs</h2>

  <?php if (isset($_GET['deleted'])): ?>
    <p style="color:green; text-align:center;">Utilisateur supprimé avec succès.</p>
  <?php endif; ?>

  <table class="cart-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Email</th>
        <th>Rôle</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $u): ?>
        <tr>
          <td><?= $u['id'] ?></td>
          <td><?= htmlspecialchars($u['nom']) ?></td>
          <td><?= htmlspecialchars($u['prenom']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= htmlspecialchars($u['role']) ?></td>
          <td>
            <a href="?delete=<?= $u['id'] ?>" class="btn-outline" style="color:red;" onclick="return confirm('Supprimer cet utilisateur ?');">
              Supprimer
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php include 'templates/partials/footer.php'; ?>
