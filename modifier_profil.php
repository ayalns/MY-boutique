<?php
session_start();
include('config.php');

if (!isset($_SESSION['client_id'])) {
    header("Location: login.php");
    exit;
}

$client_id = $_SESSION['client_id'];

$stmt = $pdo->prepare("SELECT nom, prenom, adresse, carte_bancaire FROM clients WHERE id = :id");
$stmt->execute(['id' => $client_id]);
$client = $stmt->fetch();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $adresse = trim($_POST['adresse']);
    $carte = trim($_POST['carte_bancaire']);

    $stmt = $pdo->prepare("UPDATE clients SET nom = :nom, prenom = :prenom, adresse = :adresse, carte_bancaire = :carte WHERE id = :id");
    $stmt->execute([
        'nom' => $nom,
        'prenom' => $prenom,
        'adresse' => $adresse,
        'carte' => $carte,
        'id' => $client_id
    ]);

    $_SESSION['message'] = "Profil mis à jour avec succès.";
    header("Location: profil.php");
    exit;
}

include('templates/partials/head.php');
include('templates/partials/header.php');
?>

<div class="form-container register-form">
  <h2>Modifier mon profil</h2>
  <form method="POST">
    <label>Nom</label>
    <input type="text" name="nom" value="<?= htmlspecialchars($client['nom']) ?>" required>
    
    <label>Prénom</label>
    <input type="text" name="prenom" value="<?= htmlspecialchars($client['prenom']) ?>" required>
    
    <label>Adresse</label>
    <textarea name="adresse" required><?= htmlspecialchars($client['adresse']) ?></textarea>
    
    <label>Carte bancaire (facultatif)</label>
    <input type="text" name="carte_bancaire" value="<?= htmlspecialchars($client['carte_bancaire']) ?>">
    
    <button type="submit">Enregistrer</button>
  </form>
</div>

<?php include('templates/partials/footer.php'); ?>
