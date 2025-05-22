<?php
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cart_count += $qty;
    }
}
?>

<header class="site-header">
  <div class="header-wrapper">
    <div class="logo"><a href="index.php">MY Boutique</a></div>

    <nav class="main-nav">
      <ul>
        <li><a href="index.php">Accueil</a></li>
        <li><a href="shop.php">Boutique</a></li>

        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="dropdown">
  <a href="#" class="dropdown-toggle">Mon compte ⌄</a>
  <ul class="dropdown-menu">
    <li><a href="profil.php">Gérer mes infos</a></li>
    <li><a href="mes_achats.php">Mes achats</a></li>
    <li><a href="logout.php">Se déconnecter</a></li>
  </ul>
</li>

        <?php else: ?>
          <li><a href="login.php">Connexion</a></li>
        <?php endif; ?>

        <li class="cart-icon">
          <a href="cart.php">
            <img src="assets/15.png" alt="Panier">
            <?php if ($cart_count > 0): ?>
              <span class="cart-count"><?= $cart_count ?></span>
            <?php endif; ?>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</header>





