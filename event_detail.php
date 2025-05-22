<?php
session_start();
include('config.php');

// Vérification que l'ID est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int) $_GET['id'];

// Récupération de l'événement
$stmt = $pdo->prepare("SELECT titre, description, image, statut, date_soumission FROM events WHERE id = :id AND statut = 'valide'");
$stmt->execute(['id' => $id]);
$event = $stmt->fetch();

// Si l'événement n'existe pas ou n'est pas validé
if (!$event) {
    header('Location: index.php');
    exit;
}

include('templates/partials/head.php');
include('templates/partials/header.php');
?>

<section class="container">
  <h2><?= htmlspecialchars($event['titre']) ?></h2>
  <div class="event-detail" style="margin-top: 20px;">
    <img src="assets/<?= htmlspecialchars($event['image']) ?>" alt="<?= htmlspecialchars($event['titre']) ?>" style="max-width:100%; border-radius: 10px;">
    <p style="margin-top: 15px;"><strong>Date de publication :</strong> <?= date('d/m/Y', strtotime($event['date_soumission'])) ?></p>
    <p><strong>Statut :</strong> <?= htmlspecialchars($event['statut']) ?></p>
    <p style="margin-top: 20px; white-space: pre-wrap; line-height: 1.6;">
      <?= nl2br(htmlspecialchars($event['description'])) ?>
    </p>
  </div>
  <a href="index.php" class="btn-outline" style="margin-top: 30px; display: inline-block;">Retour à l'accueil</a>
</section>

<?php include('templates/partials/footer.php'); ?>