<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header('Location: /glowy_shop/pages/index.php');
  exit;
}
?>