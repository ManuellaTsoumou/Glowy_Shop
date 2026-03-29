<?php
session_start();
require_once __DIR__ . '/../includes/admin_check.php';
require_once __DIR__ . '/../config/db.php';

$message = '';
$erreur  = '';

// SUPPRIMER un produit
if (isset($_GET['delete'])) {
  $id = (int) $_GET['delete'];
  // Vérifier si le produit a des commandes
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM order_items WHERE product_id = ?");
  $stmt->execute([$id]);
  if ($stmt->fetchColumn() > 0) {
    $erreur = 'Impossible de supprimer ce produit : il est lié à des commandes.';
  } else {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $message = 'Produit supprimé avec succès.';
  }
}

// ACTIVER / DÉSACTIVER un produit
if (isset($_GET['toggle'])) {
  $id   = (int) $_GET['toggle'];
  $stmt = $pdo->prepare("UPDATE products SET is_active = NOT is_active WHERE id = ?");
  $stmt->execute([$id]);
  $message = 'Statut du produit mis à jour.';
}

// AJOUTER ou MODIFIER un produit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id          = (int) ($_POST['id'] ?? 0);
  $nom         = trim($_POST['nom'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $prix        = (float) ($_POST['prix'] ?? 0);
  $stock       = (int) ($_POST['stock'] ?? 0);
  $marque      = trim($_POST['marque'] ?? '');
  $image_url   = trim($_POST['image_url'] ?? '');
  $categorie   = (int) ($_POST['categorie_id'] ?? 0);
  $is_vegan    = isset($_POST['is_vegan']) ? 1 : 0;
  $skin_types  = json_encode($_POST['skin_types'] ?? []);
  $concerns    = json_encode($_POST['concerns'] ?? []);

  if (empty($nom) || empty($description) || $prix <= 0 || empty($marque) || empty($image_url)) {
    $erreur = 'Tous les champs obligatoires doivent être remplis.';
  } elseif ($id > 0) {
    // Modification
    $stmt = $pdo->prepare("UPDATE products SET nom=?, description=?, prix=?, stock=?, 
                           marque=?, image_url=?, categorie_id=?, is_vegan=?, 
                           skin_types=?, concerns=? WHERE id=?");
    $stmt->execute([$nom, $description, $prix, $stock, $marque, $image_url,
                    $categorie, $is_vegan, $skin_types, $concerns, $id]);
    $message = 'Produit modifié avec succès.';
  } else {
    // Ajout
    $stmt = $pdo->prepare("INSERT INTO products 
                           (nom, description, prix, stock, marque, image_url, 
                            categorie_id, is_vegan, skin_types, concerns) 
                           VALUES (?,?,?,?,?,?,?,?,?,?)");
    $stmt->execute([$nom, $description, $prix, $stock, $marque, $image_url,
                    $categorie, $is_vegan, $skin_types, $concerns]);
    $message = 'Produit ajouté avec succès.';
  }
}

// Récupérer tous les produits
$produits   = $pdo->query("SELECT p.*, c.nom as categorie_nom 
                           FROM products p 
                           LEFT JOIN categories c ON p.categorie_id = c.id 
                           ORDER BY p.created_at DESC")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

// Produit à modifier
$edit = null;
if (isset($_GET['edit'])) {
  $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
  $stmt->execute([(int) $_GET['edit']]);
  $edit = $stmt->fetch();
}
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>

<main>
  <div class="admin-layout">

    <!-- SIDEBAR -->
    <aside class="admin-sidebar">
      <p style="color:#888; font-size:0.8rem; text-transform:uppercase; margin-bottom:1rem; padding:0 1rem;">
        Administration
      </p>
      <a href="/glowy_shop/admin/index.php">📊 Dashboard</a>
      <a href="/glowy_shop/admin/produits.php" class="active">📦 Produits</a>
      <a href="/glowy_shop/admin/commandes.php">🛒 Commandes</a>
      <a href="/glowy_shop/pages/index.php" style="margin-top:2rem;">← Retour au site</a>
      <a href="/glowy_shop/auth/logout.php">🚪 Déconnexion</a>
    </aside>

    <!-- CONTENU -->
    <div class="admin-content">
      <h1 style="margin-bottom:1.5rem;">Gestion des produits 📦</h1>

      <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
      <?php endif; ?>
      <?php if ($erreur): ?>
        <div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div>
      <?php endif; ?>

      <!-- FORMULAIRE AJOUT / MODIFICATION -->
      <div style="background:var(--blanc); border-radius:12px; box-shadow:var(--shadow); padding:1.5rem; margin-bottom:2rem;">
        <h2 style="margin-bottom:1rem;">
          <?= $edit ? '✏️ Modifier le produit' : '➕ Ajouter un produit' ?>
        </h2>
        <form method="POST" action="">
          <?php if ($edit): ?>
            <input type="hidden" name="id" value="<?= $edit['id'] ?>">
          <?php endif; ?>

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
            <div class="form-group">
              <label>Nom *</label>
              <input type="text" name="nom" value="<?= htmlspecialchars($edit['nom'] ?? '') ?>" required>
            </div>
            <div class="form-group">
              <label>Marque *</label>
              <input type="text" name="marque" value="<?= htmlspecialchars($edit['marque'] ?? '') ?>" required>
            </div>
            <div class="form-group">
              <label>Prix (€) *</label>
              <input type="number" name="prix" step="0.01" min="0"
                     value="<?= $edit['prix'] ?? '' ?>" required>
            </div>
            <div class="form-group">
              <label>Stock</label>
              <input type="number" name="stock" min="0" value="<?= $edit['stock'] ?? 0 ?>">
            </div>
            <div class="form-group">
              <label>Catégorie</label>
              <select name="categorie_id">
                <?php foreach ($categories as $cat): ?>
                  <option value="<?= $cat['id'] ?>"
                    <?= ($edit['categorie_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['nom']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>URL image *</label>
              <input type="text" name="image_url"
                     value="<?= htmlspecialchars($edit['image_url'] ?? '') ?>"
                     placeholder="assets/images/produit.jpg" required>
            </div>
          </div>

          <div class="form-group">
            <label>Description *</label>
            <textarea name="description" required><?= htmlspecialchars($edit['description'] ?? '') ?></textarea>
          </div>

          <!-- TYPES DE PEAU -->
          <div class="form-group">
            <label>Types de peau ciblés</label>
            <div style="display:flex; gap:1rem; flex-wrap:wrap;">
              <?php foreach (['seche','grasse','mixte','normale','sensible'] as $skin): ?>
                <?php
                  $skin_types_edit = json_decode($edit['skin_types'] ?? '[]', true) ?? [];
                  $checked = in_array($skin, $skin_types_edit) ? 'checked' : '';
                ?>
                <label style="font-weight:normal; display:flex; align-items:center; gap:0.3rem;">
                  <input type="checkbox" name="skin_types[]" value="<?= $skin ?>" <?= $checked ?>>
                  <?= ucfirst($skin) ?>
                </label>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- PRÉOCCUPATIONS -->
          <div class="form-group">
            <label>Préoccupations ciblées</label>
            <div style="display:flex; gap:1rem; flex-wrap:wrap;">
              <?php foreach (['acne','rides','taches','pores','eclat','hydratation'] as $concern): ?>
                <?php
                  $concerns_edit = json_decode($edit['concerns'] ?? '[]', true) ?? [];
                  $checked = in_array($concern, $concerns_edit) ? 'checked' : '';
                ?>
                <label style="font-weight:normal; display:flex; align-items:center; gap:0.3rem;">
                  <input type="checkbox" name="concerns[]" value="<?= $concern ?>" <?= $checked ?>>
                  <?= ucfirst($concern) ?>
                </label>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="form-group">
            <label style="display:flex; align-items:center; gap:0.5rem; font-weight:normal;">
              <input type="checkbox" name="is_vegan" <?= ($edit['is_vegan'] ?? 0) ? 'checked' : '' ?>>
              🌿 Produit vegan
            </label>
          </div>

          <div style="display:flex; gap:1rem;">
            <button type="submit" class="btn btn-primary">
              <?= $edit ? '✏️ Modifier' : '➕ Ajouter' ?>
            </button>
            <?php if ($edit): ?>
              <a href="/glowy_shop/admin/produits.php" class="btn btn-outline">Annuler</a>
            <?php endif; ?>
          </div>
        </form>
      </div>

      <!-- LISTE DES PRODUITS -->
      <h2 style="margin-bottom:1rem;">Liste des produits (<?= count($produits) ?>)</h2>
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Nom</th>
              <th>Marque</th>
              <th>Prix</th>
              <th>Stock</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($produits as $p): ?>
              <tr>
                <td>#<?= $p['id'] ?></td>
                <td><?= htmlspecialchars($p['nom']) ?></td>
                <td><?= htmlspecialchars($p['marque']) ?></td>
                <td><?= number_format($p['prix'], 2) ?> €</td>
                <td><?= $p['stock'] ?></td>
                <td>
                  <span class="badge <?= $p['is_active'] ? 'badge-vegan' : '' ?>"
                        style="<?= !$p['is_active'] ? 'background:#ffebee; color:var(--erreur)' : '' ?>">
                    <?= $p['is_active'] ? 'Actif' : 'Inactif' ?>
                  </span>
                </td>
                <td style="display:flex; gap:0.5rem;">
                  <a href="?edit=<?= $p['id'] ?>" class="btn btn-secondary"
                     style="padding:0.3rem 0.7rem; font-size:0.8rem;">✏️</a>
                  <a href="?toggle=<?= $p['id'] ?>" class="btn btn-outline"
                     style="padding:0.3rem 0.7rem; font-size:0.8rem;">
                    <?= $p['is_active'] ? '⏸️' : '▶️' ?>
                  </a>
                  <a href="?delete=<?= $p['id'] ?>" class="btn btn-outline"
                     style="padding:0.3rem 0.7rem; font-size:0.8rem; color:var(--erreur); border-color:var(--erreur);"
                     onclick="return confirm('Supprimer ce produit ?')">🗑️</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>