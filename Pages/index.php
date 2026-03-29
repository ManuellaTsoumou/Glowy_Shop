<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Récupérer 4 produits tendances
$produits = $pdo->query("SELECT * FROM products WHERE is_active = 1 ORDER BY note_moyenne DESC LIMIT 4")->fetchAll();

// Récupérer les catégories
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main>

  <!-- HERO -->
  <section class="hero">
    <h1>Découvrez vos produits idéaux ✨</h1>
    <p>Répondez à notre quiz beauté et recevez des recommandations personnalisées selon votre type de peau.</p>
    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="/glowy_shop/pages/quiz.php" class="btn btn-primary">Refaire le quiz</a>
    <?php else: ?>
      <a href="/glowy_shop/auth/register.php" class="btn btn-primary">Faire le quiz beauté</a>
    <?php endif; ?>
    <a href="/glowy_shop/pages/catalogue.php" class="btn btn-outline" style="margin-left:1rem">Voir le catalogue</a>
  </section>

  <!-- CATÉGORIES -->
  <?php if (!empty($categories)): ?>
  <section class="section">
    <div class="container">
      <h2 style="margin-bottom:1.5rem">Nos catégories</h2>
      <div style="display:flex; gap:1rem; flex-wrap:wrap;">
        <?php foreach ($categories as $cat): ?>
          <a href="/glowy_shop/pages/catalogue.php?categorie=<?= $cat['id'] ?>"
             class="btn btn-secondary">
            <?= htmlspecialchars($cat['nom']) ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- PRODUITS TENDANCES -->
  <section class="section section-alt">
    <div class="container">
      <h2 style="margin-bottom:1.5rem">Produits tendances 🌸</h2>
      <?php if (!empty($produits)): ?>
        <div class="products-grid">
          <?php foreach ($produits as $p): ?>
            <div class="product-card">
              <img class="product-card-img"
                   src="/glowy_shop/<?= htmlspecialchars($p['image_url']) ?>"
                   alt="<?= htmlspecialchars($p['nom']) ?>">
              <div class="product-card-body">
                <span class="product-card-marque"><?= htmlspecialchars($p['marque']) ?></span>
                <span class="product-card-nom"><?= htmlspecialchars($p['nom']) ?></span>
                <div class="stars">
                  <?= str_repeat('★', round($p['note_moyenne'])) ?>
                  <?= str_repeat('☆', 5 - round($p['note_moyenne'])) ?>
                  <span class="stars-count">(<?= $p['nb_avis'] ?>)</span>
                </div>
                <span class="product-card-prix"><?= number_format($p['prix'], 2) ?> €</span>
                <?php if ($p['is_vegan']): ?>
                  <span class="badge badge-vegan">Vegan</span>
                <?php endif; ?>
              </div>
              <div class="product-card-footer">
                <a href="/glowy_shop/pages/produit.php?id=<?= $p['id'] ?>"
                   class="btn btn-secondary">Voir</a>
                <a href="/glowy_shop/pages/catalogue.php"
                   class="btn btn-primary">Catalogue</a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="alert alert-info">Aucun produit disponible pour le moment.</div>
      <?php endif; ?>
    </div>
  </section>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>