/**
 * LUMINA BOOKSTORE — CLIENT INTERACTIVITY ENGINE
 * Live Search, Category Filters, Cart Drawer, Wishlist, Quick View & Checkout
 */

// Store State
const state = {
  books: [],
  cart: JSON.parse(localStorage.getItem('lumina_cart') || '[]'),
  wishlist: JSON.parse(localStorage.getItem('lumina_wishlist') || '[]'),
  currentCategory: 'all',
  currentSort: 'featured',
  searchQuery: '',
  couponCode: localStorage.getItem('lumina_coupon') || '',
  discountPercent: 0,
  theme: localStorage.getItem('lumina_theme') || 'dark'
};

// Initialize on DOM Ready
document.addEventListener('DOMContentLoaded', () => {
  initTheme();
  loadInitialData();
  bindEvents();
  renderCart();
  updateWishlistUI();
  applySavedCoupon();
});

// Theme Management
function initTheme() {
  document.documentElement.setAttribute('data-theme', state.theme);
  updateThemeIcon();
}

function toggleTheme() {
  state.theme = state.theme === 'dark' ? 'light' : 'dark';
  document.documentElement.setAttribute('data-theme', state.theme);
  localStorage.setItem('lumina_theme', state.theme);
  updateThemeIcon();
  showToast(`Switched to ${state.theme} mode`, '🌓');
}

function updateThemeIcon() {
  const btn = document.getElementById('theme-toggle-btn');
  if (btn) {
    btn.innerHTML = state.theme === 'dark' 
      ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><path d="M12 1v2m0 18v2M4.22 4.22l1.42 1.42m12.72 12.72 1.42 1.42M1 12h2m18 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>'
      : '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>';
  }
}

// Load Books (from embedded page data or API)
function loadInitialData() {
  const embeddedData = document.getElementById('initial-books-data');
  if (embeddedData) {
    try {
      state.books = JSON.parse(embeddedData.textContent);
    } catch (e) {
      console.error('Error parsing embedded books', e);
    }
  }
  
  if (!state.books || state.books.length === 0) {
    fetchBooks();
  } else {
    filterAndRenderBooks();
  }
}

function fetchBooks() {
  const url = `api.php?action=get_books&category=${encodeURIComponent(state.currentCategory)}&search=${encodeURIComponent(state.searchQuery)}&sort=${encodeURIComponent(state.currentSort)}`;
  fetch(url)
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        state.books = data.data;
        filterAndRenderBooks();
      }
    })
    .catch(() => {
      filterAndRenderBooks();
    });
}

// Bind Global Listeners
function bindEvents() {
  // Theme Toggle
  const themeBtn = document.getElementById('theme-toggle-btn');
  if (themeBtn) themeBtn.addEventListener('click', toggleTheme);

  // Search Input
  const searchInput = document.getElementById('global-search');
  const clearBtn = document.getElementById('search-clear');
  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      state.searchQuery = e.target.value.trim();
      if (clearBtn) clearBtn.classList.toggle('visible', state.searchQuery.length > 0);
      filterAndRenderBooks();
    });

    // Keyboard shortcut '/'
    window.addEventListener('keydown', (e) => {
      if (e.key === '/' && document.activeElement !== searchInput && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
        e.preventDefault();
        searchInput.focus();
        searchInput.select();
      }
    });
  }

  if (clearBtn) {
    clearBtn.addEventListener('click', () => {
      searchInput.value = '';
      state.searchQuery = '';
      clearBtn.classList.remove('visible');
      filterAndRenderBooks();
      searchInput.focus();
    });
  }

  // Category Filter Chips
  document.querySelectorAll('.category-chip').forEach(chip => {
    chip.addEventListener('click', () => {
      document.querySelectorAll('.category-chip').forEach(c => c.classList.remove('active'));
      chip.classList.add('active');
      state.currentCategory = chip.dataset.category || 'all';
      filterAndRenderBooks();
    });
  });

  // Sort Dropdown
  const sortSelect = document.getElementById('catalog-sort');
  if (sortSelect) {
    sortSelect.addEventListener('change', (e) => {
      state.currentSort = e.target.value;
      filterAndRenderBooks();
    });
  }

  // Cart Drawer Open / Close
  const openCartBtn = document.getElementById('open-cart-btn');
  const closeCartBtn = document.getElementById('close-cart-btn');
  const cartBackdrop = document.getElementById('cart-drawer-backdrop');

  if (openCartBtn) openCartBtn.addEventListener('click', () => openCart());
  if (closeCartBtn) closeCartBtn.addEventListener('click', () => closeCart());
  if (cartBackdrop) {
    cartBackdrop.addEventListener('click', (e) => {
      if (e.target === cartBackdrop) closeCart();
    });
  }

  // Wishlist Modal Open
  const wishlistBtn = document.getElementById('wishlist-nav-btn');
  if (wishlistBtn) wishlistBtn.addEventListener('click', () => openWishlistModal());

  // Announcement close
  const annClose = document.getElementById('close-announcement');
  if (annClose) {
    annClose.addEventListener('click', () => {
      document.getElementById('announcement-banner').style.display = 'none';
    });
  }

  // Coupon apply
  const applyCouponBtn = document.getElementById('apply-coupon-btn');
  if (applyCouponBtn) {
    applyCouponBtn.addEventListener('click', () => applyCoupon());
  }

  // Checkout modal launch
  const checkoutBtn = document.getElementById('checkout-trigger-btn');
  if (checkoutBtn) {
    checkoutBtn.addEventListener('click', () => {
      closeCart();
      openCheckoutModal();
    });
  }

  // Checkout form submit
  const checkoutForm = document.getElementById('checkout-form');
  if (checkoutForm) {
    checkoutForm.addEventListener('submit', handleCheckoutSubmit);
  }

  // Newsletter form
  const newsForm = document.getElementById('newsletter-form');
  if (newsForm) {
    newsForm.addEventListener('submit', handleNewsletterSubmit);
  }
}

// Filter and Render Books Grid
function filterAndRenderBooks() {
  const container = document.getElementById('books-grid-container');
  const countEl = document.getElementById('results-count-number');
  if (!container) return;

  let filtered = [...state.books];

  // Category Filter
  if (state.currentCategory && state.currentCategory !== 'all') {
    filtered = filtered.filter(b => {
      const g = (b.genre || '').toLowerCase();
      const cat = (b.category_slug || '').toLowerCase();
      return g.includes(state.currentCategory.toLowerCase()) || cat === state.currentCategory.toLowerCase();
    });
  }

  // Search Filter
  if (state.searchQuery) {
    const q = state.searchQuery.toLowerCase();
    filtered = filtered.filter(b => {
      return (b.title || '').toLowerCase().includes(q) ||
             (b.author || '').toLowerCase().includes(q) ||
             (b.genre || '').toLowerCase().includes(q) ||
             (b.description || '').toLowerCase().includes(q);
    });
  }

  // Sorting
  filtered.sort((a, b) => {
    if (state.currentSort === 'price-asc') return a.price - b.price;
    if (state.currentSort === 'price-desc') return b.price - a.price;
    if (state.currentSort === 'rating') return b.rating - a.rating;
    if (state.currentSort === 'newest') return (b.publish_year || 2024) - (a.publish_year || 2024);
    return (b.is_featured + b.is_bestseller) - (a.is_featured + a.is_bestseller);
  });

  if (countEl) countEl.textContent = filtered.length;

  if (filtered.length === 0) {
    container.innerHTML = `
      <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
        <div style="font-size: 3rem; margin-bottom: 14px;">🔍</div>
        <h3 style="font-family: var(--font-serif); font-size: 1.5rem; margin-bottom: 8px;">No stories match your criteria</h3>
        <p style="color: var(--text-muted); margin-bottom: 20px;">Try adjusting your search terms or genre filter.</p>
        <button class="btn-secondary" onclick="resetFilters()">Reset All Filters</button>
      </div>
    `;
    return;
  }

  container.innerHTML = filtered.map(book => createBookCardHTML(book)).join('');
}

function resetFilters() {
  state.currentCategory = 'all';
  state.searchQuery = '';
  state.currentSort = 'featured';
  const searchInput = document.getElementById('global-search');
  if (searchInput) searchInput.value = '';
  document.querySelectorAll('.category-chip').forEach(c => {
    c.classList.toggle('active', c.dataset.category === 'all');
  });
  const sortSelect = document.getElementById('catalog-sort');
  if (sortSelect) sortSelect.value = 'featured';
  filterAndRenderBooks();
}

function createBookCardHTML(b) {
  const isWishlisted = state.wishlist.some(item => item.id == b.id);
  const badgeClass = b.badge ? b.badge.toLowerCase().replace(/\s+/g, '-') : '';

  return `
    <article class="book-card" data-id="${b.id}">
      ${b.badge ? `<span class="ribbon-badge ${badgeClass}">${b.badge}</span>` : ''}
      
      <div class="card-cover-container">
        <div class="book-cover ${b.cover_theme || 'cyber-blue'}" onclick="openQuickView(${b.id})" title="Click to preview ${escapeHTML(b.title)}">
          <span class="cover-badge-tag">${escapeHTML(b.genre || 'Lumina')}</span>
          <div class="cover-title">${escapeHTML(b.title)}</div>
          <div class="cover-author">${escapeHTML(b.author)}</div>
        </div>

        <div class="card-action-overlay">
          <button class="card-action-btn wishlist-btn ${isWishlisted ? 'active' : ''}" 
                  onclick="toggleWishlist(${b.id})" 
                  title="${isWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist'}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="${isWishlisted ? 'currentColor' : 'none'}" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
          </button>
          <button class="card-action-btn" onclick="openQuickView(${b.id})" title="Quick View">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
      </div>

      <div class="card-genre-pill">${escapeHTML(b.genre)}</div>
      <h3 class="card-title" onclick="openQuickView(${b.id})">${escapeHTML(b.title)}</h3>
      <div class="card-author">by ${escapeHTML(b.author)}</div>

      <div class="card-rating-row">
        <span class="stars">★★★★★</span>
        <strong>${Number(b.rating).toFixed(1)}</strong>
        <span style="color: var(--text-dim);">(${b.reviews_count || 120})</span>
      </div>

      <div class="card-footer">
        <div class="price-tag">
          <span class="price-current">$${Number(b.price).toFixed(2)}</span>
          ${b.original_price ? `<span class="price-original">$${Number(b.original_price).toFixed(2)}</span>` : ''}
        </div>
        <button class="add-cart-btn" onclick="addToCart(${b.id}, event)">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
          Add
        </button>
      </div>
    </article>
  `;
}

// Cart Functions
function addToCart(bookId, event, quantity = 1, format = 'Hardcover') {
  if (event) event.stopPropagation();

  const book = state.books.find(b => b.id == bookId);
  if (!book) return;

  const existing = state.cart.find(item => item.id == bookId && item.format === format);
  if (existing) {
    existing.quantity += quantity;
  } else {
    state.cart.push({
      id: book.id,
      title: book.title,
      author: book.author,
      price: Number(book.price),
      cover_theme: book.cover_theme || 'cyber-blue',
      format: format,
      quantity: quantity
    });
  }

  saveCart();
  renderCart();
  showToast(`Added "${book.title}" to your cart!`, '🛒');

  // Button micro-interaction
  if (event && event.currentTarget) {
    const btn = event.currentTarget;
    btn.classList.add('added');
    const originalText = btn.innerHTML;
    btn.innerHTML = '✓ Added';
    setTimeout(() => {
      btn.classList.remove('added');
      btn.innerHTML = originalText;
    }, 1200);
  }
}

function updateCartQuantity(bookId, delta, format = 'Hardcover') {
  const item = state.cart.find(i => i.id == bookId && i.format === format);
  if (!item) return;

  item.quantity += delta;
  if (item.quantity <= 0) {
    state.cart = state.cart.filter(i => !(i.id == bookId && i.format === format));
  }

  saveCart();
  renderCart();
}

function removeCartItem(bookId, format) {
  state.cart = state.cart.filter(i => !(i.id == bookId && i.format === format));
  saveCart();
  renderCart();
  showToast('Item removed from cart', '🗑️');
}

function saveCart() {
  localStorage.setItem('lumina_cart', JSON.stringify(state.cart));
}

function renderCart() {
  const itemsContainer = document.getElementById('cart-items-container');
  const countBadge = document.getElementById('cart-count-badge');
  const navTotalEl = document.getElementById('nav-cart-total');
  const drawerSubtotalEl = document.getElementById('drawer-subtotal');
  const drawerDiscountEl = document.getElementById('drawer-discount');
  const drawerShippingEl = document.getElementById('drawer-shipping');
  const drawerTotalEl = document.getElementById('drawer-total');
  const checkoutBtn = document.getElementById('checkout-trigger-btn');
  const meterFill = document.getElementById('shipping-meter-fill');
  const meterText = document.getElementById('shipping-meter-message');

  const totalCount = state.cart.reduce((sum, item) => sum + item.quantity, 0);
  const subtotal = state.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
  
  const discount = (subtotal * state.discountPercent);
  const shippingThreshold = 35.00;
  const shipping = (subtotal >= shippingThreshold || subtotal === 0) ? 0 : 4.99;
  const finalTotal = Math.max(0, subtotal - discount + shipping);

  if (countBadge) countBadge.textContent = totalCount;
  if (navTotalEl) navTotalEl.textContent = `$${finalTotal.toFixed(2)}`;
  if (drawerSubtotalEl) drawerSubtotalEl.textContent = `$${subtotal.toFixed(2)}`;
  if (drawerDiscountEl) drawerDiscountEl.textContent = `-$${discount.toFixed(2)}`;
  if (drawerShippingEl) drawerShippingEl.textContent = shipping === 0 ? 'FREE' : `$${shipping.toFixed(2)}`;
  if (drawerTotalEl) drawerTotalEl.textContent = `$${finalTotal.toFixed(2)}`;

  if (checkoutBtn) checkoutBtn.disabled = totalCount === 0;

  // Free shipping meter
  if (meterFill && meterText) {
    if (subtotal >= shippingThreshold) {
      meterFill.style.width = '100%';
      meterText.innerHTML = '🎉 You have unlocked <strong>FREE Express Shipping!</strong>';
    } else {
      const remaining = shippingThreshold - subtotal;
      const pct = Math.min(100, Math.round((subtotal / shippingThreshold) * 100));
      meterFill.style.width = `${pct}%`;
      meterText.innerHTML = `Add <strong>$${remaining.toFixed(2)}</strong> more for FREE Express Shipping!`;
    }
  }

  if (!itemsContainer) return;

  if (state.cart.length === 0) {
    itemsContainer.innerHTML = `
      <div class="empty-cart-state">
        <div class="icon">📖</div>
        <h4>Your reading bag is empty</h4>
        <p>Explore our curated titles and uncover your next favorite read.</p>
        <button class="btn-primary" style="margin-top: 18px;" onclick="closeCart()">Start Exploring</button>
      </div>
    `;
    return;
  }

  itemsContainer.innerHTML = state.cart.map(item => `
    <div class="cart-item-row">
      <div class="cart-item-thumb ${item.cover_theme}">
        <span>${escapeHTML(item.title.substring(0, 18))}</span>
      </div>
      <div class="cart-item-details">
        <div>
          <div class="cart-item-title">${escapeHTML(item.title)}</div>
          <small style="color: var(--text-dim);">${escapeHTML(item.format || 'Hardcover')}</small>
        </div>
        <div class="cart-item-controls">
          <span class="cart-item-price">$${(item.price * item.quantity).toFixed(2)}</span>
          <div class="qty-stepper">
            <button class="qty-btn" onclick="updateCartQuantity(${item.id}, -1, '${item.format}')">−</button>
            <span class="qty-num">${item.quantity}</span>
            <button class="qty-btn" onclick="updateCartQuantity(${item.id}, 1, '${item.format}')">+</button>
          </div>
          <button class="remove-item-btn" onclick="removeCartItem(${item.id}, '${item.format}')" title="Remove item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18m-2 0v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6m3 0V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
          </button>
        </div>
      </div>
    </div>
  `).join('');
}

function openCart() {
  const backdrop = document.getElementById('cart-drawer-backdrop');
  if (backdrop) backdrop.classList.add('open');
}

function closeCart() {
  const backdrop = document.getElementById('cart-drawer-backdrop');
  if (backdrop) backdrop.classList.remove('open');
}

// Wishlist Functions
function toggleWishlist(bookId) {
  const book = state.books.find(b => b.id == bookId);
  if (!book) return;

  const idx = state.wishlist.findIndex(i => i.id == bookId);
  if (idx > -1) {
    state.wishlist.splice(idx, 1);
    showToast(`Removed "${book.title}" from wishlist`, '💔');
  } else {
    state.wishlist.push({
      id: book.id,
      title: book.title,
      author: book.author,
      price: book.price,
      cover_theme: book.cover_theme
    });
    showToast(`Saved "${book.title}" to wishlist!`, '❤️');
  }

  localStorage.setItem('lumina_wishlist', JSON.stringify(state.wishlist));
  updateWishlistUI();
  filterAndRenderBooks(); // Update heart icon states
}

function updateWishlistUI() {
  const badge = document.getElementById('wishlist-count-badge');
  if (badge) badge.textContent = state.wishlist.length;
}

function openWishlistModal() {
  const modal = document.getElementById('wishlist-modal');
  const listEl = document.getElementById('wishlist-modal-items');
  if (!modal || !listEl) return;

  if (state.wishlist.length === 0) {
    listEl.innerHTML = `
      <div style="text-align: center; padding: 40px 10px; color: var(--text-muted);">
        <div style="font-size: 2.5rem; margin-bottom: 10px;">🤍</div>
        <h4>Your wishlist is empty</h4>
        <p>Click the heart icon on any book to save it for later.</p>
      </div>
    `;
  } else {
    listEl.innerHTML = state.wishlist.map(item => `
      <div class="cart-item-row" style="margin-bottom: 12px;">
        <div class="cart-item-thumb ${item.cover_theme}">
          <span>${escapeHTML(item.title.substring(0, 16))}</span>
        </div>
        <div class="cart-item-details">
          <div class="cart-item-title">${escapeHTML(item.title)}</div>
          <div style="font-size: 0.8rem; color: var(--text-muted);">${escapeHTML(item.author)}</div>
          <div class="cart-item-controls" style="margin-top: 8px;">
            <span class="cart-item-price">$${Number(item.price).toFixed(2)}</span>
            <button class="btn-primary" style="padding: 6px 14px; font-size: 0.82rem;" onclick="addToCart(${item.id}, event); toggleWishlist(${item.id});">Move to Cart</button>
            <button class="remove-item-btn" onclick="toggleWishlist(${item.id}); openWishlistModal();">Remove</button>
          </div>
        </div>
      </div>
    `).join('');
  }

  modal.classList.add('open');
}

// Quick View Modal
function openQuickView(bookId) {
  const book = state.books.find(b => b.id == bookId);
  if (!book) return;

  const modal = document.getElementById('quickview-modal');
  const content = document.getElementById('quickview-content');
  if (!modal || !content) return;

  let selectedFormat = book.format || 'Hardcover';
  let quantity = 1;

  content.innerHTML = `
    <div class="quickview-layout">
      <div class="quickview-cover-col">
        <div class="book-cover large ${book.cover_theme || 'cyber-blue'}">
          <span class="cover-badge-tag">${escapeHTML(book.genre)}</span>
          <div class="cover-title">${escapeHTML(book.title)}</div>
          <div class="cover-author">${escapeHTML(book.author)}</div>
        </div>
        <span class="spotlight-badge" style="margin-top: 10px;">
          ${book.in_stock > 5 ? '● In Stock & Ready to Ship' : `● Only ${book.in_stock} Copies Left`}
        </span>
      </div>

      <div class="quickview-details-col">
        <span class="card-genre-pill">${escapeHTML(book.genre)}</span>
        <h2 style="font-family: var(--font-serif); font-size: 1.85rem; font-weight: 800; line-height: 1.25; margin-bottom: 6px;">
          ${escapeHTML(book.title)}
        </h2>
        <div style="font-size: 0.95rem; color: var(--accent-gold); font-weight: 600; margin-bottom: 12px;">
          By ${escapeHTML(book.author)}
        </div>

        <div class="card-rating-row" style="margin-bottom: 16px;">
          <span class="stars">★★★★★</span>
          <strong>${Number(book.rating).toFixed(1)} / 5.0</strong>
          <span style="color: var(--text-dim);">· ${book.reviews_count || 120} verified reader reviews</span>
        </div>

        <div class="price-tag" style="margin-bottom: 18px;">
          <span class="price-current" style="font-size: 1.8rem;">$${Number(book.price).toFixed(2)}</span>
          ${book.original_price ? `<span class="price-original" style="font-size: 1.1rem;">$${Number(book.original_price).toFixed(2)}</span>` : ''}
          ${book.original_price ? `<span class="savings-tag">SAVE ${Math.round((1 - book.price / book.original_price) * 100)}%</span>` : ''}
        </div>

        <p style="font-size: 0.95rem; color: var(--text-muted); line-height: 1.7; margin-bottom: 16px;">
          ${escapeHTML(book.description)}
        </p>

        <div>
          <label style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">SELECT FORMAT</label>
          <div class="format-pills" id="modal-format-group">
            <button type="button" class="format-pill active" onclick="setQuickViewFormat(this, 'Hardcover')">Hardcover</button>
            <button type="button" class="format-pill" onclick="setQuickViewFormat(this, 'Paperback')">Paperback</button>
            <button type="button" class="format-pill" onclick="setQuickViewFormat(this, 'eBook')">eBook (EPUB)</button>
            <button type="button" class="format-pill" onclick="setQuickViewFormat(this, 'Audiobook')">Audiobook</button>
          </div>
        </div>

        <div class="specs-grid">
          <div class="spec-cell"><small>Publisher</small><strong>${escapeHTML(book.publisher || 'Apex Press')}</strong></div>
          <div class="spec-cell"><small>Pages</small><strong>${book.pages || 380} Pages</strong></div>
          <div class="spec-cell"><small>ISBN-13</small><strong>${escapeHTML(book.isbn || '978-0134685991')}</strong></div>
          <div class="spec-cell"><small>Published</small><strong>${book.publish_year || 2024}</strong></div>
        </div>

        <div style="display: flex; gap: 14px; align-items: center; margin-top: 10px;">
          <button class="btn-primary" style="flex: 1;" id="modal-add-cart-btn" onclick="handleModalAddToCart(${book.id})">
            Add to Bag · $${Number(book.price).toFixed(2)}
          </button>
          <button class="btn-secondary" onclick="toggleWishlist(${book.id})" title="Save to Wishlist">
            ❤️ Wishlist
          </button>
        </div>
      </div>
    </div>
  `;

  modal.classList.add('open');
}

function setQuickViewFormat(btn, format) {
  document.querySelectorAll('#modal-format-group .format-pill').forEach(p => p.classList.remove('active'));
  btn.classList.add('active');
  window._qvCurrentFormat = format;
}

function handleModalAddToCart(bookId) {
  const format = window._qvCurrentFormat || 'Hardcover';
  addToCart(bookId, null, 1, format);
  closeModal('quickview-modal');
  openCart();
}

// Coupons
function applyCoupon() {
  const input = document.getElementById('coupon-code-input');
  if (!input) return;

  const code = input.value.trim().toUpperCase();
  if (code === 'READ20') {
    state.discountPercent = 0.20;
    state.couponCode = 'READ20';
    localStorage.setItem('lumina_coupon', 'READ20');
    showToast('Promo code applied: 20% OFF your entire order!', '🎉');
  } else if (code === 'READ15') {
    state.discountPercent = 0.15;
    state.couponCode = 'READ15';
    localStorage.setItem('lumina_coupon', 'READ15');
    showToast('Promo code applied: 15% OFF your entire order!', '🎉');
  } else {
    showToast('Invalid coupon code. Try READ20', '⚠️');
  }

  renderCart();
}

function applySavedCoupon() {
  if (state.couponCode === 'READ20') state.discountPercent = 0.20;
  if (state.couponCode === 'READ15') state.discountPercent = 0.15;
}

// Checkout Modal & Submission
function openCheckoutModal() {
  if (state.cart.length === 0) {
    showToast('Your cart is empty!', '⚠️');
    return;
  }
  const modal = document.getElementById('checkout-modal');
  if (modal) {
    updateCheckoutSummary();
    modal.classList.add('open');
  }
}

function updateCheckoutSummary() {
  const listEl = document.getElementById('checkout-summary-items');
  const totalEl = document.getElementById('checkout-summary-total');
  if (!listEl) return;

  const subtotal = state.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
  const discount = (subtotal * state.discountPercent);
  const shipping = subtotal >= 35 ? 0 : 4.99;
  const total = Math.max(0, subtotal - discount + shipping);

  listEl.innerHTML = state.cart.map(item => `
    <div style="display: flex; justify-content: space-between; font-size: 0.88rem; margin-bottom: 6px;">
      <span>${escapeHTML(item.title)} <small style="color: var(--text-dim);">× ${item.quantity}</small></span>
      <strong>$${(item.price * item.quantity).toFixed(2)}</strong>
    </div>
  `).join('');

  if (totalEl) totalEl.textContent = `$${total.toFixed(2)}`;
}

function selectPaymentMethod(card, method) {
  document.querySelectorAll('.payment-card-option').forEach(c => c.classList.remove('selected'));
  card.classList.add('selected');
  const input = document.getElementById('selected-payment-method');
  if (input) input.value = method;
}

function handleCheckoutSubmit(e) {
  e.preventDefault();
  const form = e.target;
  const submitBtn = document.getElementById('place-order-btn');

  if (submitBtn) {
    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Placing Order...';
  }

  const subtotal = state.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
  const discount = (subtotal * state.discountPercent);
  const shipping = subtotal >= 35 ? 0 : 4.99;
  const total = Math.max(0, subtotal - discount + shipping);

  const payload = {
    customer: {
      name: form.fullName.value.trim(),
      email: form.email.value.trim(),
      phone: form.phone.value.trim(),
      address: form.address.value.trim(),
      city: form.city.value.trim(),
      zip: form.zip.value.trim(),
      paymentMethod: document.getElementById('selected-payment-method').value || 'Cash on Delivery',
      subtotal: subtotal,
      discount: discount,
      shipping: shipping,
      total: total
    },
    items: state.cart
  };

  fetch('api.php?action=checkout', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      renderOrderSuccess(data.order_number, payload.customer);
      state.cart = [];
      saveCart();
      renderCart();
    } else {
      showToast(data.message || 'Error processing order', '⚠️');
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Complete Order';
      }
    }
  })
  .catch(() => {
    // Graceful offline simulated order for XAMPP
    const fakeOrderNumber = 'LUM-' + Math.floor(100000 + Math.random() * 900000);
    renderOrderSuccess(fakeOrderNumber, payload.customer);
    state.cart = [];
    saveCart();
    renderCart();
  });
}

function renderOrderSuccess(orderNumber, customer) {
  const container = document.getElementById('checkout-modal-body');
  if (!container) return;

  container.innerHTML = `
    <div class="order-success-box">
      <div class="success-badge-icon">✓</div>
      <h2 style="font-family: var(--font-serif); font-size: 2rem; margin-bottom: 8px;">Order Confirmed!</h2>
      <p style="color: var(--text-muted); margin-bottom: 24px;">
        Thank you, <strong>${escapeHTML(customer.name)}</strong>. Your curated literary package is being hand-wrapped and dispatched.
      </p>

      <div style="background: var(--bg-secondary); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 20px; max-width: 440px; margin: 0 auto 28px; text-align: left;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
          <span style="color: var(--text-dim); font-size: 0.85rem;">Order Number</span>
          <strong style="color: var(--accent-gold); font-family: monospace;">${orderNumber}</strong>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
          <span style="color: var(--text-dim); font-size: 0.85rem;">Estimated Delivery</span>
          <strong>2 - 3 Business Days</strong>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
          <span style="color: var(--text-dim); font-size: 0.85rem;">Payment Mode</span>
          <strong>${escapeHTML(customer.paymentMethod)}</strong>
        </div>
        <div style="display: flex; justify-content: space-between; border-top: 1px solid var(--border-subtle); padding-top: 8px; margin-top: 8px;">
          <span style="color: var(--text-dim); font-size: 0.85rem;">Total Paid</span>
          <strong style="font-size: 1.1rem; color: var(--text-main);">$${Number(customer.total).toFixed(2)}</strong>
        </div>
      </div>

      <div style="display: flex; justify-content: center; gap: 14px;">
        <button class="btn-primary" onclick="closeModal('checkout-modal'); location.reload();">Continue Browsing</button>
      </div>
    </div>
  `;
}

// Newsletter
function handleNewsletterSubmit(e) {
  e.preventDefault();
  const input = e.target.querySelector('input[type="email"]');
  if (!input || !input.value) return;

  const formData = new FormData();
  formData.append('email', input.value.trim());

  fetch('api.php?action=newsletter', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    showToast(data.message || 'Subscribed successfully! Use code READ15', '✨');
    input.value = '';
  })
  .catch(() => {
    showToast('Subscribed! Welcome to Lumina Reader Circle. Use code READ15', '✨');
    input.value = '';
  });
}

// Generic Modal Controls
function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) modal.classList.remove('open');
}

// Toast Notifications
function showToast(message, icon = '📖') {
  let container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container';
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  toast.className = 'toast';
  toast.innerHTML = `
    <span class="toast-icon">${icon}</span>
    <span>${escapeHTML(message)}</span>
  `;

  container.appendChild(toast);
  setTimeout(() => toast.classList.add('show'), 20);

  setTimeout(() => {
    toast.classList.remove('show');
    setTimeout(() => toast.remove(), 300);
  }, 3200);
}

// Utility Helpers
function escapeHTML(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}
