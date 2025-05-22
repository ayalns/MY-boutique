<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$review_id  = (int)$_GET['id'];
$product_id = (int)$_GET['product'];

// 1️⃣ Vérifier que cet avis appartient à l’utilisateur
$stmt = $pdo->prepare("SELECT * FROM product_reviews WHERE id = ? AND user_id = ?");
$stmt->execute([$review_id, $_SESSION['user_id']]);
$review = $stmt->fetch();
if (!$review) { header("Location: product.php?id=$product_id"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $commentaire = trim($_POST['commentaire']);
  $note        = (int)$_POST['note'];

  if ($commentaire !== '' && $note >= 1 && $note <= 5) {
    $upd = $pdo->prepare("
      UPDATE product_reviews
      SET commentaire = :c, note = :n, date_posted = NOW()
      WHERE id = :id AND user_id = :u
    ");
    $upd->execute([
      'c'  => $commentaire,
      'n'  => $note,
      'id' => $review_id,
      'u'  => $_SESSION['user_id']
    ]);
  }
  header("Location: product.php?id=$product_id");
  exit;
}

include 'templates/partials/head.php';
include 'templates/partials/header.php';
?>

<div class="form-container">
  <h2>Modifier mon avis</h2>
  <form method="POST">
    <textarea name="commentaire" rows="4" required><?= htmlspecialchars($review['commentaire']) ?></textarea><br>
    <label>Note :
      <select name="note" required>
        <?php for ($i = 5; $i >= 1; $i--): ?>
          <option value="<?= $i ?>" <?= $i == $review['note'] ? 'selected' : '' ?>>
            <?= $i ?> ★
          </option>
        <?php endfor; ?>
      </select>
    </label><br><br>
    <button type="submit" class="btn">Mettre à jour</button>
    <a href="product.php?id=<?= $product_id ?>" class="btn-outline">Annuler</a>
  </form>
</div>

<?php include 'templates/partials/footer.php'; ?>
