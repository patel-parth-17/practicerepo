<?php
/**
 * LUMINA BOOKSTORE — USER SIDE STOREFRONT
 * Designed for XAMPP (Apache + PHP + MySQL)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/data.php';

// Retrieve categories and catalog books
$categories = getCategories();
$books = getBooks('all');
$currentUser = $_SESSION['user'] ?? null;
$dbConnected = isDbConnected();

// Selected spotlight book for the hero section
$spotlightBook = $books[0] ?? null;
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="theme-color" content="#0a0e17" />
  <meta name="description" content="Lumina Bookstore — Discover hand-curated books across literature, science, design, and philosophy. Free express delivery on orders over $35." />
  <title>Lumina Books — Curated Independent Bookstore</title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,400;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Core Bookstore Stylesheet -->
  <link rel="stylesheet" href="bookstore.css" />
</head>
<body>

  <!-- Top Announcement Bar -->
  <aside class="announcement-bar" id="announcement-banner">
    <span class="badge">SPRING FESTIVAL</span>
    <span>Use code <strong onclick="applyPromoQuick()">READ20</strong> for 20% off all Bestsellers · Free Express Delivery over $35!</span>
    <button class="announcement-close" id="close-announcement" aria-label="Dismiss banner">&times;</button>
  </aside>

  <!-- Sticky Glassmorphic Header -->
  <header class="site-header">
    <div class="header-container">
      
      <!-- Brand Logo -->
      <a href="index.php" class="brand" aria-label="Lumina Books Home">
        <div class="brand-icon-box">📚</div>
        <div class="brand-text">
          LUMINA<span>.</span>
          <span class="brand-sub">FINE BOOKS & PRINTS</span>
        </div>
      </a>

      <!-- Live Search Bar -->
      <div class="search-container">
        <div class="search-input-wrapper">
          <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <path d="m21 21-4.3-4.3"></path>
          </svg>
          <input 
            type="search" 
            class="search-input" 
            id="global-search" 
            placeholder="Search by title, author, or genre..." 
            autocomplete="off"
            aria-label="Search books"
          />
          <button type="button" class="search-clear-btn" id="search-clear" aria-label="Clear search">&times;</button>
          <span class="search-shortcut" title="Press '/' to quickly search">/</span>
        </div>
      </div>

      <!-- Navigation Links -->
      <nav aria-label="Main Navigation">
        <ul class="nav-links">
          <li><a href="#catalog" class="nav-link active">Catalog</a></li>
          <li><a href="#spotlight" class="nav-link">Staff Picks</a></li>
          <li><a href="#reviews" class="nav-link">Reviews</a></li>
          <li><a href="orders.php" class="nav-link">My Orders</a></li>
        </ul>
      </nav>

      <!-- Action Buttons -->
      <div class="nav-actions">
        <!-- Theme Toggle -->
        <button class="action-btn" id="theme-toggle-btn" title="Toggle Light / Dark Mode" aria-label="Toggle Theme">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="5"></circle>
            <path d="M12 1v2m0 18v2M4.22 4.22l1.42 1.42m12.72 12.72 1.42 1.42M1 12h2m18 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"></path>
          </svg>
        </button>

        <!-- Wishlist Button -->
        <button class="action-btn" id="wishlist-nav-btn" title="View Wishlist" aria-label="Wishlist">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path>
          </svg>
          <span class="action-badge" id="wishlist-count-badge">0</span>
        </button>

        <!-- Cart Pill Button -->
        <button class="cart-button-pill" id="open-cart-btn" aria-label="Shopping Cart">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="8" cy="21" r="1"></circle>
            <circle cx="19" cy="21" r="1"></circle>
            <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
          </svg>
          <span id="nav-cart-total">$0.00</span>
          <span class="action-badge" id="cart-count-badge">0</span>
        </button>

        <!-- User / Login -->
        <?php if ($currentUser): ?>
          <a href="login.php" class="user-login-btn" title="Logged in as <?php echo htmlspecialchars($currentUser['name']); ?>">
            <span>👤</span> <?php echo htmlspecialchars(explode(' ', $currentUser['name'])[0]); ?>
          </a>
        <?php else: ?>
          <a href="login.php" class="user-login-btn">
            <span>Sign In</span>
          </a>
        <?php endif; ?>

      </div>

    </div>
  </header>

  <!-- Hero Editorial Showcase -->
  <section class="hero-section">
    <div class="hero-container">
      
      <!-- Hero Copy Left -->
      <div class="hero-copy">
        <div class="hero-eyebrow">
          <span class="pulse-dot"></span>
          <span>CURATED READING SANCTUARY</span>
        </div>

        <h1 class="hero-title">
          Books that shape how you<br />
          <em>think, dream & lead.</em>
        </h1>

        <p class="hero-desc">
          Step into a carefully curated sanctuary of contemporary thought, extraordinary fiction, and groundbreaking ideas. Free worldwide express shipping on orders over $35.
        </p>

        <div class="hero-actions">
          <a href="#catalog" class="btn-primary">
            <span>Explore Bestsellers</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
          </a>
          <button class="btn-secondary" onclick="openQuickView(<?php echo $spotlightBook ? $spotlightBook['id'] : 1; ?>)">
            <span>Read Excerpt</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>
          </button>
        </div>

        <div class="hero-stats">
          <div class="stat-item">
            <strong>10,000+</strong>
            <span>Curated Titles</span>
          </div>
          <div class="stat-item">
            <strong>4.9 / 5.0</strong>
            <span>Verified Rating</span>
          </div>
          <div class="stat-item">
            <strong>45k+</strong>
            <span>Avid Readers</span>
          </div>
        </div>
      </div>

      <!-- Spotlight Book Showcase Right -->
      <?php if ($spotlightBook): ?>
      <div class="spotlight-card">
        <span class="spotlight-badge">★ BOOK OF THE MONTH</span>
        
        <div class="spotlight-main">
          <div class="book-cover large <?php echo htmlspecialchars($spotlightBook['cover_theme'] ?? 'cyber-blue'); ?>" 
               onclick="openQuickView(<?php echo $spotlightBook['id']; ?>)">
            <span class="cover-badge-tag"><?php echo htmlspecialchars($spotlightBook['genre']); ?></span>
            <div class="cover-title"><?php echo htmlspecialchars($spotlightBook['title']); ?></div>
            <div class="cover-author"><?php echo htmlspecialchars($spotlightBook['author']); ?></div>
          </div>

          <div class="spotlight-info">
            <h3><?php echo htmlspecialchars($spotlightBook['title']); ?></h3>
            <span class="author-tag">by <?php echo htmlspecialchars($spotlightBook['author']); ?></span>
            
            <div class="rating-row">
              <span class="stars">★★★★★</span>
              <strong><?php echo number_format($spotlightBook['rating'], 1); ?></strong>
              <span class="count">(<?php echo $spotlightBook['reviews_count']; ?> reader reviews)</span>
            </div>

            <p class="excerpt">
              <?php echo htmlspecialchars($spotlightBook['description']); ?>
            </p>

            <div class="spotlight-footer">
              <div class="price-tag">
                <span class="price-current">$<?php echo number_format($spotlightBook['price'], 2); ?></span>
                <?php if (!empty($spotlightBook['original_price'])): ?>
                  <span class="price-original">$<?php echo number_format($spotlightBook['original_price'], 2); ?></span>
                  <span class="savings-tag">SAVE 21%</span>
                <?php endif; ?>
              </div>
              <button class="btn-primary" style="padding: 10px 20px; font-size: 0.88rem;" onclick="addToCart(<?php echo $spotlightBook['id']; ?>, event)">
                Quick Add
              </button>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>

    </div>
  </section>

  <!-- Service Guarantees Strip -->
  <section class="features-strip">
    <div class="features-container">
      <div class="feature-box">
        <div class="feature-icon-box">🚀</div>
        <div>
          <h4>Free Express Shipping</h4>
          <p>Complimentary delivery over $35 with real-time tracking.</p>
        </div>
      </div>
      <div class="feature-box">
        <div class="feature-icon-box">✨</div>
        <div>
          <h4>100% Genuine Print</h4>
          <p>First editions, premium hardcovers & archival quality paper.</p>
        </div>
      </div>
      <div class="feature-box">
        <div class="feature-icon-box">🛡️</div>
        <div>
          <h4>30-Day Reader Guarantee</h4>
          <p>Hassle-free exchange or full refund if you aren't captivated.</p>
        </div>
      </div>
      <div class="feature-box">
        <div class="feature-icon-box">☕</div>
        <div>
          <h4>Literary Concierge</h4>
          <p>Curated recommendations tailored to your reading journey.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Catalog Section -->
  <main class="catalog-section" id="catalog">
    
    <div class="section-header">
      <div class="section-title-wrap">
        <h2>Curated Literary Collection</h2>
        <p>Explore award-winning fiction, groundbreaking research, and transformative ideas.</p>
      </div>

      <!-- Quick Action Pills -->
      <div style="display: flex; gap: 8px;">
        <button class="btn-secondary" style="padding: 8px 16px; font-size: 0.85rem;" onclick="filterBestsellers()">
          🔥 Bestsellers Only
        </button>
      </div>
    </div>

    <!-- Category Chips Carousel -->
    <div class="categories-filter-bar" role="tablist">
      <?php foreach ($categories as $index => $cat): ?>
        <button 
          class="category-chip <?php echo $index === 0 ? 'active' : ''; ?>" 
          data-category="<?php echo htmlspecialchars($cat['slug']); ?>"
          role="tab"
        >
          <?php echo htmlspecialchars($cat['name']); ?>
        </button>
      <?php endforeach; ?>
    </div>

    <!-- Filter & Sort Toolbar -->
    <div class="catalog-toolbar">
      <div class="results-count">
        Showing <strong id="results-count-number"><?php echo count($books); ?></strong> curated titles
      </div>

      <div class="toolbar-controls">
        <label for="catalog-sort" style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Sort By:</label>
        <select id="catalog-sort" class="sort-select">
          <option value="featured">Featured & Curated</option>
          <option value="rating">Highest Rated ★</option>
          <option value="price-asc">Price: Low to High</option>
          <option value="price-desc">Price: High to Low</option>
          <option value="newest">Newest Releases</option>
        </select>
      </div>
    </div>

    <!-- Books Grid Container -->
    <div class="books-grid" id="books-grid-container">
      <?php foreach ($books as $b): ?>
        <article class="book-card" data-id="<?php echo $b['id']; ?>">
          <?php if (!empty($b['badge'])): ?>
            <span class="ribbon-badge <?php echo strtolower(str_replace(' ', '-', $b['badge'])); ?>">
              <?php echo htmlspecialchars($b['badge']); ?>
            </span>
          <?php endif; ?>

          <div class="card-cover-container">
            <div class="book-cover <?php echo htmlspecialchars($b['cover_theme'] ?? 'cyber-blue'); ?>" onclick="openQuickView(<?php echo $b['id']; ?>)">
              <span class="cover-badge-tag"><?php echo htmlspecialchars($b['genre']); ?></span>
              <div class="cover-title"><?php echo htmlspecialchars($b['title']); ?></div>
              <div class="cover-author"><?php echo htmlspecialchars($b['author']); ?></div>
            </div>

            <div class="card-action-overlay">
              <button class="card-action-btn wishlist-btn" onclick="toggleWishlist(<?php echo $b['id']; ?>)" title="Save to Wishlist">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
              </button>
              <button class="card-action-btn" onclick="openQuickView(<?php echo $b['id']; ?>)" title="Quick View">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
          </div>

          <div class="card-genre-pill"><?php echo htmlspecialchars($b['genre']); ?></div>
          <h3 class="card-title" onclick="openQuickView(<?php echo $b['id']; ?>)"><?php echo htmlspecialchars($b['title']); ?></h3>
          <div class="card-author">by <?php echo htmlspecialchars($b['author']); ?></div>

          <div class="card-rating-row">
            <span class="stars">★★★★★</span>
            <strong><?php echo number_format($b['rating'], 1); ?></strong>
            <span style="color: var(--text-dim);">(<?php echo $b['reviews_count'] ?? 120; ?>)</span>
          </div>

          <div class="card-footer">
            <div class="price-tag">
              <span class="price-current">$<?php echo number_format($b['price'], 2); ?></span>
              <?php if (!empty($b['original_price'])): ?>
                <span class="price-original">$<?php echo number_format($b['original_price'], 2); ?></span>
              <?php endif; ?>
            </div>
            <button class="add-cart-btn" onclick="addToCart(<?php echo $b['id']; ?>, event)">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
              Add
            </button>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </main>

  <!-- Editorial Spotlight Section -->
  <section class="curator-spotlight" id="spotlight">
    <div class="spotlight-wrapper">
      <div>
        <span class="curator-badge">CURATOR'S NOTE & AUTHOR OF THE MONTH</span>
        <h2 class="curator-heading">"Literature is the only passport that transcends time and space."</h2>
        <p class="curator-quote">
          "In our fast-paced digitized era, deep long-form reading is no longer just recreation — it is a superpower. Every curated edition on our shelves has been scrutinized for depth, prose quality, and enduring impact."
        </p>
        <div class="curator-author">
          <div class="author-avatar">✒️</div>
          <div class="author-info">
            <strong>Dr. Evelyn Vance</strong>
            <span>Lead Literary Curator & Author of The Architecture of Tomorrow</span>
          </div>
        </div>
      </div>

      <div style="display: flex; justify-content: center;">
        <div class="book-cover large sapphire-dream" onclick="openQuickView(1)" style="transform: perspective(700px) rotateY(-12deg) rotateX(4deg); box-shadow: 0 30px 60px -12px rgba(0,0,0,0.7);">
          <span class="cover-badge-tag">EXCLUSIVE EDITION</span>
          <div class="cover-title">The Architecture of Tomorrow</div>
          <div class="cover-author">Dr. Evelyn Vance</div>
        </div>
      </div>
    </div>
  </section>

  <!-- Reader Testimonials / Social Proof -->
  <section class="reviews-section" id="reviews">
    <div class="section-title-wrap" style="text-align: center;">
      <span class="hero-eyebrow" style="margin: 0 auto 12px;">READER ACCLAIM</span>
      <h2>Loved by 45,000+ Bibliophiles</h2>
      <p>Here is what members of our literary circle have to say about their reading experiences.</p>
    </div>

    <div class="reviews-grid">
      <div class="review-card">
        <div class="review-rating">★★★★★</div>
        <p class="review-text">
          "The unboxing experience from Lumina is incomparable. The tactile hardcover binding, custom ribbon bookmarks, and swift packaging feel like receiving a present from an old friend."
        </p>
        <div class="reviewer-meta">
          <div class="reviewer-avatar">AW</div>
          <div class="reviewer-details">
            <strong>Alexander Wright</strong>
            <span>Verified Reader · Boston, MA</span>
          </div>
        </div>
      </div>

      <div class="review-card">
        <div class="review-rating">★★★★★</div>
        <p class="review-text">
          "Finding genuinely thoughtful, non-cliché non-fiction has always been tough until I found Lumina. 'Zero to Mastery' transformed our leadership weekly discussions completely."
        </p>
        <div class="reviewer-meta">
          <div class="reviewer-avatar">SR</div>
          <div class="reviewer-details">
            <strong>Sarah Reed</strong>
            <span>Product Director · San Francisco, CA</span>
          </div>
        </div>
      </div>

      <div class="review-card">
        <div class="review-rating">★★★★★</div>
        <p class="review-text">
          "Seamless checkout and lightning-fast delivery! Running locally on XAMPP with real-time cart and order confirmation makes this the benchmark bookstore experience."
        </p>
        <div class="reviewer-meta">
          <div class="reviewer-avatar">MK</div>
          <div class="reviewer-details">
            <strong>Michael Kowalski</strong>
            <span>Collector & Historian · London, UK</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Newsletter / Book Club Subscription -->
  <section class="newsletter-section">
    <div class="newsletter-box">
      <span class="hero-eyebrow" style="margin-bottom: 12px;">LUMINA CIRCLE</span>
      <h3>Receive 15% Off Your Next Literary Discovery</h3>
      <p>Subscribe to our weekly dispatch of essays, exclusive author discussions, and seasonal book collector drops.</p>

      <form class="newsletter-form" id="newsletter-form">
        <input 
          type="email" 
          class="newsletter-input" 
          placeholder="Enter your email address..." 
          required 
        />
        <button type="submit" class="btn-primary" style="padding: 12px 28px; white-space: nowrap;">
          Join the Circle
        </button>
      </form>
    </div>
  </section>

  <!-- Footer -->
  <footer class="site-footer">
    <div class="footer-container">
      <div>
        <a href="index.php" class="brand" style="margin-bottom: 14px;">
          <div class="brand-icon-box">📚</div>
          <div class="brand-text">
            LUMINA<span>.</span>
            <span class="brand-sub">FINE BOOKS & PRINTS</span>
          </div>
        </a>
        <p style="font-size: 0.9rem; line-height: 1.7; max-width: 320px; margin-bottom: 20px;">
          An independent bookstore dedicated to the craft of physical literature, transformative non-fiction, and thoughtful ideas.
        </p>
        <div style="display: flex; gap: 12px; align-items: center;">
          <span style="font-size: 0.76rem; background: var(--bg-card); border: 1px solid var(--border-subtle); padding: 4px 10px; border-radius: var(--radius-full); color: var(--accent-gold);">
            <?php echo $dbConnected ? '● MySQL Connected (XAMPP)' : '● Standalone Fast Mode (XAMPP)'; ?>
          </span>
        </div>
      </div>

      <div class="footer-col">
        <h5>Explore Genres</h5>
        <ul>
          <li><a href="#catalog" onclick="filterByGenre('Fiction & Literature')">Fiction & Literature</a></li>
          <li><a href="#catalog" onclick="filterByGenre('Sci-Fi & Fantasy')">Sci-Fi & Fantasy</a></li>
          <li><a href="#catalog" onclick="filterByGenre('Technology & AI')">Technology & AI</a></li>
          <li><a href="#catalog" onclick="filterByGenre('Business & Wealth')">Business & Leadership</a></li>
          <li><a href="#catalog" onclick="filterByGenre('Psychology & Mind')">Psychology & Mind</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h5>Reader Services</h5>
        <ul>
          <li><a href="orders.php">Track Order Status</a></li>
          <li><a href="orders.php">Recent Receipts</a></li>
          <li><a href="#announcement-banner">Promotions & Codes</a></li>
          <li><a href="login.php">Reader Account</a></li>
          <li><a href="login.php">Admin Portal</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h5>About Lumina</h5>
        <ul>
          <li><a href="#spotlight">Our Curation Philosophy</a></li>
          <li><a href="#reviews">Reader Testimonials</a></li>
          <li><a href="#">Shipping Policies</a></li>
          <li><a href="#">Privacy & Terms</a></li>
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <div>
        © <?php echo date('Y'); ?> Lumina Books International. Built for XAMPP Server. All rights reserved.
      </div>
      <div style="display: flex; gap: 14px; font-size: 0.8rem;">
        <span>🔒 256-Bit SSL Encryption</span>
        <span>💳 Cards · UPI · Cash on Delivery</span>
      </div>
    </div>
  </footer>

  <!-- Slide-Out Shopping Cart Drawer -->
  <div class="drawer-backdrop" id="cart-drawer-backdrop" aria-hidden="true">
    <aside class="cart-drawer" role="dialog" aria-label="Shopping Cart Drawer">
      
      <div class="drawer-header">
        <h3>Your Reading Bag</h3>
        <button class="drawer-close-btn" id="close-cart-btn" aria-label="Close cart">&times;</button>
      </div>

      <!-- Free Shipping Meter -->
      <div class="free-shipping-meter">
        <div class="shipping-meter-text" id="shipping-meter-message">
          Add <strong>$35.00</strong> more for FREE Express Shipping!
        </div>
        <div class="shipping-bar-track">
          <div class="shipping-bar-fill" id="shipping-meter-fill"></div>
        </div>
      </div>

      <!-- Cart Item Rows -->
      <div class="cart-items-list" id="cart-items-container">
        <!-- Injected dynamically via bookstore.js -->
      </div>

      <!-- Drawer Footer -->
      <div class="drawer-footer">
        <div class="coupon-row">
          <input 
            type="text" 
            class="coupon-input" 
            id="coupon-code-input" 
            placeholder="PROMO CODE (e.g. READ20)" 
          />
          <button class="coupon-btn" id="apply-coupon-btn">Apply</button>
        </div>

        <div class="order-calc-row">
          <span>Subtotal</span>
          <strong id="drawer-subtotal">$0.00</strong>
        </div>
        <div class="order-calc-row">
          <span>Discount</span>
          <span id="drawer-discount" style="color: var(--accent-emerald);">-$0.00</span>
        </div>
        <div class="order-calc-row">
          <span>Estimated Shipping</span>
          <span id="drawer-shipping">FREE</span>
        </div>
        <div class="order-calc-row total-row">
          <span>Total</span>
          <span id="drawer-total" style="color: var(--accent-gold);">$0.00</span>
        </div>

        <button class="checkout-btn" id="checkout-trigger-btn">
          <span>Proceed to Checkout</span>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
        </button>
      </div>
    </aside>
  </div>

  <!-- Quick View Lightbox Modal -->
  <div class="modal-overlay" id="quickview-modal" aria-hidden="true">
    <div class="modal-content-box" role="dialog" aria-label="Book Preview">
      <button class="modal-close-icon" onclick="closeModal('quickview-modal')" aria-label="Close preview">&times;</button>
      <div id="quickview-content">
        <!-- Injected via bookstore.js -->
      </div>
    </div>
  </div>

  <!-- Wishlist Modal -->
  <div class="modal-overlay" id="wishlist-modal" aria-hidden="true">
    <div class="modal-content-box" style="max-width: 520px;" role="dialog" aria-label="Wishlist">
      <button class="modal-close-icon" onclick="closeModal('wishlist-modal')" aria-label="Close wishlist">&times;</button>
      <div style="padding: 28px;">
        <h3 style="font-family: var(--font-serif); font-size: 1.5rem; margin-bottom: 16px;">Saved to Wishlist ❤️</h3>
        <div id="wishlist-modal-items">
          <!-- Injected via bookstore.js -->
        </div>
      </div>
    </div>
  </div>

  <!-- Full Checkout Modal -->
  <div class="modal-overlay" id="checkout-modal" aria-hidden="true">
    <div class="modal-content-box" style="max-width: 680px;" role="dialog" aria-label="Checkout">
      <button class="modal-close-icon" onclick="closeModal('checkout-modal')" aria-label="Close checkout">&times;</button>
      
      <div class="checkout-form-wrap" id="checkout-modal-body">
        <div style="border-bottom: 1px solid var(--border-subtle); padding-bottom: 16px; margin-bottom: 20px;">
          <h2 style="font-family: var(--font-serif); font-size: 1.75rem; margin-bottom: 4px;">Secure Express Checkout</h2>
          <p style="color: var(--text-muted); font-size: 0.88rem;">Complete your order details below. Hand-packaged and dispatched with care.</p>
        </div>

        <form id="checkout-form">
          <input type="hidden" id="selected-payment-method" value="Cash on Delivery" />

          <!-- Order Summary Mini Card -->
          <div style="background: var(--bg-secondary); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 16px; margin-bottom: 20px;">
            <div style="font-size: 0.82rem; font-weight: 700; color: var(--text-muted); margin-bottom: 10px; text-transform: uppercase;">Order Items</div>
            <div id="checkout-summary-items"></div>
            <div style="display: flex; justify-content: space-between; border-top: 1px solid var(--border-subtle); padding-top: 10px; margin-top: 10px; font-weight: 800; font-size: 1.05rem;">
              <span>Total Payable</span>
              <span id="checkout-summary-total" style="color: var(--accent-gold);">$0.00</span>
            </div>
          </div>

          <div class="checkout-grid">
            <div class="form-group full">
              <label for="fullName">Full Name</label>
              <input type="text" id="fullName" name="fullName" class="form-control" placeholder="e.g. Eleanor Vance" required value="<?php echo htmlspecialchars($currentUser['name'] ?? ''); ?>" />
            </div>

            <div class="form-group">
              <label for="email">Email Address</label>
              <input type="email" id="email" name="email" class="form-control" placeholder="reader@example.com" required value="<?php echo htmlspecialchars($currentUser['email'] ?? ''); ?>" />
            </div>

            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input type="tel" id="phone" name="phone" class="form-control" placeholder="+1 (555) 019-2834" required />
            </div>

            <div class="form-group full">
              <label for="address">Shipping Street Address</label>
              <input type="text" id="address" name="address" class="form-control" placeholder="House / Flat No., Street Name" required />
            </div>

            <div class="form-group">
              <label for="city">City / Region</label>
              <input type="text" id="city" name="city" class="form-control" placeholder="City" required />
            </div>

            <div class="form-group">
              <label for="zip">Postal / ZIP Code</label>
              <input type="text" id="zip" name="zip" class="form-control" placeholder="10001" required />
            </div>

            <div class="form-group full">
              <label>Payment Method</label>
              <div class="payment-methods-grid">
                <div class="payment-card-option selected" onclick="selectPaymentMethod(this, 'Cash on Delivery')">
                  <span style="font-size: 1.2rem;">💵</span>
                  <span>Cash on Delivery</span>
                </div>
                <div class="payment-card-option" onclick="selectPaymentMethod(this, 'Credit / Debit Card')">
                  <span style="font-size: 1.2rem;">💳</span>
                  <span>Card / Stripe</span>
                </div>
                <div class="payment-card-option" onclick="selectPaymentMethod(this, 'UPI / QR Code')">
                  <span style="font-size: 1.2rem;">📱</span>
                  <span>UPI / Instant QR</span>
                </div>
                <div class="payment-card-option" onclick="selectPaymentMethod(this, 'PayPal')">
                  <span style="font-size: 1.2rem;">🅿️</span>
                  <span>PayPal</span>
                </div>
              </div>
            </div>
          </div>

          <button type="submit" class="checkout-btn" id="place-order-btn" style="margin-top: 24px;">
            <span>Complete Order</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
          </button>
        </form>
      </div>

    </div>
  </div>

  <!-- Embedded Books JSON for Instant Client Hydration -->
  <script type="application/json" id="initial-books-data">
    <?php echo json_encode($books, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>
  </script>

  <!-- Interactive Scripts -->
  <script src="bookstore.js"></script>

  <script>
    function applyPromoQuick() {
      const input = document.getElementById('coupon-code-input');
      if (input) input.value = 'READ20';
      openCart();
      applyCoupon();
    }

    function filterByGenre(genre) {
      const chip = Array.from(document.querySelectorAll('.category-chip')).find(c => c.textContent.trim().toLowerCase().includes(genre.toLowerCase()));
      if (chip) {
        chip.click();
      } else {
        const searchInput = document.getElementById('global-search');
        if (searchInput) {
          searchInput.value = genre;
          state.searchQuery = genre;
          filterAndRenderBooks();
        }
      }
    }

    function filterBestsellers() {
      state.books = state.books.filter(b => b.is_bestseller == 1);
      filterAndRenderBooks();
      showToast('Showing Bestsellers only', '🔥');
    }
  </script>
</body>
</html>
