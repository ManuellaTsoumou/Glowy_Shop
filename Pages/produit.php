<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Récupérer l'id produit
$id = (int) ($_GET['id'] ?? 0);
if (!$id) {
  header('Location: /glowy_shop/pages/catalogue.php');
  exit;
}

// Récupérer le produit
$stmt = $pdo->prepare("SELECT p.*, c.nom as categorie_nom 
                       FROM products p 
                       LEFT JOIN categories c ON p.categorie_id = c.id 
                       WHERE p.id = ? AND p.is_active = 1");
$stmt->execute([$id]);
$produit = $stmt->fetch();

if (!$produit) {
  header('Location: /glowy_shop/pages/catalogue.php');
  exit;
}

// Récupérer les avis
$stmt = $pdo->prepare("SELECT r.*, u.prenom, u.nom 
                       FROM reviews r 
                       JOIN users u ON r.user_id = u.id 
                       WHERE r.product_id = ? AND r.is_approved = 1 
                       ORDER BY r.created_at DESC");
$stmt->execute([$id]);
$avis = $stmt->fetchAll();

// Récupérer produits similaires
$stmt = $pdo->prepare("SELECT * FROM products 
                       WHERE categorie_id = ? AND id != ? AND is_active = 1 
                       LIMIT 4");
$stmt->execute([$produit['categorie_id'], $id]);
$similaires = $stmt->fetchAll();

$skin_types = json_decode($produit['skin_types'], true) ?? [];
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main>
  <section class="section">
    <div class="container">

      <!-- BREADCRUMB -->
      <p style="margin-bottom:1.5rem; font-size:0.9rem; color:#888;">
        <a href="/glowy_shop/pages/catalogue.php">Catalogue</a> &rsaquo;
        <?= htmlspecialchars($produit['categorie_nom']) ?> &rsaquo;
        <?= htmlspecialchars($produit['nom']) ?>
      </p>

      <!-- FICHE PRODUIT -->
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:3rem; margin-bottom:3rem;">

        <!-- IMAGE -->
        <div>
          <img src="/glowy_shop/<?= htmlspecialchars($produit['image_url']) ?>"
               alt="<?= htmlspecialchars($produit['nom']) ?>"
               style="width:100%; border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,0.1);">
        </div>

        <!-- INFOS -->
        <div style="display:flex; flex-direction:column; gap:1rem;">
          <span style="color:#888; font-size:0.85rem; text-transform:uppercase;">
            <?= htmlspecialchars($produit['marque']) ?>
          </span>
          <h1 style="font-size:1.8rem;"><?= htmlspecialchars($produit['nom']) ?></h1>

          <div class="stars" style="font-size:1.2rem;">
            <?= str_repeat('★', round($produit['note_moyenne'])) ?>
            <?= str_repeat('☆', 5 - round($produit['note_moyenne'])) ?>
            <span class="stars-count"><?= $produit['nb_avis'] ?> avis</span>
          </div>

          <p style="font-size:1.8rem; font-weight:700; color:var(--rose-profond);">
            <?= number_format($produit['prix'], 2) ?> €
          </p>

          <!-- BADGES -->
          <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
            <?php if ($produit['is_vegan']): ?>
              <span class="badge badge-vegan">🌿 Vegan</span>
            <?php endif; ?>
            <?php foreach ($skin_types as $skin): ?>
              <span class="badge badge-skin"><?= htmlspecialchars($skin) ?></span>
            <?php endforeach; ?>
          </div>

          <p style="color:#555; line-height:1.7;">
            <?= htmlspecialchars($produit['description']) ?>
          </p>

          <!-- STOCK -->
          <?php if ($produit['stock'] > 0): ?>
            <p style="color:var(--succes); font-weight:600;">✅ En stock (<?= $produit['stock'] ?> disponibles)</p>
          <?php else: ?>
            <p style="color:var(--erreur); font-weight:600;">❌ Rupture de stock</p>
          <?php endif; ?>

          <!-- BOUTONS -->
          <div style="display:flex; gap:1rem; margin-top:0.5rem;">
            <button class="btn btn-primary" onclick="ajouterAuPanier(
  <?= $produit['id'] ?>,
  '<?= addslashes($produit['nom']) ?>',
  <?= $produit['prix'] ?>,
  '<?= addslashes($produit['image_url']) ?>'
)">
                🛒 Ajouter au panier
              </button>
            <?php else: ?>
              <button class="btn btn-primary" disabled>Rupture de stock</button>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
              <button class="btn btn-outline" onclick="toggleFavori(<?= $produit['id'] ?>)">
                🤍 Favoris
              </button>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- AVIS CLIENTS -->
      <div style="margin-bottom:3rem;">
        <h2 style="margin-bottom:1.5rem;">Avis clients (<?= count($avis) ?>)</h2>

        <?php if (isset($_SESSION['user_id'])): ?>
          <div style="background:var(--rose-pale); padding:1.5rem; border-radius:12px; margin-bottom:1.5rem;">
            <h3 style="margin-bottom:1rem;">Laisser un avis</h3>
            <div id="avis-message"></div>
            <div class="form-group">
              <label>Note</label>
              <select id="avis-note">
                <option value="5">★★★★★</option>
                <option value="4">★★★★☆</option>
                <option value="3">★★★☆☆</option>
                <option value="2">★★☆☆☆</option>
                <option value="1">★☆☆☆☆</option>
              </select>
            </div>
            <div class="form-group">
              <label>Titre</label>
              <input type="text" id="avis-titre" placeholder="Résumez votre avis">
            </div>
            <div class="form-group">
              <label>Commentaire</label>
              <textarea id="avis-commentaire" placeholder="Décrivez votre expérience..."></textarea>
            </div>
            <button class="btn btn-primary" onclick="posterAvis(<?= $produit['id'] ?>)">
              Publier mon avis
            </button>
          </div>
        <?php endif; ?>

        <?php if (!empty($avis)): ?>
          <?php foreach ($avis as $a): ?>
            <div style="border-bottom:1px solid var(--rose-pale); padding:1.2rem 0;">
              <div style="display:flex; justify-content:space-between; margin-bottom:0.4rem;">
                <strong><?= htmlspecialchars($a['prenom'] . ' ' . $a['nom'][0] . '.') ?></strong>
                <span style="color:#888; font-size:0.85rem;">
                  <?= date('d/m/Y', strtotime($a['created_at'])) ?>
                </span>
              </div>
              <div class="stars">
                <?= str_repeat('★', $a['note']) ?><?= str_repeat('☆', 5 - $a['note']) ?>
              </div>
              <p style="font-weight:600; margin:0.4rem 0;"><?= htmlspecialchars($a['titre']) ?></p>
              <p style="color:#555;"><?= htmlspecialchars($a['commentaire']) ?></p>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="alert alert-info">Aucun avis pour ce produit. Soyez la première ! 🌸</div>
        <?php endif; ?>
      </div>

      <!-- PRODUITS SIMILAIRES -->
      <?php if (!empty($similaires)): ?>
        <div>
          <h2 style="margin-bottom:1.5rem;">Produits similaires</h2>
          <div class="products-grid">
            <?php foreach ($similaires as $s): ?>
              <div class="product-card">
                <img class="product-card-img"
                     src="/glowy_shop/<?= htmlspecialchars($s['image_url']) ?>"
                     alt="<?= htmlspecialchars($s['nom']) ?>">
                <div class="product-card-body">
                  <span class="product-card-marque"><?= htmlspecialchars($s['marque']) ?></span>
                  <span class="product-card-nom"><?= htmlspecialchars($s['nom']) ?></span>
                  <span class="product-card-prix"><?= number_format($s['prix'], 2) ?> €</span>
                </div>
                <div class="product-card-footer">
                  <a href="/glowy_shop/pages/produit.php?id=<?= $s['id'] ?>"
                     class="btn btn-secondary">Voir</a>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

    </div>
  </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<script src="/glowy_shop/assets/js/panier.js"></script>
<script>
function toggleFavori(productId) {
  fetch('/glowy_shop/api/toggle_favori.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `product_id=${productId}`
  })
  .then(res => res.json())
  .then(data => alert(data.message))
  .catch(err => console.error(err));
}

function posterAvis(productId) {
  const note        = document.getElementById('avis-note').value;
  const titre       = document.getElementById('avis-titre').value;
  const commentaire = document.getElementById('avis-commentaire').value;
  const msgDiv      = document.getElementById('avis-message');

  fetch('/glowy_shop/api/post_avis.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `product_id=${productId}&note=${note}&titre=${encodeURIComponent(titre)}&commentaire=${encodeURIComponent(commentaire)}`
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      msgDiv.innerHTML = '<div class="alert alert-success">Avis publié ! 🌸</div>';
      document.getElementById('avis-titre').value       = '';
      document.getElementById('avis-commentaire').value = '';
    } else {
      msgDiv.innerHTML = `<div class="alert alert-error">${data.error}</div>`;
    }
  });
}
</script>