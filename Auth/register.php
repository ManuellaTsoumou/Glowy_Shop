<?php
session_start();

// Si déjà connectée, on redirige
if (isset($_SESSION['user_id'])) {
  header('Location: /glowy_shop/pages/index.php');
  exit;
}

require_once __DIR__ . '/../config/db.php';

$erreur = '';
$succes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nom    = trim($_POST['nom'] ?? '');
  $prenom = trim($_POST['prenom'] ?? '');
  $email  = trim($_POST['email'] ?? '');
  $mdp    = $_POST['mot_de_passe'] ?? '';
  $mdp2   = $_POST['mot_de_passe2'] ?? '';

  // Validations
  if (empty($nom) || empty($prenom) || empty($email) || empty($mdp)) {
    $erreur = 'Tous les champs sont obligatoires.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erreur = 'Adresse email invalide.';
  } elseif (strlen($mdp) < 8 || !preg_match('/[A-Z]/', $mdp) || !preg_match('/[0-9]/', $mdp)) {
    $erreur = 'Le mot de passe doit faire au moins 8 caractères, avec une majuscule et un chiffre.';
  } elseif ($mdp !== $mdp2) {
    $erreur = 'Les mots de passe ne correspondent pas.';
  } else {
    // Vérifier si l'email existe déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
      $erreur = 'Cette adresse email est déjà utilisée.';
    } else {
      // Insertion en BDD
      $hash = password_hash($mdp, PASSWORD_BCRYPT);
      $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, email, mot_de_passe) VALUES (?, ?, ?, ?)");
      $stmt->execute([$nom, $prenom, $email, $hash]);

      // Ouverture de session
      $_SESSION['user_id'] = $pdo->lastInsertId();
      $_SESSION['prenom']  = $prenom;
      $_SESSION['role']    = 'client';

      header('Location: /glowy_shop/pages/quiz.php');
      exit;
    }
  }
}
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main>
  <div class="auth-container">
    <h1>🌸 Créer un compte</h1>
    <p class="auth-subtitle">Rejoins GlowShop et découvre tes produits idéaux</p>

    <?php if ($erreur): ?>
      <div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="form-group">
        <label for="prenom">Prénom</label>
        <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label for="mot_de_passe">Mot de passe</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" required>
      </div>
      <div class="form-group">
        <label for="mot_de_passe2">Confirmer le mot de passe</label>
        <input type="password" id="mot_de_passe2" name="mot_de_passe2" required>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%">Créer mon compte</button>
    </form>

    <div class="auth-footer">
      Déjà un compte ? <a href="/glowy_shop/auth/login.php">Se connecter</a>
    </div>
  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>