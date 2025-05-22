<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=my_boutique_db;charset=utf8", "root", "root");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $adresse = trim($_POST['adresse']);
    $email = trim($_POST['email']);
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
    $carte = !empty($_POST['carte']) ? $_POST['carte'] : null;

    if ($nom && $prenom && $adresse && $email && $mot_de_passe) {
        $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, adresse, email, mot_de_passe, carte_bancaire)
                               VALUES (:nom, :prenom, :adresse, :email, :mot_de_passe, :carte)");
        $stmt->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'adresse' => $adresse,
            'email' => $email,
            'mot_de_passe' => $mot_de_passe,
            'carte' => $carte
        ]);
        $_SESSION['message'] = "Inscription réussie.";
        header("Location: login.php");
        exit;
    } else {
        $erreur = "Veuillez remplir tous les champs obligatoires.";
    }
}
include('templates/partials/head.php');
include('templates/partials/header.php');
?>

<div class="form-container register-form">
  <h2>Créer un compte</h2>

  <?php if (!empty($erreur)): ?>
    <div style="color:red;text-align:center;margin-bottom:15px"><?= $erreur; ?></div>
  <?php endif; ?>

  <form method="POST">
    <div>
      <label for="nom">Nom *</label>
      <span class="input-icon">👤</span>
      <input type="text" name="nom" id="nom" required>
    </div>
    <div>
      <label for="prenom">Prénom *</label>
      <span class="input-icon">👤</span>
      <input type="text" name="prenom" id="prenom" required>
    </div>
    <div>
      <label for="adresse">Adresse *</label>
      <span class="input-icon">🏠</span>
      <textarea name="adresse" id="adresse" required></textarea>
    </div>
    <div>
      <label for="email">Email *</label>
      <span class="input-icon">📧</span>
      <input type="email" name="email" id="email" required>
    </div>
    <div>
      <label for="mot_de_passe">Mot de passe *</label>
      <span class="input-icon">🔒</span>
      <input type="password" name="mot_de_passe" id="mot_de_passe" required>
    </div>
    <div>
      <label for="carte">Carte bancaire (facultatif)</label>
      <span class="input-icon">💳</span>
      <input type="text" name="carte" id="carte">
    </div>
    <button type="submit">S’inscrire</button>
  </form>
</div>

<?php include('templates/partials/footer.php'); ?>