<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Récupérer les catégories pour les filtres
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main>
  <section class="section">
    <div class="container">
      <h1 style="margin-bottom:1.5rem">Catalogue 🌸</h1>

      <!-- FILTRES -->
      <div style="display:flex; gap:1rem; flex-wrap:wrap; margin-bottom:2rem; align-items:center;">
        <select id="filtre-categorie" class="form-group" style="margin:0; width:auto;">
          <option value="">Toutes les catégories</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nom']) ?></option>
          <?php endforeach; ?>
        </select>

        <select id="filtre-peau" style="padding:0.7rem 1rem; border:2px solid #e0e0e0; border-radius:6px; font-size:1rem;">
          <option value="">Tous les types de peau</option>
          <option value="seche">Sèche</option>
          <option value="grasse">Grasse</option>
          <option value="mixte">Mixte</option>
          <option value="normale">Normale</option>
          <option value="sensible">Sensible</option>
        </select>

        <input type="number" id="filtre-prix" placeholder="Prix max (€)"
               style="padding:0.7rem 1rem; border:2px solid #e0e0e0; border-radius:6px; font-size:1rem; width:160px;">

        <button onclick="filtrerProduits()" class="btn btn-primary">Filtrer</button>
        <button onclick="resetFiltres()" class="btn btn-outline">Réinitialiser</button>
      </div>

      <!-- GRILLE PRODUITS -->
      <div id="products-container">
        <div class="products-grid" id="products-grid">
          <p>Chargement des produits...</p>
        </div>
        <div id="no-results" style="display:none;">
          <div class="alert alert-info">Aucun produit ne correspond à vos critères.</div>
        </div>
      </div>

    </div>
  </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<script src="/glowy_shop/assets/js/catalogue.js"></script>