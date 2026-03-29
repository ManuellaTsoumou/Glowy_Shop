<?php
session_start();
require_once __DIR__ . '/../includes/auth_check.php';
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main>
  <section class="section">
    <div class="container">

      <div style="text-align:center; margin-bottom:2rem;">
        <h1 style="color:var(--rose-profond);">
          Bonjour <?= htmlspecialchars($_SESSION['prenom']) ?> ! 🌸
        </h1>
        <p style="color:#888; font-size:1.1rem;">
          Voici vos recommandations personnalisées
        </p>
        <a href="/glowy_shop/pages/quiz.php" class="btn btn-outline" style="margin-top:1rem;">
          🔄 Refaire le quiz
        </a>
      </div>

      <!-- GRILLE DES RECOMMANDATIONS -->
      <div class="products-grid" id="recos-grid">
        <div class="alert alert-info">Chargement de vos recommandations...</div>
      </div>

    </div>
  </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<script src="/glowy_shop/assets/js/panier.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const recos = JSON.parse(sessionStorage.getItem('glowshop_recos') || '[]');
  const grid  = document.getElementById('recos-grid');

  if (!recos || recos.length === 0) {
    grid.innerHTML = '<div class="alert alert-info">Aucune recommandation trouvée. <a href="/glowy_shop/pages/quiz.php">Refaire le quiz</a></div>';
    return;
  }

  grid.innerHTML = recos.map((p, i) => `
    <div class="product-card">
      <img class="product-card-img"
           src="/glowy_shop/${p.image_url}"
           alt="${p.nom}">
      <div class="product-card-body">
        <span class="product-card-marque">${p.marque}</span>
        <span class="product-card-nom">${p.nom}</span>
        <div class="stars">
          ${'★'.repeat(Math.round(p.note_moyenne))}${'☆'.repeat(5 - Math.round(p.note_moyenne))}
          <span class="stars-count">(${p.nb_avis})</span>
        </div>
        <span class="product-card-prix">${parseFloat(p.prix).toFixed(2)} €</span>
        ${p.is_vegan == 1 ? '<span class="badge badge-vegan">🌿 Vegan</span>' : ''}
        <span style="background:var(--rose-pale); color:var(--rose-profond); padding:0.2rem 0.6rem; border-radius:20px; font-size:0.75rem; font-weight:600;">
          Score : ${p.score}
        </span>
      </div>
      <div class="product-card-footer">
        <a href="/glowy_shop/pages/produit.php?id=${p.id}" class="btn btn-secondary">Voir</a>
        <button class="btn btn-primary" onclick="ajouterAuPanier(${p.id}, '${p.nom.replace(/'/g, "\\'")}', ${p.prix}, '${p.image_url}')">
          🛒
        </button>
      </div>
    </div>
  `).join('');
});
</script>