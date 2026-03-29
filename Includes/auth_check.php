<?php
if (!isset($_SESSION['user_id'])) {
  header('Location: /glowy_shop/auth/login.php');
  exit;
}
?>