<?php
session_start();
session_unset();
session_destroy();

header('Location: /glowy_shop/pages/index.php');
exit;
?>