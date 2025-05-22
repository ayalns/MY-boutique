<?php
// contact-submit.php
session_start();
if($_SERVER['REQUEST_METHOD']!=='POST') exit('Accès direct non autorisé');
$name    = trim($_POST['nom']);
$email   = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$message = trim($_POST['message']);
// Ici vous pourriez envoyer un e-mail ou stocker en BDD
// mail('support@myboutique.com', "Contact de $name", $message, "From:$email");
header('Location: index.php?msg=Votre+message+a+été+envoyé');
exit;
