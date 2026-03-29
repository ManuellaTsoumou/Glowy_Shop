<?php
session_start();
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

// Récupérer les infos de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Récupérer le profil beauté
$stmt = $pdo->prepare("SELECT * FROM beauty_profiles WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$profil = $stmt->fetch();

// Récupérer les favoris
$stmt = $pdo->prepare("SELECT p.* FROM products p 
                       JOIN favoris f ON p.id = f.product_id 
                       WHERE f.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$favoris = $stmt->fetchAll();

// Récupérer les commandes
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$commandes = $stmt->fetchAll();
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main>
  <section class="section">
    <div class="container">

      <h1 style="color:var(--rose-profond); margin-bottom:2rem;">
        Mon profil 🌸
      </h1>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:2rem;">

        <!-- INFOS PERSONNELLES -->
        <div style="background:var(--blanc); border-radius:12px; box-shadow:var(--shadow); padding:1.5rem;">
          <h2 style="margin-bottom:1rem;">Mes informations</h2>
          <p><strong>Prénom :</strong> <?= htmlspecialchars($user['prenom']) ?></p>
          <p><strong>Nom :</strong> <?= htmlspecialchars($user['nom']) ?></p>
          <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>
          <p><strong>Membre depuis :</strong> <?= date('d/m/Y', strtotime($user['created_at'])) ?></p>
          <div style="margin-top:1rem;">
            <a href="/glowy_shop/pages/quiz.php" class="btn btn-outline">
              🔄 Refaire le quiz
            </a>
          </div>
        </div>

        <!-- PROFIL BEAUTÉ -->
        <div style="background:var(--blanc); border-radius:12px; box-shadow:var(--shadow); padding:1.5rem;">
          <h2 style="margin-bottom:1rem;">Mon profil beauté</h2>
          <?php if ($profil): ?>
            <p><strong>Type de peau :</strong> <?= htmlspecialchars($profil['skin_type']) ?></p>
            <p><strong>Carnation :</strong> <?= htmlspecialchars($profil['skin_tone']) ?></p>
            <p><strong>Préoccupations :</strong>
              <?= implode(', ', json_decode($profil['concerns'], true) ?? []) ?>
            </p>
            <p><strong>Préférences :</strong>
              <?= implode(', ', json_decode($profil['preferences'], true) ?? []) ?>
            </p>
            <p><strong>Budget :</strong> <?= htmlspecialchars($profil['budget_range']) ?></p>
          <?php else: ?>
            <div class="alert alert-info">
              Vous n'avez pas encore de profil beauté.
              <a href="/glowy_shop/pages/quiz.php">Faire le quiz</a>
            </div>
          <?php endif; ?>
        </div>

      </div>

      <!-- FAVORIS -->
      <div style="margin-top:2rem;">
        <h2 style="margin-bottom:1.5rem;">Mes favoris 🤍</h2>
        <?php if (!empty($favoris)): ?>
          <div class="products-grid">
            <?php foreach ($favoris as $p): ?>
              <div class="product-card">
                <img class="product-card-img"
                     src="/glowy_shop/<?= htmlspecialchars($p['image_url']) ?>"
                     alt="<?= htmlspecialchars($p['nom']) ?>">
                <div class="product-card-body">
                  <span class="product-card-marque"><?= htmlspecialchars($p['marque']) ?></span>
                  <span class="product-card-nom"><?= htmlspecialchars($p['nom']) ?></span>
                  <span class="product-card-prix"><?= number_format($p['prix'], 2) ?> €</span>
                </div>
                <div class="product-card-footer">
                  <a href="/glowy_shop/pages/produit.php?id=<?= $p['id'] ?>"
                     class="btn btn-secondary">Voir</a>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="alert alert-info">Aucun favori pour le moment. 🌸</div>
        <?php endif; ?>
      </div>

      <!-- COMMANDES -->
      <div style="margin-top:2rem;">
        <h2 style="margin-bottom:1.5rem;">Mes commandes 📦</h2>
        <?php if (!empty($commandes)): ?>
          <div class="table-container">
            <table>
              <thead>
                <tr>
                  <th>#</th>
                  <th>Date</th>
                  <th>Total</th>
                  <th>Statut</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($commandes as $c): ?>
                  <tr>
                    <td>#<?= $c['id'] ?></td>
                    <td><?= date('d/m/Y', strtotime($c['created_at'])) ?></td>
                    <td><?= number_format($c['total_ttc'], 2) ?> €</td>
                    <td>
                      <span class="badge" style="background:var(--rose-pale); color:var(--rose-profond);">
                        <?= htmlspecialchars($c['statut']) ?>
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="alert alert-info">Aucune commande pour le moment. 🌸</div>
        <?php endif; ?>
      </div>

    </div>
  </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>