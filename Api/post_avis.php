<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['error' => 'Non connectée']); exit;
}

$product_id  = (int) ($_POST['product_id'] ?? 0);
$note        = (int) ($_POST['note'] ?? 0);
$titre       = trim($_POST['titre'] ?? '');
$commentaire = trim($_POST['commentaire'] ?? '');

if (!$product_id || $note < 1 || $note > 5 || empty($titre) || strlen($commentaire) < 20) {
  echo json_encode(['error' => 'Données invalides. Le commentaire doit faire au moins 20 caractères.']); exit;
}

// Vérifier si l'utilisateur a déjà posté un avis sur ce produit
$stmt = $pdo->prepare("SELECT id FROM reviews WHERE user_id = ? AND product_id = ?");
$stmt->execute([$_SESSION['user_id'], $product_id]);
if ($stmt->fetch()) {
  echo json_encode(['error' => 'Vous avez déjà posté un avis sur ce produit.']); exit;
}

// Insérer l'avis
$stmt = $pdo->prepare("INSERT INTO reviews (user_id, product_id, note, titre, commentaire) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$_SESSION['user_id'], $product_id, $note, $titre, $commentaire]);

// Mettre à jour la note moyenne du produit
$stmt = $pdo->prepare("UPDATE products SET 
  note_moyenne = (SELECT AVG(note) FROM reviews WHERE product_id = ? AND is_approved = 1),
  nb_avis      = (SELECT COUNT(*) FROM reviews WHERE product_id = ? AND is_approved = 1)
  WHERE id = ?");
$stmt->execute([$product_id, $product_id, $product_id]);

echo json_encode(['success' => true]);
?>