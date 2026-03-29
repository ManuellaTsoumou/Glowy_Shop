// quiz.js — Logique des 5 étapes du quiz

const profil = {
  skin_type:    '',
  skin_tone:    '',
  concerns:     [],
  preferences:  [],
  budget_range: ''
};

// Sélection unique (radio)
function selectOption(el, champ, valeur) {
  // Désélectionner les autres options du même groupe
  el.closest('.quiz-options').querySelectorAll('.quiz-option')
    .forEach(o => o.classList.remove('selected'));
  el.classList.add('selected');
  profil[champ] = valeur;

  // Activer le bouton Continuer
  const stepNum = el.closest('.quiz-step').id.split('-')[1];
  const btn = document.getElementById(`btn-step-${stepNum}`);
  if (btn) btn.disabled = false;
}

// Sélection multiple (checkbox)
function selectMultiple(el, champ, valeur) {
  el.classList.toggle('selected');
  if (el.classList.contains('selected')) {
    if (!profil[champ].includes(valeur)) profil[champ].push(valeur);
  } else {
    profil[champ] = profil[champ].filter(v => v !== valeur);
  }
}

// Aller à l'étape suivante
function nextStep(num) {
  document.querySelectorAll('.quiz-step').forEach(s => s.classList.remove('active'));
  document.getElementById(`step-${num}`).classList.add('active');
  document.getElementById('etape-actuelle').textContent = num;
  document.getElementById('progress-bar').style.width = `${num * 20}%`;
}

// Retour à l'étape précédente
function prevStep(num) {
  nextStep(num);
}

// Soumettre le quiz
function soumettreQuiz() {
  nextStep('loading');

  fetch('/glowy_shop/api/post_quiz.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(profil)
  })
  .then(res => res.json())
  .then(data => {
    if (data.recommendations) {
      sessionStorage.setItem('glowshop_recos', JSON.stringify(data.recommendations));
      window.location.href = '/glowy_shop/pages/recommandations.php';
    } else {
      alert('Erreur : ' + (data.error || 'Inconnue'));
    }
  })
  .catch(err => console.error('Erreur quiz :', err));
}