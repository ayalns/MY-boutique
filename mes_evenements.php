<?php
session_start();
include('config.php');

// Vérification connexion
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Supprimer un événement (seulement s'il est en attente et appartient à l'utilisateur)
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = :id AND soumis_par = :uid AND statut = 'en_attente'");
    $stmt->execute(['id' => $id, 'uid' => $user_id]);
    header("Location: mes_evenements.php");
    exit;
}

// Récupérer tous les événements de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM events WHERE soumis_par = :id ORDER BY date_soumission DESC");
$stmt->execute(['id' => $user_id]);
$events = $stmt->fetchAll();

include('templates/partials/head.php');
include('templates/partials/header.php');
?>

<div class="form-container">
  <h2>Mes Événements</h2>

  <?php foreach ($events as $event): ?>
    <div class="event-card" style="margin-bottom: 20px;">
      <img src="assets/<?= htmlspecialchars($event['image']) ?>" alt="" style="max-width:200px;">
      <h4><?= htmlspecialchars($event['titre']) ?></h4>
      <p><?= htmlspecialchars($event['description']) ?></p>
      <p><strong>Statut :</strong> <?= htmlspecialchars($event['statut']) ?></p>
      
      <?php if ($event['statut'] === 'en_attente'): ?>
        <a href="modifier_event.php?id=<?= $event['id'] ?>" class="btn-outline">Modifier</a>
        <a href="mes_evenements.php?delete=<?= $event['id'] ?>" class="btn-outline" style="color:red;" onclick="return confirm('Supprimer cet événement ?');">Supprimer</a>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
</div>

<?php include('templates/partials/footer.php'); ?>
