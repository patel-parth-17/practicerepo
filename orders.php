<?php
/**
 * LUMINA BOOKSTORE — MY ORDERS & ORDER TRACKING
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/data.php';

$orders = getOrders();
$currentUser = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Orders & Receipts — Lumina Books</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="bookstore.css" />
  <style>
    .orders-page-shell {
      max-width: 1080px;
      margin: 40px auto 80px;
      padding: 0 24px;
    }
    .back-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: var(--text-muted);
      font-weight: 600;
      margin-bottom: 24px;
      transition: color 0.2s ease;
    }
    .back-btn:hover { color: var(--text-main); }
    .order-card {
      background: var(--bg-card);
      border: 1px solid var(--border-subtle);
      border-radius: var(--radius-lg);
      padding: 24px;
      margin-bottom: 24px;
      box-shadow: var(--shadow-sm);
    }
    .order-header-row {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      border-bottom: 1px solid var(--border-subtle);
      padding-bottom: 16px;
      margin-bottom: 16px;
    }
    .order-badge-status {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 4px 12px;
      border-radius: var(--radius-full);
      font-size: 0.78rem;
      font-weight: 800;
      background: rgba(16, 185, 129, 0.15);
      color: var(--accent-emerald);
      border: 1px solid rgba(16, 185, 129, 0.3);
    }
    .order-items-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 16px;
    }
    .order-items-table th {
      text-align: left;
      font-size: 0.76rem;
      color: var(--text-dim);
      text-transform: uppercase;
      padding-bottom: 8px;
    }
    .order-items-table td {
      padding: 8px 0;
      font-size: 0.9rem;
      border-top: 1px solid var(--border-subtle);
    }
    .empty-orders {
      text-align: center;
      padding: 64px 20px;
      background: var(--bg-card);
      border: 1px solid var(--border-subtle);
      border-radius: var(--radius-lg);
    }
  </style>
</head>
<body>

  <!-- Minimal Header -->
  <header class="site-header">
    <div class="header-container">
      <a href="index.php" class="brand">
        <div class="brand-icon-box">📚</div>
        <div class="brand-text">
          LUMINA<span>.</span>
          <span class="brand-sub">FINE BOOKS & PRINTS</span>
        </div>
      </a>
      <div>
        <a href="index.php" class="btn-secondary" style="padding: 8px 18px; font-size: 0.88rem;">
          Return to Storefront
        </a>
      </div>
    </div>
  </header>

  <main class="orders-page-shell">
    <a href="index.php" class="back-btn">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
      Back to Book Catalog
    </a>

    <div style="margin-bottom: 32px;">
      <h1 style="font-family: var(--font-serif); font-size: 2.2rem; font-weight: 800; margin-bottom: 6px;">
        Order History & Tracking
      </h1>
      <p style="color: var(--text-muted); font-size: 0.95rem;">
        Track recently placed orders, download purchase summaries, or verify shipment statuses.
      </p>
    </div>

    <?php if (empty($orders)): ?>
      <div class="empty-orders">
        <div style="font-size: 3rem; margin-bottom: 12px;">📦</div>
        <h3 style="font-family: var(--font-serif); font-size: 1.5rem; margin-bottom: 8px;">No orders found</h3>
        <p style="color: var(--text-muted); margin-bottom: 24px;">You haven't placed any book orders yet.</p>
        <a href="index.php" class="btn-primary">Browse Curated Books</a>
      </div>
    <?php else: ?>
      <?php foreach ($orders as $order): ?>
        <div class="order-card">
          <div class="order-header-row">
            <div>
              <div style="font-size: 0.78rem; color: var(--text-dim); text-transform: uppercase; font-weight: 700;">ORDER REFERENCE</div>
              <strong style="font-family: monospace; font-size: 1.15rem; color: var(--accent-gold);">
                <?php echo htmlspecialchars($order['order_number'] ?? 'LUM-SAMPLE'); ?>
              </strong>
            </div>

            <div>
              <div style="font-size: 0.78rem; color: var(--text-dim); text-transform: uppercase; font-weight: 700;">DATE</div>
              <span style="font-size: 0.88rem; color: var(--text-muted);">
                <?php echo htmlspecialchars($order['created_at'] ?? date('Y-m-d')); ?>
              </span>
            </div>

            <div>
              <div style="font-size: 0.78rem; color: var(--text-dim); text-transform: uppercase; font-weight: 700;">TOTAL AMOUNT</div>
              <strong style="font-size: 1.1rem; color: var(--text-main);">
                $<?php echo number_format($order['total_amount'] ?? 0, 2); ?>
              </strong>
            </div>

            <div>
              <span class="order-badge-status">
                ● <?php echo htmlspecialchars($order['status'] ?? 'Processing'); ?>
              </span>
            </div>
          </div>

          <div style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 14px;">
            Recipient: <strong><?php echo htmlspecialchars($order['customer_name'] ?? 'Reader'); ?></strong> 
            (<?php echo htmlspecialchars($order['customer_email'] ?? ''); ?>) · 
            Delivery Address: <em><?php echo htmlspecialchars($order['shipping_address'] ?? 'Confirmed'); ?></em>
          </div>

          <?php if (!empty($order['items'])): ?>
            <table class="order-items-table">
              <thead>
                <tr>
                  <th>Book Title</th>
                  <th>Format</th>
                  <th>Quantity</th>
                  <th style="text-align: right;">Price</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($order['items'] as $it): ?>
                  <tr>
                    <td><strong><?php echo htmlspecialchars($it['title']); ?></strong></td>
                    <td style="color: var(--text-dim);"><?php echo htmlspecialchars($it['format'] ?? 'Hardcover'); ?></td>
                    <td>× <?php echo htmlspecialchars($it['quantity'] ?? 1); ?></td>
                    <td style="text-align: right;">$<?php echo number_format($it['price'] ?? 0, 2); ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>

          <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid var(--border-subtle); padding-top: 14px;">
            <button class="btn-secondary" style="padding: 6px 14px; font-size: 0.82rem;" onclick="window.print()">
              🖨️ Print Receipt
            </button>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </main>

  <footer class="site-footer" style="padding: 32px 24px; text-align: center;">
    <div style="font-size: 0.85rem; color: var(--text-muted);">
      © <?php echo date('Y'); ?> Lumina Books International · XAMPP User Interface
    </div>
  </footer>

  <script>
    // Inherit theme from localStorage
    const savedTheme = localStorage.getItem('lumina_theme') || 'dark';
    document.documentElement.setAttribute('data-theme', savedTheme);
  </script>
</body>
</html>
