// panier.js — Gestion du panier via localStorage

// Récupérer le panier depuis localStorage
function getPanier() {
  return JSON.parse(localStorage.getItem('glowshop_panier')) || [];
}

// Sauvegarder le panier
function savePanier(panier) {
  localStorage.setItem('glowshop_panier', JSON.stringify(panier));
  updatePanierCount();
}

// Ajouter un produit au panier
function ajouterAuPanier(productId, nom, prix, image) {
  const panier = getPanier();
  const index  = panier.findIndex(p => p.id === productId);

  if (index !== -1) {
    panier[index].quantite += 1;
  } else {
    panier.push({ id: productId, nom, prix, image, quantite: 1 });
  }

  savePanier(panier);
  showNotification('🛒 Produit ajouté au panier !');
}

// Retirer un produit du panier
function retirerDuPanier(productId) {
  const panier = getPanier().filter(p => p.id !== productId);
  savePanier(panier);
}

// Vider le panier
function viderPanier() {
  localStorage.removeItem('glowshop_panier');
  updatePanierCount();
}

// Mettre à jour le compteur dans la navbar
function updatePanierCount() {
  const panier = getPanier();
  const total  = panier.reduce((acc, p) => acc + p.quantite, 0);
  const badge  = document.getElementById('panier-count');
  if (badge) {
    badge.textContent = total;
    badge.style.display = total > 0 ? 'inline' : 'none';
  }
}

// Notification toast
function showNotification(message) {
  const notif = document.createElement('div');
  notif.textContent = message;
  notif.style.cssText = `
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    background: var(--rose-profond);
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    z-index: 9999;
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    animation: slideIn 0.3s ease;
  `;
  document.body.appendChild(notif);
  setTimeout(() => notif.remove(), 3000);
}

// Initialisation au chargement
document.addEventListener('DOMContentLoaded', updatePanierCount);