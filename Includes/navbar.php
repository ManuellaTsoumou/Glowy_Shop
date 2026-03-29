<nav class="navbar">
  <div class="navbar-brand">
    <a href="/glowy_shop/pages/index.php">glowy_shop</a>
  </div>

  <ul class="navbar-links">
    <li><a href="/glowy_shop/pages/catalogue.php">Catalogue</a></li>
    <li><a href="/glowy_shop/pages/quiz.php">Quiz beauté</a></li>

    <?php if (isset($_SESSION['user_id'])) : ?>
      <li><a href="/glowy_shop/pages/profil.php">Mon profil</a></li>
      <?php if ($_SESSION['role'] === 'admin') : ?>
        <li><a href="/glowy_shop/admin/index.php">Admin</a></li>
      <?php endif; ?>
      <li><a href="/glowy_shop/auth/logout.php">Déconnexion</a></li>
    <?php else : ?>
      <li><a href="/glowy_shop/auth/login.php">Connexion</a></li>
      <li><a href="/glowy_shop/auth/register.php">Inscription</a></li>
    <?php endif; ?>
  </ul>
</nav>