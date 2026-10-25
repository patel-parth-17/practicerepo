<?php
require_once __DIR__ . '/admin_auth.php';
require_once __DIR__ . '/db.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: admin.php');
    exit;
}
if (isset($_SESSION['user_id'])) {
    header('Location: user.php');
    exit;
}

$role = requestString($_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $_GET, 'role');
$role = in_array($role, ['admin', 'user'], true) ? $role : 'user';
$mode = 'login';
$error = '';
$email = requestString($_POST, 'email');
$fullName = requestString($_POST, 'full_name');
$intent = requestString($_POST, 'intent');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mode = $intent === 'signup' && $role === 'user' ? 'signup' : 'login';
    if (!verifyCsrfToken(requestString($_POST, 'csrf_token'))) {
        $error = 'Your session expired. Refresh the page and try again.';
    } else {
        $email = strtolower(trim($email));
        $password = requestString($_POST, 'password');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
            $error = 'Enter a valid email address.';
        } elseif ($mode === 'signup') {
            $fullName = trim($fullName);
            $confirmPassword = requestString($_POST, 'confirm_password');
            if (mb_strlen($fullName) < 2 || mb_strlen($fullName) > 120) {
                $error = 'Enter your name (between 2 and 120 characters).';
            } elseif (strlen($password) < 8 || strlen($password) > 4096) {
                $error = 'Choose a password between 8 and 4096 characters.';
            } elseif ($password !== $confirmPassword) {
                $error = 'The passwords do not match.';
            } else {
                try {
                    $adminCheck = $conn->prepare('SELECT 1 FROM admin_users WHERE email = ? LIMIT 1');
                    $adminCheck->execute([$email]);
                    if ($adminCheck->fetchColumn()) {
                        $error = 'That email is already in use. Please sign in or use another email.';
                    } else {
                        $statement = $conn->prepare(
                            'INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, ?)'
                        );
                        $statement->execute([$fullName, $email, password_hash($password, PASSWORD_DEFAULT), 'user']);
                        session_regenerate_id(true);
                        unset($_SESSION['csrf_token']);
                        $_SESSION['user_id'] = (int) $conn->lastInsertId();
                        $_SESSION['user_name'] = $fullName;
                        $_SESSION['user_email'] = $email;
                        header('Location: user.php');
                        exit;
                    }
                } catch (PDOException $exception) {
                    if ($exception->getCode() === '23000') {
                        $error = 'An account with that email already exists. Please sign in instead.';
                    } else {
                        error_log($exception->getMessage());
                        $error = 'Account creation is temporarily unavailable. Please try again later.';
                    }
                }
            }
        } elseif ($password === '') {
            $error = 'Enter your password.';
        } else {
            try {
                if ($role === 'admin') {
                    $statement = $conn->prepare(
                        'SELECT id, email, password_hash FROM admin_users WHERE email = ? LIMIT 1'
                    );
                } else {
                    $statement = $conn->prepare(
                        'SELECT id, full_name, email, password_hash FROM users WHERE email = ? AND role = ? LIMIT 1'
                    );
                }
                $statement->execute($role === 'admin' ? [$email] : [$email, 'user']);
                $account = $statement->fetch();

                if ($account && password_verify($password, $account['password_hash'])) {
                    session_regenerate_id(true);
                    unset($_SESSION['csrf_token']);
                    if ($role === 'admin') {
                        $_SESSION['admin_id'] = (int) $account['id'];
                        $_SESSION['admin_email'] = $account['email'];
                        header('Location: admin.php');
                    } else {
                        $_SESSION['user_id'] = (int) $account['id'];
                        $_SESSION['user_name'] = $account['full_name'];
                        $_SESSION['user_email'] = $account['email'];
                        header('Location: user.php');
                    }
                    exit;
                }
                $error = 'The email or password is incorrect.';
            } catch (PDOException $exception) {
                error_log($exception->getMessage());
                $error = 'Sign in is temporarily unavailable. Please try again later.';
            }
        }
    }
}

$isUser = $role === 'user';
$isSignup = $mode === 'signup';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#f6f7fb">
  <title><?= $isUser ? ($isSignup ? 'Create an account' : 'Customer sign in') : 'Admin sign in' ?> | Papertrail Books</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="index.css">
</head>
<body class="login-page">
  <main class="login-layout">
    <section class="login-story" aria-label="Papertrail Books">
      <a class="book-brand book-brand-light" href="index.php"><span class="brand-icon" aria-hidden="true">P</span><span>papertrail<span class="brand-period">.</span></span></a>
      <div class="story-copy">
        <span class="story-kicker"><span class="status-dot"></span> THE BOOKSHOP BACK OFFICE</span>
        <h1>A good story<br>starts <em>here.</em></h1>
        <p>Keep your shelves organized, your stock in view, and your independent bookshop moving forward.</p>
        <div class="story-note"><span class="note-mark" aria-hidden="true">“</span><span>Every book deserves to find its reader.</span></div>
      </div>
      <div class="story-footer"><span>YOUR BOOKSHOP, BEAUTIFULLY ORGANIZED</span><span>EST. 2026</span></div>
    </section>
    <section class="login-content">
      <div class="login-card">
        <div class="login-mobile-brand"><a class="book-brand" href="index.php"><span class="brand-icon" aria-hidden="true">P</span><span>papertrail<span class="brand-period">.</span></span></a></div>
        <span class="section-kicker" id="login-kicker"><?= $isUser ? 'WELCOME TO THE BOOKSHOP' : 'SECURE STAFF ACCESS' ?></span>
        <h2 id="login-title"><?= $isSignup ? 'Create your account' : 'Welcome back' ?></h2>
        <p class="login-intro" id="login-intro"><?= $isSignup ? 'Sign up to explore the books on our shelves.' : ($isUser ? 'Sign in to discover your next favorite book.' : 'Sign in to manage your bookstore inventory.') ?></p>

        <fieldset class="account-role">
          <legend id="role-legend"><?= $isSignup ? 'Create a customer account' : 'Continue as' ?></legend>
          <div class="account-role-options">
            <label class="account-role-option <?= $isUser ? 'selected' : '' ?>">
              <input type="radio" name="account-role" value="user" form="auth-form" <?= $isUser ? 'checked' : '' ?>>
              <span class="account-role-icon" aria-hidden="true">♧</span>
              <span><strong>Reader</strong><small>Browse the bookstore</small></span>
            </label>
            <label class="account-role-option <?= !$isUser ? 'selected' : '' ?>">
              <input type="radio" name="account-role" value="admin" form="auth-form" <?= !$isUser ? 'checked' : '' ?>>
              <span class="account-role-icon staff-icon" aria-hidden="true">▣</span>
              <span><strong>Admin</strong><small>Manage the bookstore</small></span>
            </label>
          </div>
        </fieldset>

        <div class="account-mode" aria-label="Account action" <?= $isUser ? '' : 'hidden' ?>>
          <button class="account-mode-button <?= !$isSignup ? 'active' : '' ?>" type="button" data-mode="login" aria-pressed="<?= !$isSignup ? 'true' : 'false' ?>">Sign in</button>
          <button class="account-mode-button <?= $isSignup ? 'active' : '' ?>" type="button" data-mode="signup" aria-pressed="<?= $isSignup ? 'true' : 'false' ?>">Create account</button>
        </div>

        <?php if ($error !== ''): ?><p class="login-error" role="alert"><?= e($error) ?></p><?php endif; ?>
        <form method="post" action="index.php" class="login-form" id="auth-form">
          <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
          <input type="hidden" name="role" id="selected-role" value="<?= e($role) ?>">
          <input type="hidden" name="intent" id="selected-intent" value="<?= $isSignup ? 'signup' : 'login' ?>">
          <label for="email">Email address</label>
          <input id="email" name="email" type="email" autocomplete="username" placeholder="<?= $isUser ? 'you@example.com' : 'admin@yourbookshop.com' ?>" value="<?= e($email) ?>" maxlength="190" required>
          <div class="signup-fields" <?= $isSignup ? '' : 'hidden' ?>>
            <label for="full-name">Your name</label>
            <input id="full-name" name="full_name" type="text" autocomplete="name" placeholder="Your full name" value="<?= e($fullName) ?>" maxlength="120" <?= $isSignup ? 'required' : '' ?>>
          </div>
          <label for="password">Password</label>
          <input id="password" name="password" type="password" autocomplete="<?= $isSignup ? 'new-password' : 'current-password' ?>" placeholder="<?= $isSignup ? 'At least 8 characters' : 'Enter your password' ?>" <?= $isSignup ? 'minlength="8"' : '' ?> required>
          <div class="signup-fields" <?= $isSignup ? '' : 'hidden' ?>>
            <label for="confirm-password">Confirm password</label>
            <input id="confirm-password" name="confirm_password" type="password" autocomplete="new-password" placeholder="Enter your password again" <?= $isSignup ? 'required' : '' ?>>
          </div>
          <button class="primary-button login-submit" type="submit"><span id="submit-label"><?= $isSignup ? 'Create your account' : ($isUser ? 'Sign in to your account' : 'Sign in to your dashboard') ?></span><span aria-hidden="true">→</span></button>
        </form>
        <p class="login-footnote"><span aria-hidden="true">▣</span> <?= $isUser ? 'Your reader account keeps your bookstore experience personal.' : 'Admin accounts are created securely by your shop owner.' ?></p>
      </div>
      <footer class="login-copyright">© <?= date('Y') ?> Papertrail Books <span>·</span> Made for book people.</footer>
    </section>
  </main>
  <script>
    const selectedRole = document.getElementById('selected-role');
    const selectedIntent = document.getElementById('selected-intent');
    const signupFields = document.querySelectorAll('.signup-fields');
    const fullNameInput = document.getElementById('full-name');
    const confirmInput = document.getElementById('confirm-password');
    const submitLabel = document.getElementById('submit-label');
    const userRoleRadio = document.querySelector('input[name="account-role"][value="user"]');
    const adminRoleRadio = document.querySelector('input[name="account-role"][value="admin"]');

    function setMode(mode) {
      const signup = mode === 'signup' && selectedRole.value === 'user';
      selectedIntent.value = signup ? 'signup' : 'login';
      signupFields.forEach((field) => { field.hidden = !signup; });
      fullNameInput.required = signup;
      confirmInput.required = signup;
      document.getElementById('password').autocomplete = signup ? 'new-password' : 'current-password';
      submitLabel.textContent = signup ? 'Create your account' : (selectedRole.value === 'user' ? 'Sign in to your account' : 'Sign in to your dashboard');
      document.getElementById('login-kicker').textContent = signup
        ? 'WELCOME TO THE BOOKSHOP'
        : (selectedRole.value === 'user' ? 'WELCOME TO THE BOOKSHOP' : 'SECURE STAFF ACCESS');
      document.getElementById('login-title').textContent = signup ? 'Create your account' : 'Welcome back';
      document.getElementById('login-intro').textContent = signup
        ? 'Sign up to explore the books on our shelves.'
        : (selectedRole.value === 'user' ? 'Sign in to discover your next favorite book.' : 'Sign in to manage your bookstore inventory.');
      document.getElementById('role-legend').textContent = signup ? 'Create a customer account' : 'Continue as';
      document.querySelectorAll('.account-mode-button').forEach((button) => {
        const active = button.dataset.mode === selectedIntent.value;
        button.classList.toggle('active', active);
        button.setAttribute('aria-pressed', String(active));
      });
      const url = new URL(window.location.href);
      url.search = '';
      window.history.replaceState({}, '', url);
    }

    userRoleRadio.addEventListener('change', () => {
      if (!userRoleRadio.checked) return;
      selectedRole.value = 'user';
      document.querySelectorAll('.account-role-option').forEach((card) => card.classList.remove('selected'));
      userRoleRadio.closest('.account-role-option').classList.add('selected');
      document.querySelector('.account-mode').hidden = false;
      setMode(selectedIntent.value);
    });
    adminRoleRadio.addEventListener('change', () => {
      if (!adminRoleRadio.checked) return;
      selectedRole.value = 'admin';
      document.querySelectorAll('.account-role-option').forEach((card) => card.classList.remove('selected'));
      adminRoleRadio.closest('.account-role-option').classList.add('selected');
      document.querySelector('.account-mode').hidden = true;
      setMode('login');
    });
    document.querySelectorAll('.account-mode-button').forEach((button) => {
      button.addEventListener('click', () => setMode(button.dataset.mode));
    });
  </script>
</body>
</html>
