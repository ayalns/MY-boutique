<?php
session_start();
include('templates/partials/head.php');
include('templates/partials/header.php');
?>

<!-- 1. Hero Slider -->
<section id="hero" class="hero-slider">
  <div class="slide active" style="background-image:url('assets/1.png')">
    <div class="overlay">
      <h2>Découvrez la Collection Printemps</h2>
      <a href="shop.php" class="btn">Voir la collection</a>
    </div>
  </div>
  <div class="slide" style="background-image:url('assets/2.png')">
    <div class="overlay">
      <h2>Nouveautés Vestes & Manteaux</h2>
      <a href="shop.php" class="btn">Shoppez maintenant</a>
    </div>
  </div>
</section>

<!-- 2. Shop Preview -->
<section id="shop" class="shop-overview container">
  <h2>Nos indispensables mode</h2>
  <div class="product-row">
    <div class="product-group">
      <h3>Nouveautés</h3>
      <div class="products">
        <div class="product-card-mini"><img src="assets/3.png" alt="Robe fleurie"><p>Robe Fleurie</p></div>
        <div class="product-card-mini"><img src="assets/4.png" alt="Chemise en lin"><p>Chemise Lin</p></div>
        <div class="product-card-mini"><img src="assets/5.png" alt="Pantalon chino"><p>Pantalon Chino</p></div>
        <div class="product-card-mini"><img src="assets/6.png" alt="T-shirt oversize"><p>T-Shirt Oversize</p></div>
      </div>
    </div>
    <div class="product-group">
      <h3>Best-sellers</h3>
      <div class="products">
        <div class="product-card-mini"><img src="assets/7.png" alt="Veste en cuir"><p>Veste Cuir</p></div>
        <div class="product-card-mini"><img src="assets/8.png" alt="Jean slim"><p>Jean Slim</p></div>
        <div class="product-card-mini"><img src="assets/9.png" alt="Sneakers blanches"><p>Sneakers</p></div>
        <div class="product-card-mini"><img src="assets/10.png" alt="Sac bandoulière"><p>Sac Bandoulière</p></div>
      </div>
    </div>
  </div>
</section>

<!-- 3. À propos -->
<section class="about container">
  <h2>À propos de MY Boutique</h2>
  <div class="about-content">
    <img src="assets/11.png" alt="Notre histoire">
    <div class="about-text">
      <p>Depuis 2015, MY Boutique propose des pièces sélectionnées pour leur style unique et leur qualité irréprochable.</p>
      <a href="about.html" class="btn-outline">En savoir plus</a>
    </div>
  </div>
</section>

<!-- 4. Blog Preview -->
<section id="blog" class="blog-overview container">
  <h2 class="section-title">Le style selon MY</h2>
  <div class="blog-grid">
    <div class="blog-card">
      <img src="assets/12.png" alt="Tendance florale">
      <h3>Tendance florale</h3>
      <p>Comment adopter les imprimés fleuris cette saison.</p>
      <a href="blog.html" class="read-more">Lire la suite</a>
    </div>
    <div class="blog-card">
      <img src="assets/13.png" alt="Couleurs pastel">
      <h3>Pastels doux</h3>
      <p>Les couleurs pastel qui sublimeront votre garde-robe.</p>
      <a href="blog.html" class="read-more">Lire la suite</a>
    </div>
    <div class="blog-card">
      <img src="assets/14.png" alt="Accessoiriser votre tenue">
      <h3>Accessoirisation</h3>
      <p>Le guide pour choisir le sac et les bijoux parfaits.</p>
      <a href="blog.html" class="read-more">Lire la suite</a>
    </div>
  </div>
</section>

<!-- 5. Produit Détail -->
<section id="product-detail" class="product-detail container">
  <h2>Fiche produit</h2>
  <div class="detail-grid">
    <div class="detail-image">
      <img src="assets/16.png" alt="Veste en jean oversize">
    </div>
    <div class="detail-info">
      <h3>Veste en jean oversize</h3>
      <p class="price"><strong>€89.99</strong></p>
      <p>Veste oversize en jean brut, boutonnières argentées, deux poches latérales.</p>
      <button class="btn">Ajouter au panier</button>
    </div>
  </div>
</section>

<!-- 6. Événements validés -->
<section id="events" class="event-section container">
  <h2>Événements & Salons</h2>
  <div class="event-gallery">
    <?php
    include('config.php');
    $stmt = $pdo->query("SELECT id, titre, image, description, statut FROM events WHERE statut = 'valide' ORDER BY date_soumission DESC LIMIT 6");
    $events = $stmt->fetchAll();

    if (!empty($events)):
      foreach ($events as $event): ?>
        <div class="event-item" style="border:1px solid #ddd; padding:15px; margin-bottom:20px; border-radius:8px;">
          <img src="assets/<?= htmlspecialchars($event['image']) ?>" alt="<?= htmlspecialchars($event['titre']) ?>" style="max-width:100%; height:auto;">
          <h4><?= htmlspecialchars($event['titre']) ?></h4>
          <p><strong>Statut :</strong> <?= htmlspecialchars($event['statut']) ?></p>
          <p><?= nl2br(htmlspecialchars(substr($event['description'], 0, 100))) ?>...</p>
          <a href="event_detail.php?id=<?= $event['id'] ?>" class="btn-outline">Voir les détails</a>
        </div>
      <?php endforeach;
    else: ?>
      <p><em>Aucun événement à afficher pour le moment.</em></p>
    <?php endif; ?>
  </div>
</section>

<!-- 7. Contact -->
<section id="contact" class="contact container">
  <h2>Contactez-nous</h2>
  <form action="contact_submit.php" method="post" class="contact-form">
    <input type="text" name="nom" placeholder="Nom et prénom" required>
    <input type="email" name="email" placeholder="Adresse e-mail" required>
    <textarea name="message" rows="5" placeholder="Votre message" required></textarea>
    <button type="submit" class="btn">Envoyer</button>
  </form>
</section>

<!-- 8. Commentaires -->
<section id="comments" class="comments container">
  <h2>Avis clients</h2>
  <div class="comment-list">
    <div class="comment-card"><p><strong>Claire :</strong> J’adore ma nouvelle veste en jean – coupe parfaite !</p></div>
    <div class="comment-card"><p><strong>Antoine :</strong> Les Chemises en lin sont incroyablement confortables.</p></div>
    <div class="comment-card"><p><strong>Juliette :</strong> Service client réactif et livraison rapide, merci !</p></div>
  </div>
</section>

<?php include('templates/partials/footer.php'); ?>

<script>
// Simple slider JS
const slides = document.querySelectorAll('.hero-slider .slide');
let current = 0;
setInterval(() => {
  slides[current].classList.remove('active');
  current = (current + 1) % slides.length;
  slides[current].classList.add('active');
}, 5000);
</script>
