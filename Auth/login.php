<?php
session_start();

// Si déjà connectée, on redirige
if (isset($_SESSION['user_id'])) {
  header('Location: /glowy_shop/pages/index.php');
  exit;
}

require_once __DIR__ . '/../config/db.php';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $mdp   = $_POST['mot_de_passe'] ?? '';

  if (empty($email) || empty($mdp)) {
    $erreur = 'Tous les champs sont obligatoires.';
  } else {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($mdp, $user['mot_de_passe'])) {
      // Sécurité : regénérer l'ID de session
      session_regenerate_id(true);

      $_SESSION['user_id'] = $user['id'];
      $_SESSION['prenom']  = $user['prenom'];
      $_SESSION['role']    = $user['role'];

      // Redirection selon le rôle
      if ($user['role'] === 'admin') {
        header('Location: /glowy_shop/admin/index.php');
      } else {
        header('Location: /glowy_shop/pages/index.php');
      }
      exit;
    } else {
      $erreur = 'Identifiants incorrects.';
    }
  }
}
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main>
  <div class="auth-container">
    <h1>🌸 Connexion</h1>
    <p class="auth-subtitle">Bon retour sur GlowShop !</p>

    <?php if ($erreur): ?>
      <div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label for="mot_de_passe">Mot de passe</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" required>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%">Se connecter</button>
    </form>

    <div class="auth-footer">
      Pas encore de compte ? <a href="/glowy_shop/auth/register.php">S'inscrire</a>
    </div>
  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>