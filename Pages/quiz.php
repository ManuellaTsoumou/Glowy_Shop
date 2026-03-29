<?php
session_start();
require_once __DIR__ . '/../includes/auth_check.php';
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main>
  <div class="quiz-container">
    <h1 style="color:var(--rose-profond); text-align:center; margin-bottom:0.5rem;">Quiz beauté ✨</h1>
    <p style="text-align:center; color:#888; margin-bottom:1.5rem;">5 questions pour trouver vos produits idéaux</p>

    <!-- BARRE DE PROGRESSION -->
    <div class="quiz-progress">
      <div class="quiz-progress-bar" id="progress-bar" style="width:20%"></div>
    </div>
    <p style="text-align:center; color:#888; font-size:0.9rem; margin-bottom:2rem;">
      Étape <span id="etape-actuelle">1</span> sur 5
    </p>

    <!-- ÉTAPE 1 : TYPE DE PEAU -->
    <div class="quiz-step active" id="step-1">
      <h2 style="text-align:center; margin-bottom:1.5rem;">Quel est votre type de peau ?</h2>
      <div class="quiz-options">
        <div class="quiz-option" onclick="selectOption(this, 'skin_type', 'seche')">🌵 Sèche</div>
        <div class="quiz-option" onclick="selectOption(this, 'skin_type', 'grasse')">💧 Grasse</div>
        <div class="quiz-option" onclick="selectOption(this, 'skin_type', 'mixte')">🌊 Mixte</div>
        <div class="quiz-option" onclick="selectOption(this, 'skin_type', 'normale')">✨ Normale</div>
        <div class="quiz-option" onclick="selectOption(this, 'skin_type', 'sensible')">🌸 Sensible</div>
      </div>
      <div style="text-align:right;">
        <button class="btn btn-primary" onclick="nextStep(2)" id="btn-step-1" disabled>
          Continuer →
        </button>
      </div>
    </div>

    <!-- ÉTAPE 2 : CARNATION -->
    <div class="quiz-step" id="step-2">
      <h2 style="text-align:center; margin-bottom:1.5rem;">Quelle est votre carnation ?</h2>
      <div class="quiz-options">
        <div class="quiz-option" onclick="selectOption(this, 'skin_tone', 'claire')">🤍 Claire</div>
        <div class="quiz-option" onclick="selectOption(this, 'skin_tone', 'medium')">🌿 Medium</div>
        <div class="quiz-option" onclick="selectOption(this, 'skin_tone', 'mate')">🌻 Mate</div>
        <div class="quiz-option" onclick="selectOption(this, 'skin_tone', 'foncee')">🌙 Foncée</div>
      </div>
      <div style="display:flex; justify-content:space-between;">
        <button class="btn btn-outline" onclick="prevStep(1)">← Retour</button>
        <button class="btn btn-primary" onclick="nextStep(3)" id="btn-step-2" disabled>Continuer →</button>
      </div>
    </div>

    <!-- ÉTAPE 3 : PRÉOCCUPATIONS -->
    <div class="quiz-step" id="step-3">
      <h2 style="text-align:center; margin-bottom:0.5rem;">Quelles sont vos préoccupations ?</h2>
      <p style="text-align:center; color:#888; margin-bottom:1.5rem;">Plusieurs choix possibles</p>
      <div class="quiz-options">
        <div class="quiz-option" onclick="selectMultiple(this, 'concerns', 'acne')">😤 Acné</div>
        <div class="quiz-option" onclick="selectMultiple(this, 'concerns', 'rides')">⏳ Rides</div>
        <div class="quiz-option" onclick="selectMultiple(this, 'concerns', 'taches')">🌑 Taches</div>
        <div class="quiz-option" onclick="selectMultiple(this, 'concerns', 'pores')">🔍 Pores</div>
        <div class="quiz-option" onclick="selectMultiple(this, 'concerns', 'eclat')">✨ Éclat</div>
        <div class="quiz-option" onclick="selectMultiple(this, 'concerns', 'hydratation')">💦 Hydratation</div>
      </div>
      <div style="display:flex; justify-content:space-between;">
        <button class="btn btn-outline" onclick="prevStep(2)">← Retour</button>
        <button class="btn btn-primary" onclick="nextStep(4)">Continuer →</button>
      </div>
    </div>

    <!-- ÉTAPE 4 : PRÉFÉRENCES -->
    <div class="quiz-step" id="step-4">
      <h2 style="text-align:center; margin-bottom:0.5rem;">Quelles sont vos préférences ?</h2>
      <p style="text-align:center; color:#888; margin-bottom:1.5rem;">Plusieurs choix possibles</p>
      <div class="quiz-options">
        <div class="quiz-option" onclick="selectMultiple(this, 'preferences', 'vegan')">🌿 Vegan</div>
        <div class="quiz-option" onclick="selectMultiple(this, 'preferences', 'cruelty_free')">🐰 Cruelty-free</div>
        <div class="quiz-option" onclick="selectMultiple(this, 'preferences', 'sans_paraben')">🚫 Sans paraben</div>
        <div class="quiz-option" onclick="selectMultiple(this, 'preferences', 'naturel')">🍃 Naturel</div>
      </div>
      <div style="display:flex; justify-content:space-between;">
        <button class="btn btn-outline" onclick="prevStep(3)">← Retour</button>
        <button class="btn btn-primary" onclick="nextStep(5)">Continuer →</button>
      </div>
    </div>

    <!-- ÉTAPE 5 : BUDGET -->
    <div class="quiz-step" id="step-5">
      <h2 style="text-align:center; margin-bottom:1.5rem;">Quel est votre budget mensuel beauté ?</h2>
      <div class="quiz-options" style="grid-template-columns:1fr;">
        <div class="quiz-option" onclick="selectOption(this, 'budget_range', 'low')">💰 Moins de 30€</div>
        <div class="quiz-option" onclick="selectOption(this, 'budget_range', 'medium')">💰💰 30€ - 70€</div>
        <div class="quiz-option" onclick="selectOption(this, 'budget_range', 'high')">💰💰💰 Plus de 70€</div>
      </div>
      <div style="display:flex; justify-content:space-between;">
        <button class="btn btn-outline" onclick="prevStep(4)">← Retour</button>
        <button class="btn btn-primary" onclick="soumettreQuiz()" id="btn-step-5" disabled>
          Voir mes recommandations 🌸
        </button>
      </div>
    </div>

    <!-- CHARGEMENT -->
    <div class="quiz-step" id="step-loading">
      <div style="text-align:center; padding:2rem;">
        <p style="font-size:1.5rem;">🌸</p>
        <p style="color:#888;">Calcul de vos recommandations...</p>
      </div>
    </div>

  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<script src="/glowy_shop/assets/js/quiz.js"></script>