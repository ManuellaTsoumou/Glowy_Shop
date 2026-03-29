<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$conditions = ["p.is_active = 1"];
$params     = [];

if (!empty($_GET['categorie'])) {
  $conditions[] = "p.categorie_id = ?";
  $params[]     = (int) $_GET['categorie'];
}

if (!empty($_GET['skin'])) {
  $conditions[] = "JSON_CONTAINS(p.skin_types, ?)";
  $params[]     = json_encode($_GET['skin']);
}

if (!empty($_GET['prix_max'])) {
  $conditions[] = "p.prix <= ?";
  $params[]     = (float) $_GET['prix_max'];
}

$where = implode(' AND ', $conditions);
$sql   = "SELECT p.*, c.nom as categorie_nom
          FROM products p
          LEFT JOIN categories c ON p.categorie_id = c.id
          WHERE $where
          ORDER BY p.note_moyenne DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$produits = $stmt->fetchAll();

echo json_encode($produits);
?>