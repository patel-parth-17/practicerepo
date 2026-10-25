<?php
require_once __DIR__ . '/admin_auth.php';
requireUser();
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken(requestString($_POST, 'csrf_token'))) {
        http_response_code(400);
        exit('Your session expired. Refresh the page and try again.');
    }
    if (requestString($_POST, 'action') === 'logout') {
        session_unset();
        session_destroy();
        setcookie(session_name(), '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        header('Location: index.php?role=user');
        exit;
    }
}

$search = trim(requestString($_GET, 'q'));
$sql = 'SELECT id, title, author, category, price, stock FROM books';
$parameters = [];
if ($search !== '') {
    $sql .= ' WHERE title LIKE ? OR author LIKE ? OR category LIKE ?';
    $term = '%' . $search . '%';
    array_push($parameters, $term, $term, $term);
}
$sql .= ' ORDER BY title ASC';
$statement = $conn->prepare($sql);
$statement->execute($parameters);
$books = $statement->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#f6f7fb">
  <title>Discover books | Papertrail Books</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="index.css">
</head>
<body class="reader-page">
  <header class="reader-header">
    <a class="book-brand" href="user.php"><span class="brand-icon" aria-hidden="true">P</span><span>papertrail<span class="brand-period">.</span></span></a>
    <nav class="reader-nav" aria-label="Reader account">
      <span>Welcome, <?= e($_SESSION['user_name']) ?></span>
      <form method="post" action="user.php">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <input type="hidden" name="action" value="logout">
        <button class="reader-signout" type="submit">Sign out</button>
      </form>
    </nav>
  </header>
  <main class="reader-content">
    <section class="reader-hero">
      <span class="section-kicker">A LITTLE SOMETHING TO GET LOST IN</span>
      <h1>Your next favorite<br>is <em>on these shelves.</em></h1>
      <p>Explore our bookstore and find a story worth bringing home.</p>
      <form class="reader-search" action="user.php" method="get">
        <label for="book-search" class="visually-hidden">Search books</label>
        <input id="book-search" type="search" name="q" value="<?= e($search) ?>" placeholder="Search by book, author or category">
        <button type="submit">Find a book <span aria-hidden="true">→</span></button>
      </form>
    </section>
    <section class="reader-shelves">
      <div class="reader-section-heading">
        <div><span class="section-kicker">THE BOOKSHOP</span><h2><?= $search !== '' ? 'Your search results' : 'Browse the shelves' ?></h2></div>
        <span class="reader-book-count"><?= count($books) ?> <?= count($books) === 1 ? 'book' : 'books' ?></span>
      </div>
      <?php if (!$books): ?>
        <div class="reader-empty"><span aria-hidden="true">▤</span><strong><?= $search !== '' ? 'No books found just yet.' : 'Our shelves are being arranged.' ?></strong><p><?= $search !== '' ? 'Try another search to discover more titles.' : 'Please check back soon for new books.' ?></p></div>
      <?php else: ?>
        <div class="reader-book-grid">
          <?php foreach ($books as $book): ?>
            <article class="reader-book-card">
              <div class="reader-book-cover" aria-hidden="true"><span><?= e(strtoupper(substr($book['title'], 0, 1))) ?></span><small>PAPERTRAIL BOOKS</small></div>
              <span class="reader-category"><?= e($book['category']) ?></span>
              <h3><?= e($book['title']) ?></h3>
              <p class="reader-author">by <?= e($book['author']) ?></p>
              <div class="reader-book-bottom"><strong>$<?= number_format((float) $book['price'], 2) ?></strong><span class="<?= (int) $book['stock'] > 0 ? 'reader-in-stock' : 'reader-out-of-stock' ?>"><?= (int) $book['stock'] > 0 ? 'In stock' : 'Currently out of stock' ?></span></div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
    <footer class="reader-footer">A good story starts here. <span>·</span> Papertrail Books</footer>
  </main>
</body>
</html>
