<?php
require_once __DIR__ . '/admin_auth.php';
requireAdmin();
require_once __DIR__ . '/db.php';

$error = '';
$formBook = [
    'id' => '',
    'title' => '',
    'author' => '',
    'isbn' => '',
    'category' => '',
    'price' => '',
    'stock' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Your session expired. Refresh the page and try again.';
    } elseif (($_POST['action'] ?? '') === 'logout') {
        session_unset();
        session_destroy();
        setcookie(session_name(), '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        header('Location: index.php');
        exit;
    } elseif (($_POST['action'] ?? '') === 'delete') {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($id === false) {
            $error = 'The selected book could not be identified.';
        } else {
            $statement = $conn->prepare('DELETE FROM books WHERE id = ?');
            $statement->execute([$id]);
            $_SESSION['flash_message'] = $statement->rowCount() ? 'Book removed from your inventory.' : 'That book was already removed.';
            header('Location: admin.php');
            exit;
        }
    } elseif (($_POST['action'] ?? '') === 'save') {
        foreach ($formBook as $key => $value) {
            $formBook[$key] = trim(requestString($_POST, $key));
        }
        $isbn = strtoupper(preg_replace('/[\s-]+/', '', $formBook['isbn']));
        $formBook['isbn'] = $isbn;
        $id = $formBook['id'] === '' ? null : filter_var($formBook['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $price = filter_var($formBook['price'], FILTER_VALIDATE_FLOAT);
        $stock = filter_var($formBook['stock'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);

        if (($formBook['id'] !== '' && $id === false)
            || $formBook['title'] === ''
            || mb_strlen($formBook['title']) > 200
            || $formBook['author'] === ''
            || mb_strlen($formBook['author']) > 150
            || $formBook['category'] === ''
            || mb_strlen($formBook['category']) > 80
            || $price === false
            || $price < 0
            || $price > 99999999.99
            || $stock === false
            || ($isbn !== '' && !preg_match('/^(?:\d{9}[\dX]|\d{13})$/', $isbn))) {
            $error = 'Check the book details: title, author, category, price and stock are required; ISBN must be 10 or 13 digits.';
        } else {
            try {
                $isbnValue = $isbn === '' ? null : $isbn;
                if ($id === null) {
                    $statement = $conn->prepare(
                        'INSERT INTO books (title, author, isbn, category, price, stock) VALUES (?, ?, ?, ?, ?, ?)'
                    );
                    $statement->execute([$formBook['title'], $formBook['author'], $isbnValue, $formBook['category'], $price, $stock]);
                } else {
                    $statement = $conn->prepare(
                        'UPDATE books SET title = ?, author = ?, isbn = ?, category = ?, price = ?, stock = ? WHERE id = ?'
                    );
                    $statement->execute([$formBook['title'], $formBook['author'], $isbnValue, $formBook['category'], $price, $stock, $id]);
                    if ($statement->rowCount() === 0) {
                        $exists = $conn->prepare('SELECT 1 FROM books WHERE id = ?');
                        $exists->execute([$id]);
                        if (!$exists->fetchColumn()) {
                            $error = 'That book no longer exists.';
                        }
                    }
                }
                if ($error === '') {
                    $_SESSION['flash_message'] = $id === null ? 'Book added to your inventory.' : 'Book details saved.';
                    header('Location: admin.php');
                    exit;
                }
            } catch (PDOException $exception) {
                if ($exception->getCode() === '23000') {
                    $error = 'That ISBN is already assigned to another book.';
                } else {
                    error_log($exception->getMessage());
                    $error = 'The book could not be saved. Please try again.';
                }
            }
        }
    }
}

$search = trim(requestString($_GET, 'q'));
$stats = $conn->query(
    'SELECT COUNT(*) AS title_count, COALESCE(SUM(stock), 0) AS stock_count, '
    . 'COALESCE(SUM(CASE WHEN stock < 5 THEN 1 ELSE 0 END), 0) AS low_stock_count FROM books'
)->fetch();

$categories = $conn->query('SELECT DISTINCT category FROM books ORDER BY category')->fetchAll(PDO::FETCH_COLUMN);
$sql = 'SELECT id, title, author, isbn, category, price, stock, updated_at FROM books';
$conditions = [];
$parameters = [];
if ($search !== '') {
    $conditions[] = '(title LIKE ? OR author LIKE ? OR isbn LIKE ? OR category LIKE ?)';
    $term = '%' . $search . '%';
    array_push($parameters, $term, $term, $term, $term);
}
if ($conditions) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}
$sql .= ' ORDER BY updated_at DESC, title ASC';
$statement = $conn->prepare($sql);
$statement->execute($parameters);
$books = $statement->fetchAll();
$flash = $_SESSION['flash_message'] ?? '';
unset($_SESSION['flash_message']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save' && $error !== '') {
    $showModal = true;
} else {
    $showModal = false;
    $editId = filter_var($_GET['edit'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($editId !== false && $editId !== null) {
        $editStatement = $conn->prepare('SELECT id, title, author, isbn, category, price, stock FROM books WHERE id = ?');
        $editStatement->execute([$editId]);
        $existingBook = $editStatement->fetch();
        if ($existingBook) {
            $formBook = $existingBook;
            $showModal = true;
        } else {
            $error = 'That book could not be found.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#f6f7fb">
  <title>Bookshop dashboard | Papertrail Books</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="index.css">
</head>
<body class="admin-page">
  <div class="admin-layout">
    <aside class="sidebar">
      <a class="book-brand" href="admin.php"><span class="brand-icon" aria-hidden="true">P</span><span>papertrail<span class="brand-period">.</span></span></a>
      <div class="store-switcher"><span class="store-avatar" aria-hidden="true">B</span><span><strong>Bookshop</strong><small>Admin workspace</small></span><span class="switcher-caret" aria-hidden="true">⌄</span></div>
      <span class="nav-label">WORKSPACE</span>
      <nav class="side-nav" aria-label="Admin navigation">
        <a class="nav-item active" href="#overview"><span aria-hidden="true">▦</span> Overview</a>
        <a class="nav-item" href="#inventory"><span aria-hidden="true">▤</span> Book inventory <span class="nav-count"><?= (int) $stats['title_count'] ?></span></a>
      </nav>
      <div class="sidebar-bottom">
        <div class="sidebar-help"><span class="help-icon" aria-hidden="true">?</span><span><strong>Need a hand?</strong><small>Your bookshop toolkit</small></span><span aria-hidden="true">↗</span></div>
        <div class="admin-profile"><span class="profile-avatar" aria-hidden="true"><?= e(strtoupper(substr($_SESSION['admin_email'], 0, 1))) ?></span><span class="profile-copy"><strong><?= e($_SESSION['admin_email']) ?></strong><small>Administrator</small></span>
          <form method="post" action="admin.php" class="logout-form">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
            <button type="submit" name="action" value="logout" class="logout-button" aria-label="Sign out" title="Sign out">↗</button>
          </form>
        </div>
      </div>
    </aside>

    <main class="dashboard-main">
      <header class="topbar">
        <div class="breadcrumb">Workspace <span>/</span> <strong>Overview</strong></div>
        <div class="topbar-right"><span class="today-label"><?= date('D, M j, Y') ?></span><span class="topbar-divider"></span><span class="profile-avatar top-avatar" aria-hidden="true"><?= e(strtoupper(substr($_SESSION['admin_email'], 0, 1))) ?></span></div>
      </header>
      <div class="dashboard-content">
        <section class="welcome-row" id="overview">
          <div><span class="section-kicker">YOUR SHOP AT A GLANCE</span><h1>Good books. Better organized.</h1><p>Here's what's happening in your bookstore today.</p></div>
          <button type="button" class="primary-button add-book-button" data-open-book><span aria-hidden="true">＋</span> Add a book</button>
        </section>

        <?php if ($flash !== ''): ?><div class="notice success-notice" role="status"><?= e($flash) ?></div><?php endif; ?>
        <?php if ($error !== ''): ?><div class="notice error-notice" role="alert"><?= e($error) ?></div><?php endif; ?>

        <section class="stats-grid" aria-label="Inventory summary">
          <article class="stat-card"><div class="stat-head"><span class="stat-icon purple-icon" aria-hidden="true">▤</span><span class="stat-caption">BOOK TITLES</span></div><strong class="stat-value"><?= number_format((int) $stats['title_count']) ?></strong><span class="stat-foot">Unique titles in your catalog</span></article>
          <article class="stat-card"><div class="stat-head"><span class="stat-icon blue-icon" aria-hidden="true">▣</span><span class="stat-caption">BOOKS IN STOCK</span></div><strong class="stat-value"><?= number_format((int) $stats['stock_count']) ?></strong><span class="stat-foot">Total copies on your shelves</span></article>
          <article class="stat-card"><div class="stat-head"><span class="stat-icon amber-icon" aria-hidden="true">⌁</span><span class="stat-caption">RUNNING LOW</span></div><strong class="stat-value"><?= number_format((int) $stats['low_stock_count']) ?></strong><span class="stat-foot">Titles with fewer than 5 copies</span></article>
          <article class="stat-card welcome-stat"><span class="welcome-stat-spark" aria-hidden="true">✳</span><span class="stat-caption">A NOTE FOR TODAY</span><strong>Make space<br>for a new story.</strong><span class="stat-foot">Your next reader is looking for a book.</span></article>
        </section>

        <section class="inventory-panel" id="inventory">
          <div class="inventory-heading"><div><span class="section-kicker">THE SHELVES</span><h2>Book inventory</h2><p>Manage titles, pricing and stock levels.</p></div>
            <form method="get" action="admin.php" class="inventory-search">
              <label class="search-field"><span aria-hidden="true">⌕</span><input type="search" name="q" placeholder="Search books, authors..." value="<?= e($search) ?>" aria-label="Search books"></label>
              <?php if ($search !== ''): ?><a class="clear-search" href="admin.php">Clear</a><?php endif; ?>
            </form>
          </div>
          <div class="table-scroll">
            <table class="book-table">
              <thead><tr><th>BOOK</th><th>ISBN</th><th>CATEGORY</th><th>PRICE</th><th>IN STOCK</th><th><span class="visually-hidden">Actions</span></th></tr></thead>
              <tbody>
                <?php if (!$books): ?>
                  <tr><td colspan="6" class="empty-state"><span aria-hidden="true">▤</span><strong><?= $search !== '' ? 'No books match your search' : 'Your shelves are waiting' ?></strong><small><?= $search !== '' ? 'Try a different title, author or category.' : 'Add your first book to start building your catalog.' ?></small><?php if ($search === ''): ?><button type="button" class="text-button" data-open-book>Add your first book →</button><?php endif; ?></td></tr>
                <?php else: ?>
                  <?php foreach ($books as $book): ?>
                    <tr>
                      <td><div class="book-cell"><span class="book-cover" aria-hidden="true"><?= e(strtoupper(substr($book['title'], 0, 1))) ?></span><span><strong><?= e($book['title']) ?></strong><small>by <?= e($book['author']) ?></small></span></div></td>
                      <td class="isbn-cell"><?= e($book['isbn'] ?: '—') ?></td>
                      <td><span class="category-pill"><?= e($book['category']) ?></span></td>
                      <td class="price-cell">$<?= number_format((float) $book['price'], 2) ?></td>
                      <td><span class="stock-pill <?= (int) $book['stock'] < 5 ? 'stock-low' : 'stock-good' ?>"><span></span><?= (int) $book['stock'] ?> <?= (int) $book['stock'] === 1 ? 'copy' : 'copies' ?></span></td>
                      <td class="row-actions"><a class="edit-link" href="admin.php?edit=<?= (int) $book['id'] ?>" aria-label="Edit <?= e($book['title']) ?>">Edit</a><form method="post" action="admin.php" class="delete-form" onsubmit="return confirm('Remove this book from inventory?');"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $book['id'] ?>"><button class="delete-link" type="submit" aria-label="Delete <?= e($book['title']) ?>">Delete</button></form></td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          <div class="table-footer"><span>Showing <strong><?= count($books) ?></strong> <?= count($books) === 1 ? 'book' : 'books' ?><?= $search !== '' ? ' matching your search' : ' in your catalog' ?></span><span>Stock alerts appear below 5 copies</span></div>
        </section>
        <footer class="dashboard-footer">Made with care for independent bookshops <span>·</span> Papertrail Books</footer>
      </div>
    </main>
  </div>

  <div class="modal-backdrop" id="book-modal" <?= $showModal ? '' : 'hidden' ?>>
    <section class="book-modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
      <div class="modal-heading"><div><span class="section-kicker">YOUR CATALOG</span><h2 id="modal-title"><?= $formBook['id'] !== '' ? 'Edit book details' : 'Add a new book' ?></h2><p>Add the details that help readers find their next favorite.</p></div><button type="button" class="modal-close" data-close-book aria-label="Close">×</button></div>
      <form method="post" action="admin.php" class="book-form">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= e($formBook['id']) ?>">
        <div class="form-grid">
          <label class="form-field full-field">Book title<input name="title" maxlength="200" value="<?= e($formBook['title']) ?>" placeholder="e.g. The Midnight Library" required></label>
          <label class="form-field">Author<input name="author" maxlength="150" value="<?= e($formBook['author']) ?>" placeholder="Author name" required></label>
          <label class="form-field">Category<input name="category" maxlength="80" list="category-options" value="<?= e($formBook['category']) ?>" placeholder="e.g. Fiction" required><datalist id="category-options"><?php foreach ($categories as $category): ?><option value="<?= e($category) ?>"><?php endforeach; ?></datalist></label>
          <label class="form-field">ISBN <span class="optional-label">Optional</span><input name="isbn" maxlength="20" value="<?= e($formBook['isbn']) ?>" placeholder="10 or 13 digits"></label>
          <label class="form-field">Price (USD)<input name="price" type="number" min="0" max="99999999.99" step="0.01" value="<?= e($formBook['price']) ?>" placeholder="0.00" required></label>
          <label class="form-field">Copies in stock<input name="stock" type="number" min="0" step="1" value="<?= e($formBook['stock']) ?>" placeholder="0" required></label>
        </div>
        <div class="modal-actions"><button type="button" class="secondary-button" data-close-book>Cancel</button><button type="submit" class="primary-button"><?= $formBook['id'] !== '' ? 'Save changes' : 'Add book to inventory' ?></button></div>
      </form>
    </section>
  </div>
  <script>
    const bookModal = document.getElementById('book-modal');
    const firstBookInput = bookModal.querySelector('input[name="title"]');
    document.querySelectorAll('[data-open-book]').forEach((button) => button.addEventListener('click', () => {
      bookModal.hidden = false;
      firstBookInput.focus();
    }));
    document.querySelectorAll('[data-close-book]').forEach((button) => button.addEventListener('click', () => {
      bookModal.hidden = true;
    }));
    bookModal.addEventListener('click', (event) => {
      if (event.target === bookModal) bookModal.hidden = true;
    });
    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') bookModal.hidden = true;
    });
  </script>
</body>
</html>
