<?php
/**
 * LUMINA BOOKSTORE — SINGLE BOOK DETAIL PAGE
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/data.php';

$bookId = (int)($_GET['id'] ?? 1);
$book = getBookById($bookId);

if (!$book) {
    header("Location: index.php");
    exit;
}

$allBooks = getBooks('all');
$relatedBooks = array_filter($allBooks, function($b) use ($book) {
    return $b['id'] != $book['id'] && ($b['genre'] == $book['genre'] || $b['category_id'] == ($book['category_id'] ?? 0));
});
if (empty($relatedBooks)) {
    $relatedBooks = array_slice($allBooks, 0, 4);
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo htmlspecialchars($book['title']); ?> by <?php echo htmlspecialchars($book['author']); ?> — Lumina Books</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="bookstore.css" />
  <style>
    .book-detail-wrapper {
      max-width: 1200px;
      margin: 40px auto 80px;
      padding: 0 24px;
    }
    .breadcrumb {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.86rem;
      color: var(--text-muted);
      margin-bottom: 32px;
    }
    .breadcrumb a:hover { color: var(--accent-gold); }
    .book-hero-grid {
      display: grid;
      grid-template-columns: 360px 1fr;
      gap: 56px;
      margin-bottom: 64px;
    }
    .synopsis-box {
      font-size: 1.05rem;
      line-height: 1.8;
      color: var(--text-muted);
      margin-bottom: 28px;
    }
    @media (max-width: 860px) {
      .book-hero-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

  <!-- Sticky Header -->
  <header class="site-header">
    <div class="header-container">
      <a href="index.php" class="brand">
        <div class="brand-icon-box">📚</div>
        <div class="brand-text">
          LUMINA<span>.</span>
          <span class="brand-sub">FINE BOOKS & PRINTS</span>
        </div>
      </a>

      <div class="nav-actions">
        <a href="index.php" class="btn-secondary" style="padding: 8px 18px; font-size: 0.88rem;">
          ← Back to Catalog
        </a>
        <button class="cart-button-pill" id="open-cart-btn">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle>
            <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
          </svg>
          <span id="nav-cart-total">$0.00</span>
          <span class="action-badge" id="cart-count-badge">0</span>
        </button>
      </div>
    </div>
  </header>

  <main class="book-detail-wrapper">
    <!-- Breadcrumb -->
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="index.php">Catalog</a>
      <span>/</span>
      <a href="index.php#catalog"><?php echo htmlspecialchars($book['genre']); ?></a>
      <span>/</span>
      <span style="color: var(--text-main); font-weight: 600;"><?php echo htmlspecialchars($book['title']); ?></span>
    </nav>

    <div class="book-hero-grid">
      <!-- Book Cover Column -->
      <div style="display: flex; flex-direction: column; align-items: center; gap: 24px;">
        <div class="book-cover large <?php echo htmlspecialchars($book['cover_theme'] ?? 'cyber-blue'); ?>" style="width: 260px; height: 380px; padding: 28px;">
          <span class="cover-badge-tag" style="font-size: 0.8rem;"><?php echo htmlspecialchars($book['genre']); ?></span>
          <div class="cover-title" style="font-size: 1.6rem;"><?php echo htmlspecialchars($book['title']); ?></div>
          <div class="cover-author" style="font-size: 0.95rem;">by <?php echo htmlspecialchars($book['author']); ?></div>
        </div>

        <div style="display: flex; gap: 14px; width: 100%; max-width: 260px;">
          <button class="btn-primary" style="flex: 1;" onclick="addToCart(<?php echo $book['id']; ?>, event)">
            Add to Bag
          </button>
          <button class="action-btn" onclick="toggleWishlist(<?php echo $book['id']; ?>)" title="Wishlist">
            ❤️
          </button>
        </div>
      </div>

      <!-- Book Info Column -->
      <div>
        <span class="hero-eyebrow" style="margin-bottom: 14px;"><?php echo htmlspecialchars($book['genre']); ?></span>
        <h1 style="font-family: var(--font-serif); font-size: 2.8rem; font-weight: 800; line-height: 1.15; margin-bottom: 8px;">
          <?php echo htmlspecialchars($book['title']); ?>
        </h1>
        <div style="font-size: 1.15rem; color: var(--accent-gold); font-weight: 600; margin-bottom: 18px;">
          Written by <?php echo htmlspecialchars($book['author']); ?>
        </div>

        <div class="card-rating-row" style="font-size: 1rem; margin-bottom: 24px;">
          <span class="stars" style="font-size: 1.1rem;">★★★★★</span>
          <strong><?php echo number_format($book['rating'], 1); ?></strong>
          <span style="color: var(--text-dim);">· (<?php echo $book['reviews_count'] ?? 120; ?> verified reviews)</span>
        </div>

        <div class="price-tag" style="margin-bottom: 24px;">
          <span class="price-current" style="font-size: 2.2rem;">$<?php echo number_format($book['price'], 2); ?></span>
          <?php if (!empty($book['original_price'])): ?>
            <span class="price-original" style="font-size: 1.25rem;">$<?php echo number_format($book['original_price'], 2); ?></span>
            <span class="savings-tag">SAVE <?php echo round((1 - $book['price'] / $book['original_price']) * 100); ?>%</span>
          <?php endif; ?>
        </div>

        <div class="synopsis-box">
          <h4 style="color: var(--text-main); font-family: var(--font-serif); font-size: 1.2rem; margin-bottom: 8px;">Book Synopsis</h4>
          <?php echo htmlspecialchars($book['description']); ?>
        </div>

        <div class="specs-grid" style="margin-bottom: 32px;">
          <div class="spec-cell"><small>Publisher</small><strong><?php echo htmlspecialchars($book['publisher'] ?? 'Apex Press'); ?></strong></div>
          <div class="spec-cell"><small>Pages</small><strong><?php echo $book['pages'] ?? 380; ?> Pages</strong></div>
          <div class="spec-cell"><small>ISBN-13</small><strong><?php echo htmlspecialchars($book['isbn'] ?? '978-0134685991'); ?></strong></div>
          <div class="spec-cell"><small>Year</small><strong><?php echo $book['publish_year'] ?? 2024; ?></strong></div>
        </div>
      </div>
    </div>

    <!-- Related Books -->
    <div style="border-top: 1px solid var(--border-subtle); padding-top: 48px;">
      <h2 style="font-family: var(--font-serif); font-size: 1.8rem; margin-bottom: 24px;">Related Curated Reads</h2>
      <div class="books-grid">
        <?php foreach (array_slice($relatedBooks, 0, 3) as $rb): ?>
          <article class="book-card">
            <div class="card-cover-container">
              <a href="book.php?id=<?php echo $rb['id']; ?>" class="book-cover <?php echo htmlspecialchars($rb['cover_theme'] ?? 'cyber-blue'); ?>">
                <span class="cover-badge-tag"><?php echo htmlspecialchars($rb['genre']); ?></span>
                <div class="cover-title"><?php echo htmlspecialchars($rb['title']); ?></div>
                <div class="cover-author"><?php echo htmlspecialchars($rb['author']); ?></div>
              </a>
            </div>
            <div class="card-genre-pill"><?php echo htmlspecialchars($rb['genre']); ?></div>
            <h3 class="card-title"><a href="book.php?id=<?php echo $rb['id']; ?>"><?php echo htmlspecialchars($rb['title']); ?></a></h3>
            <div class="card-author">by <?php echo htmlspecialchars($rb['author']); ?></div>
            <div class="card-footer">
              <span class="price-current">$<?php echo number_format($rb['price'], 2); ?></span>
              <button class="add-cart-btn" onclick="addToCart(<?php echo $rb['id']; ?>, event)">Add</button>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </main>

  <!-- Slide-Out Shopping Cart Drawer (reused) -->
  <div class="drawer-backdrop" id="cart-drawer-backdrop" aria-hidden="true">
    <aside class="cart-drawer" role="dialog" aria-label="Shopping Cart Drawer">
      <div class="drawer-header">
        <h3>Your Reading Bag</h3>
        <button class="drawer-close-btn" id="close-cart-btn" aria-label="Close cart">&times;</button>
      </div>
      <div class="free-shipping-meter">
        <div class="shipping-meter-text" id="shipping-meter-message">
          Add <strong>$35.00</strong> more for FREE Express Shipping!
        </div>
        <div class="shipping-bar-track">
          <div class="shipping-bar-fill" id="shipping-meter-fill"></div>
        </div>
      </div>
      <div class="cart-items-list" id="cart-items-container"></div>
      <div class="drawer-footer">
        <div class="order-calc-row total-row">
          <span>Total</span>
          <span id="drawer-total" style="color: var(--accent-gold);">$0.00</span>
        </div>
        <a href="index.php" class="checkout-btn" style="text-decoration: none; text-align: center;">
          Return to Checkout
        </a>
      </div>
    </aside>
  </div>

  <script type="application/json" id="initial-books-data">
    <?php echo json_encode($allBooks, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>
  </script>
  <script src="bookstore.js"></script>
</body>
</html>
