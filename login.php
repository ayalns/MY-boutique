<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=my_boutique_db;charset=utf8", "root", "root");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
        $_SESSION['user_id'] = $user['id']; // 👈 remplacé client_id par user_id
        $_SESSION['role'] = $user['role'];
        header("Location: index.php");
        exit;
    } else {
        $erreur = "Identifiants incorrects.";
    }
}

include('templates/partials/head.php');
include('templates/partials/header.php');
?>

<div class="form-container login-form">
  <h2>Connexion</h2>

  <?php if (!empty($erreur)): ?>
    <div style="color:red;text-align:center;margin-bottom:15px"><?= htmlspecialchars($erreur); ?></div>
  <?php endif; ?>

  <form method="POST">
    <div>
      <label for="email">Email</label>
      <span class="input-icon">📧</span>
      <input type="email" name="email" id="email" required>
    </div>
    <div>
      <label for="mot_de_passe">Mot de passe</label>
      <span class="input-icon">🔒</span>
      <input type="password" name="mot_de_passe" id="mot_de_passe" required>
    </div>
    <button type="submit">Se connecter</button>
  </form>

  <div style="text-align: center; margin-top: 20px;">
    <p>Pas encore de compte ?</p>
    <a href="register.php" class="btn-outline">Créer un compte</a>
  </div>
</div>

<?php include('templates/partials/footer.php'); ?>