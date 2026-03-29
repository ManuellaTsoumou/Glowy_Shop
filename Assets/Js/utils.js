// utils.js — Fonctions utilitaires communes

// Notification toast (utilisée dans panier.js et ailleurs)
function showNotification(message, type = 'success') {
  const notif = document.createElement('div');
  notif.textContent = message;
  notif.style.cssText = `
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    background: ${type === 'success' ? 'var(--rose-profond)' : 'var(--erreur)'};
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    z-index: 9999;
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
  `;
  document.body.appendChild(notif);
  setTimeout(() => notif.remove(), 3000);
}

// Formater un prix
function formatPrix(prix) {
  return parseFloat(prix).toFixed(2) + ' €';
}

// Formater une date
function formatDate(dateStr) {
  return new Date(dateStr).toLocaleDateString('fr-FR');
}