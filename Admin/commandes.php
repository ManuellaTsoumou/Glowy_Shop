<?php
session_start();
require_once __DIR__ . '/../includes/admin_check.php';
require_once __DIR__ . '/../config/db.php';

$message = '';

// MODIFIER LE STATUT d'une commande
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id     = (int) ($_POST['id'] ?? 0);
  $statut = $_POST['statut'] ?? '';
  $statuts_valides = ['en_attente', 'confirmee', 'expediee', 'livree', 'annulee'];

  if ($id && in_array($statut, $statuts_valides)) {
    $stmt = $pdo->prepare("UPDATE orders SET statut = ? WHERE id = ?");
    $stmt->execute([$statut, $id]);
    $message = 'Statut mis à jour avec succès.';
  }
}

// Récupérer toutes les commandes
$commandes = $pdo->query("SELECT o.*, u.prenom, u.nom, u.email
                          FROM orders o
                          JOIN users u ON o.user_id = u.id
                          ORDER BY o.created_at DESC")->fetchAll();
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
      <a href="/glowy_shop/admin/produits.php">📦 Produits</a>
      <a href="/glowy_shop/admin/commandes.php" class="active">🛒 Commandes</a>
      <a href="/glowy_shop/pages/index.php" style="margin-top:2rem;">← Retour au site</a>
      <a href="/glowy_shop/auth/logout.php">🚪 Déconnexion</a>
    </aside>

    <!-- CONTENU -->
    <div class="admin-content">
      <h1 style="margin-bottom:1.5rem;">Gestion des commandes 🛒</h1>

      <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
      <?php endif; ?>

      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Client</th>
              <th>Email</th>
              <th>Total</th>
              <th>Date</th>
              <th>Statut</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($commandes)): ?>
              <?php foreach ($commandes as $c): ?>
                <tr>
                  <td>#<?= $c['id'] ?></td>
                  <td><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></td>
                  <td><?= htmlspecialchars($c['email']) ?></td>
                  <td><?= number_format($c['total_ttc'], 2) ?> €</td>
                  <td><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></td>
                  <td>
                    <?php
                      $couleurs = [
                        'en_attente' => '#FFF8E1;color:#F57F17',
                        'confirmee'  => '#E3F2FD;color:#1565C0',
                        'expediee'   => '#F3E5F5;color:#6A1B9A',
                        'livree'     => '#E8F5E9;color:#2E7D32',
                        'annulee'    => '#FFEBEE;color:#C62828',
                      ];
                      $style = $couleurs[$c['statut']] ?? '';
                    ?>
                    <span class="badge" style="background:<?= $style ?>">
                      <?= htmlspecialchars($c['statut']) ?>
                    </span>
                  </td>
                  <td>
                    <form method="POST" style="display:flex; gap:0.5rem; align-items:center;">
                      <input type="hidden" name="id" value="<?= $c['id'] ?>">
                      <select name="statut" style="padding:0.3rem 0.5rem; border:2px solid #e0e0e0; border-radius:6px; font-size:0.85rem;">
                        <?php foreach (['en_attente','confirmee','expediee','livree','annulee'] as $s): ?>
                          <option value="<?= $s ?>" <?= $c['statut'] === $s ? 'selected' : '' ?>>
                            <?= ucfirst($s) ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                      <button type="submit" class="btn btn-primary"
                              style="padding:0.3rem 0.7rem; font-size:0.8rem;">
                        ✅
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" style="text-align:center; color:#888; padding:2rem;">
                  Aucune commande pour le moment. 🌸
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>