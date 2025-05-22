<?php
session_start();
include('templates/partials/head.php');
include('templates/partials/header.php');
?>

<div class="form-container confirmation-page">
  <h2>Commande confirmée ✅</h2>
  <p>Merci pour votre achat chez <strong>MY Boutique</strong> !</p>
  <p>Votre commande a bien été enregistrée. Un e-mail de confirmation vous sera envoyé sous peu.</p>

  <div style="text-align:center; margin-top: 30px;">
    <a href="index.php" class="btn-outline">Retour à l'accueil</a>
    <a href="profil.php" class="btn-outline" style="margin-left:10px;">Voir mon profil</a>
  </div>
</div>

<?php include('templates/partials/footer.php'); ?>
