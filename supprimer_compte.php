<?php
session_start();
include('config.php');

if (!isset($_SESSION['client_id'])) {
    header('Location: login.php');
    exit;
}

$id = $_SESSION['client_id'];

$stmt = $pdo->prepare("DELETE FROM clients WHERE id = :id");
$stmt->execute(['id' => $id]);

session_destroy();
header("Location: index.php");
exit;
?>
