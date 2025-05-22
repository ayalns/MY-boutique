<?php
session_start();
require 'config.php';

// 1. Vérification de l'ID produit
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$product_id = (int) $_GET['id'];

// 2. Récupérer les infos du produit
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute(['id' => $product_id]);
$product = $stmt->fetch();

if (!$product) {
    echo "Produit introuvable.";
    exit;
}

include('templates/partials/head.php');
include('templates/partials/header.php');
?>

<!-- Produit -->
<section id="product-detail" class="product-detail container">
  <h2><?= htmlspecialchars($product['nom']) ?></h2>
  <div class="detail-grid">
    <div class="detail-image">
      <img src="assets/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['nom']) ?>">
    </div>
    <div class="detail-info">
      <h3><?= htmlspecialchars($product['nom']) ?></h3>
      <p class="price"><strong><?= number_format($product['prix'], 2) ?> €</strong></p>
      <p><?= htmlspecialchars($product['description']) ?></p>
      <a href="add_to_cart.php?id=<?= $product_id ?>&nom=<?= urlencode($product['nom']) ?>&prix=<?= $product['prix'] ?>" class="btn">Ajouter au panier</a>
    </div>
  </div>
</section>

<!-- Avis clients -->
<section class="product-reviews container">
  <h3>Avis clients</h3>
  <?php
  $stmt = $pdo->prepare("
    SELECT pr.id, pr.commentaire, pr.note, pr.date_posted, pr.user_id, u.nom
    FROM product_reviews pr
    JOIN users u ON pr.user_id = u.id
    WHERE pr.product_id = :id
    ORDER BY pr.date_posted DESC
  ");
  $stmt->execute(['id' => $product_id]);
  $avis = $stmt->fetchAll();

  if ($avis):
    foreach ($avis as $a):
      $isMine = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $a['user_id'];
  ?>
    <div class="comment-card">
      <p><strong><?= htmlspecialchars($a['nom']) ?></strong> (<?= $a['note'] ?> ★)</p>
      <p><?= nl2br(htmlspecialchars($a['commentaire'])) ?></p>
      <small>Posté le <?= date('d/m/Y', strtotime($a['date_posted'])) ?></small>

      <?php if ($isMine): ?>
        <div style="margin-top:6px;">
          <a href="edit_review.php?id=<?= $a['id'] ?>&product=<?= $product_id ?>" class="btn-outline">Modifier</a>
          <a href="delete_review.php?id=<?= $a['id'] ?>&product=<?= $product_id ?>" class="btn-outline" style="color:red;" onclick="return confirm('Supprimer cet avis ?');">Supprimer</a>
        </div>
      <?php endif; ?>
    </div>
  <?php endforeach;
  else: ?>
    <p>Aucun avis pour ce produit.</p>
  <?php endif; ?>
</section>

<!-- Laisser un avis si l'utilisateur a acheté -->
<?php if (isset($_SESSION['user_id'])):
  $user_id = $_SESSION['user_id'];

  // Vérifie s'il a déjà noté
  $check = $pdo->prepare("SELECT id FROM product_reviews WHERE user_id = ? AND product_id = ?");
  $check->execute([$user_id, $product_id]);
  $alreadyReviewed = $check->fetch();

  if (!$alreadyReviewed):
    // Vérifie s'il a acheté
    $stmt = $pdo->prepare("
      SELECT oi.id FROM orders o
      JOIN order_items oi ON o.id = oi.order_id
      WHERE o.user_id = :user_id AND oi.product_id = :product_id
    ");
    $stmt->execute([
      'user_id' => $user_id,
      'product_id' => $product_id
    ]);

    if ($stmt->fetch()):
?>
<section class="review-form container">
  <h3>Laisser un avis</h3>
  <form method="POST" action="submit_review.php">
    <input type="hidden" name="product_id" value="<?= $product_id ?>">
    <textarea name="commentaire" rows="4" placeholder="Votre avis..." required></textarea><br>
    <label>Note :
      <select name="note" required>
        <option value="">--</option>
        <?php for ($i = 5; $i >= 1; $i--): ?>
          <option value="<?= $i ?>"><?= $i ?> ★</option>
        <?php endfor; ?>
      </select>
    </label><br><br>
    <button type="submit" class="btn">Envoyer</button>
  </form>
</section>
<?php
    endif;
  endif;
endif;
?>

<?php include('templates/partials/footer.php'); ?>