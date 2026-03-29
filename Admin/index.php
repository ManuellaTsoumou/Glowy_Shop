<?php
session_start();
require_once __DIR__ . '/../includes/admin_check.php';
require_once __DIR__ . '/../config/db.php';

// Stats
$nb_commandes = $pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()")->fetchColumn();
$ca_total     = $pdo->query("SELECT COALESCE(SUM(total_ttc), 0) FROM orders WHERE statut != 'annulee'")->fetchColumn();
$nb_users     = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'client'")->fetchColumn();
$note_moyenne = $pdo->query("SELECT COALESCE(AVG(note_moyenne), 0) FROM products WHERE is_active = 1")->fetchColumn();

// Dernières commandes
$commandes = $pdo->query("SELECT o.*, u.prenom, u.nom 
                          FROM orders o 
                          JOIN users u ON o.user_id = u.id 
                          ORDER BY o.created_at DESC 
                          LIMIT 10")->fetchAll();

// Top produits
$top_produits = $pdo->query("SELECT p.nom, SUM(oi.quantite) as total_ventes
                             FROM order_items oi
                             JOIN products p ON oi.product_id = p.id
                             GROUP BY p.id
                             ORDER BY total_ventes DESC
                             LIMIT 5")->fetchAll();
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>

<main>
  <div class="admin-layout">

    <!-- SIDEBAR -->
    <aside class="admin-sidebar">
      <p style="color:#888; font-size:0.8rem; text-transform:uppercase; margin-bottom:1rem; padding:0 1rem;">
        Administration
      </p>
      <a href="/glowy_shop/admin/index.php" class="active">📊 Dashboard</a>
      <a href="/glowy_shop/admin/produits.php">📦 Produits</a>
      <a href="/glowy_shop/admin/commandes.php">🛒 Commandes</a>
      <a href="/glowy_shop/pages/index.php" style="margin-top:2rem;">← Retour au site</a>
      <a href="/glowy_shop/auth/logout.php">🚪 Déconnexion</a>
    </aside>

    <!-- CONTENU -->
    <div class="admin-content">
      <h1 style="margin-bottom:1.5rem;">Dashboard 📊</h1>

      <!-- STATS -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-number"><?= $nb_commandes ?></div>
          <div class="stat-label">Commandes aujourd'hui</div>
        </div>
        <div class="stat-card">
          <div class="stat-number"><?= number_format($ca_total, 2) ?> €</div>
          <div class="stat-label">Chiffre d'affaires total</div>
        </div>
        <div class="stat-card">
          <div class="stat-number"><?= $nb_users ?></div>
          <div class="stat-label">Clients inscrits</div>
        </div>
        <div class="stat-card">
          <div class="stat-number"><?= number_format($note_moyenne, 1) ?> ★</div>
          <div class="stat-label">Note moyenne produits</div>
        </div>
      </div>

      <div style="display:grid; grid-template-columns:2fr 1fr; gap:2rem;">

        <!-- DERNIÈRES COMMANDES -->
        <div>
          <h2 style="margin-bottom:1rem;">Dernières commandes</h2>
          <div class="table-container">
            <table>
              <thead>
                <tr>
                  <th>#</th>
                  <th>Client</th>
                  <th>Total</th>
                  <th>Statut</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($commandes)): ?>
                  <?php foreach ($commandes as $c): ?>
                    <tr>
                      <td>#<?= $c['id'] ?></td>
                      <td><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></td>
                      <td><?= number_format($c['total_ttc'], 2) ?> €</td>
                      <td>
                        <span class="badge badge-skin">
                          <?= htmlspecialchars($c['statut']) ?>
                        </span>
                      </td>
                      <td><?= date('d/m/Y', strtotime($c['created_at'])) ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="5" style="text-align:center; color:#888;">Aucune commande</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- TOP PRODUITS -->
        <div>
          <h2 style="margin-bottom:1rem;">Top produits</h2>
          <div style="background:var(--blanc); border-radius:12px; box-shadow:var(--shadow); padding:1rem;">
            <?php if (!empty($top_produits)): ?>
              <?php foreach ($top_produits as $i => $p): ?>
                <div style="display:flex; justify-content:space-between; align-items:center; padding:0.7rem 0; border-bottom:1px solid var(--rose-pale);">
                  <span style="color:var(--rose-profond); font-weight:700;">#<?= $i + 1 ?></span>
                  <span style="flex:1; margin:0 1rem; font-size:0.9rem;">
                    <?= htmlspecialchars($p['nom']) ?>
                  </span>
                  <span class="badge badge-vegan"><?= $p['total_ventes'] ?> ventes</span>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <p style="color:#888; text-align:center;">Aucune vente pour le moment.</p>
            <?php endif; ?>
          </div>
        </div>

      </div>
    </div>
  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>