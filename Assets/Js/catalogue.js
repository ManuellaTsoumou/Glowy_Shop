// catalogue.js — Gestion des filtres AJAX

function filtrerProduits() {
  const categorie = document.getElementById('filtre-categorie').value;
  const peau      = document.getElementById('filtre-peau').value;
  const prixMax   = document.getElementById('filtre-prix').value;

  let url = '/glowy_shop/api/get_produits.php?';
  if (categorie) url += `categorie=${categorie}&`;
  if (peau)      url += `skin=${peau}&`;
  if (prixMax)   url += `prix_max=${prixMax}&`;

  fetch(url)
    .then(res => res.json())
    .then(data => renderProduits(data))
    .catch(err => console.error('Erreur filtres :', err));
}

function resetFiltres() {
  document.getElementById('filtre-categorie').value = '';
  document.getElementById('filtre-peau').value      = '';
  document.getElementById('filtre-prix').value      = '';
  filtrerProduits();
}

function renderProduits(produits) {
  const grid      = document.getElementById('products-grid');
  const noResults = document.getElementById('no-results');

  if (!produits || produits.length === 0) {
    grid.innerHTML = '';
    noResults.style.display = 'block';
    return;
  }

  noResults.style.display = 'none';
  grid.innerHTML = produits.map(p => `
    <div class="product-card">
      <img class="product-card-img"
           src="/glowy_shop/${p.image_url}"
           alt="${p.nom}">
      <div class="product-card-body">
        <span class="product-card-marque">${p.marque}</span>
        <span class="product-card-nom">${p.nom}</span>
        <div class="stars">${renderStars(p.note_moyenne)}
          <span class="stars-count">(${p.nb_avis})</span>
        </div>
        <span class="product-card-prix">${parseFloat(p.prix).toFixed(2)} €</span>
        ${p.is_vegan == 1 ? '<span class="badge badge-vegan">Vegan</span>' : ''}
      </div>
      <div class="product-card-footer">
        <a href="/glowy_shop/pages/produit.php?id=${p.id}" class="btn btn-secondary">Voir</a>
      </div>
    </div>
  `).join('');
}

function renderStars(note) {
  const full  = Math.round(note);
  const empty = 5 - full;
  return '★'.repeat(full) + '☆'.repeat(empty);
}

// Chargement initial au démarrage de la page
document.addEventListener('DOMContentLoaded', filtrerProduits);