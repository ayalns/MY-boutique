<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Contact – MY Boutique</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
  <?php include __DIR__ . '/partials/header.php'; ?>

  <main class="container">
    <h1>Contactez-nous</h1>
    <p>Une question ? Remplissez le formulaire ci-dessous, nous vous répondrons sous 48 h.</p>

    <form action="/contact-submit.php" method="post" class="contact-form">
      <label for="nom">Nom et prénom :</label>
      <input type="text" id="nom" name="nom" required>

      <label for="email">Adresse e-mail :</label>
      <input type="email" id="email" name="email" required>

      <label for="message">Votre message :</label>
      <textarea id="message" name="message" rows="5" required></textarea>

      <button type="submit">Envoyer</button>
    </form>
  </main>

  <?php include __DIR__ . '/partials/footer.php'; ?>
  <script src="/js/main.js"></script>
</body>
</html>
