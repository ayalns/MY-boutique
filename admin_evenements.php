<?php
session_start();
require 'config.php';

// Vérifie que l'utilisateur est admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "Accès réservé à l'administrateur.";
    exit;
}

// Traitement des actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $event_id = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($action === 'valider') {
        $stmt = $pdo->prepare("UPDATE events SET statut = 'valide' WHERE id = ?");
        $stmt->execute([$event_id]);
    } elseif ($action === 'refuser') {
        $stmt = $pdo->prepare("UPDATE events SET statut = 'refuse' WHERE id = ?");
        $stmt->execute([$event_id]);
    } elseif ($action === 'supprimer') {
        $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$event_id]);
    }

    header("Location: admin_evenements.php");
    exit;
}

// Récupérer les événements en attente
$stmt = $pdo->query("SELECT e.*, u.nom AS auteur FROM events e JOIN users u ON e.soumis_par = u.id WHERE e.statut = 'en_attente'");
$events = $stmt->fetchAll();

include 'templates/partials/head.php';
include 'templates/partials/header.php';
?>

<div class="container">
  <h2 style="text-align:center;">Validation des Événements</h2>

  <?php if (empty($events)): ?>
    <p style="text-align:center;">Aucun événement en attente de validation.</p>
  <?php else: ?>
    <?php foreach ($events as $event): ?>
      <div class="event-item" style="border:1px solid #ccc; padding:20px; margin-bottom:20px; border-radius:8px;">
        <img src="assets/<?= htmlspecialchars($event['image']) ?>" alt="<?= htmlspecialchars($event['titre']) ?>" style="max-width:100%; height:auto;">
        <h4><?= htmlspecialchars($event['titre']) ?></h4>
        <p><strong>Soumis par :</strong> <?= htmlspecialchars($event['auteur']) ?></p>
        <p><?= nl2br(htmlspecialchars($event['description'])) ?></p>
        <div style="margin-top: 10px;">
          <a href="?action=valider&id=<?= $event['id'] ?>" class="btn-outline" style="color:green;">✔ Valider</a>
          <a href="?action=refuser&id=<?= $event['id'] ?>" class="btn-outline" style="color:orange;">✖ Refuser</a>
          <a href="?action=supprimer&id=<?= $event['id'] ?>" class="btn-outline" style="color:red;" onclick="return confirm('Supprimer cet événement ?');">🗑 Supprimer</a>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php include 'templates/partials/footer.php'; ?>
