<?php
session_start();
include('config.php');

if (!isset($_SESSION['client_id'])) {
    header("Location: login.php");
    exit;
}

$client_id = $_SESSION['client_id'];
$erreur = $success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ancien = $_POST['ancien'];
    $nouveau = $_POST['nouveau'];
    $confirm = $_POST['confirm'];

    if ($nouveau !== $confirm) {
        $erreur = "Les nouveaux mots de passe ne correspondent pas.";
    } else {
        $stmt = $pdo->prepare("SELECT mot_de_passe FROM clients WHERE id = :id");
        $stmt->execute(['id' => $client_id]);
        $client = $stmt->fetch();

        if ($client && password_verify($ancien, $client['mot_de_passe'])) {
            $hash = password_hash($nouveau, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE clients SET mot_de_passe = :mdp WHERE id = :id");
            $stmt->execute(['mdp' => $hash, 'id' => $client_id]);

            $success = "Mot de passe modifié avec succès.";

// Envoyer l'email de confirmation
$stmtEmail = $pdo->prepare("SELECT email, prenom FROM clients WHERE id = :id");
$stmtEmail->execute(['id' => $client_id]);
$infos = $stmtEmail->fetch();

$to = $infos['email'];
$subject = "Modification de votre mot de passe – MY Boutique";
$message = "Bonjour " . $infos['prenom'] . ",\n\nVotre mot de passe a été modifié avec succès. Si ce n'était pas vous, contactez-nous immédiatement.\n\nMerci,\nMY Boutique";
$headers = "From: noreply@myboutique.com\r\n";

mail($to, $subject, $message, $headers);
        } else {
            $erreur = "Ancien mot de passe incorrect.";
        }
    }
}

include('templates/partials/head.php');
include('templates/partials/header.php');
?>

<div class="form-container register-form">
  <h2>Changer mon mot de passe</h2>

  <?php if ($erreur): ?><p style="color:red;"><?= $erreur ?></p><?php endif; ?>
  <?php if ($success): ?><p style="color:green;"><?= $success ?></p><?php endif; ?>

  <form method="POST">
    <label>Ancien mot de passe</label>
    <input type="password" name="ancien" required>

    <label>Nouveau mot de passe</label>
    <input type="password" name="nouveau" required>

    <label>Confirmer le nouveau</label>
    <input type="password" name="confirm" required>

    <button type="submit">Changer le mot de passe</button>
  </form>
</div>

<?php include('templates/partials/footer.php'); ?>
