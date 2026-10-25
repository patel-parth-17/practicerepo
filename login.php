<?php
session_start();
$login_message = '';
$login_status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'user';
    $mode = $_POST['auth_mode'] ?? 'login';
    $name = trim($_POST['name'] ?? '');

    if (!empty($email)) {
        $_SESSION['user'] = [
            'name' => !empty($name) ? $name : ($role === 'admin' ? 'Admin Manager' : 'Avid Reader'),
            'email' => $email,
            'role' => $role
        ];
        header("Location: index.php?auth_success=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="theme-color" content="#0b0f19" />
  <title>Lumina Books — Reader Sign In & Account</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="index.css" />
  <style>
    :root {
      --bg-dark: #0b0f19;
      --card-bg: rgba(23, 30, 48, 0.7);
      --accent-gold: #f59e0b;
      --accent-glow: #fbbf24;
    }
    .back-store-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: #94a3b8;
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 500;
      margin-bottom: 24px;
      transition: color 0.2s ease;
    }
    .back-store-btn:hover {
      color: #f8fafc;
    }
    .brand-mark {
      background: linear-gradient(135deg, #f59e0b, #d97706);
    }
  </style>
</head>
<body>
  <main class="page-shell">
    <section class="visual-panel" aria-label="Welcome">
      <a class="brand" href="index.php" aria-label="Lumina Books home">
        <span class="brand-mark"><span></span><span></span><span></span><span></span></span>
        <span>LUMINA<span class="brand-dot">.</span></span>
      </a>

      <div class="visual-copy">
        <span class="eyebrow"><span class="live-dot"></span> CURATED READING SANCTUARY</span>
        <h1>Feed your mind with<br><em>extraordinary stories.</em></h1>
        <p>Unlock members-only book clubs, personalized reading tracks, early access to signed collector editions, and free express delivery.</p>
      </div>

      <div class="preview-card" aria-hidden="true">
        <div class="preview-top">
          <div class="mini-avatar">📚</div>
          <div><strong>Reader Club Pass</strong><small>Active Tier Member</small></div>
          <span class="preview-status">● Verified</span>
        </div>
        <div class="preview-divider"></div>
        <div class="preview-stats">
          <div><small>BOOKS READ</small><strong>28 <span>↗</span></strong></div>
          <div><small>CLUB CREDITS</small><strong>$45.00 <span class="muted-number">USD</span></strong></div>
          <div class="stat-visual"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>
        </div>
      </div>

      <div class="visual-footer">
        <div class="avatar-stack" aria-hidden="true"><span>📖</span><span>✨</span><span>🌟</span><span>+</span></div>
        <p><strong>Over 45,000+ passionate readers.</strong><br>Independent bookstore powered by XAMPP.</p>
        <span class="footer-sparkle">✳</span>
      </div>
    </section>

    <section class="form-panel">
      <div>
        <a href="index.php" class="back-store-btn">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
          Return to Book Store
        </a>
      </div>

      <div class="mobile-brand brand">
        <span class="brand-mark"><span></span><span></span><span></span><span></span></span>
        <span>LUMINA<span class="brand-dot">.</span></span>
      </div>

      <div class="auth-wrap">
        <div class="form-heading">
          <span class="eyebrow form-eyebrow">READER & ADMIN PORTAL</span>
          <h2 id="form-title">Welcome back</h2>
          <p id="form-subtitle">Enter your details to access your bookstore account.</p>
        </div>

        <div class="mode-switch" role="tablist" aria-label="Account action">
          <button class="mode-btn active" id="login-tab" type="button" role="tab" aria-selected="true">Sign in</button>
          <button class="mode-btn" id="signup-tab" type="button" role="tab" aria-selected="false">Create account</button>
        </div>

        <form id="auth-form" method="POST" action="login.php" novalidate>
          <input type="hidden" name="auth_mode" id="auth_mode_input" value="login">

          <fieldset class="role-field">
            <legend>Access as</legend>
            <div class="role-options">
              <label class="role-option selected" id="user-role-card">
                <input type="radio" name="role" value="user" checked>
                <span class="role-icon user-icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.4"/><path d="M5.5 20c.5-3.4 2.7-5.2 6.5-5.2s6 1.8 6.5 5.2"/></svg>
                </span>
                <span class="role-text"><strong>Reader</strong><small>Browse, buy & review</small></span>
                <span class="radio-dot"></span>
              </label>
              <label class="role-option" id="admin-role-card">
                <input type="radio" name="role" value="admin">
                <span class="role-icon admin-icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24"><path d="M12 3 19 6v5.2c0 4.4-2.8 7.6-7 9.8-4.2-2.2-7-5.4-7-9.8V6l7-3Z"/><path d="m9 12 2 2 4-4"/></svg>
                </span>
                <span class="role-text"><strong>Admin</strong><small>Catalog & inventory</small></span>
                <span class="radio-dot"></span>
              </label>
            </div>
          </fieldset>

          <div class="field" id="name-field" hidden>
            <label for="full-name">Full name</label>
            <div class="input-wrap">
              <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="3.3"/><path d="M5.5 20c.5-3.4 2.7-5.2 6.5-5.2s6 1.8 6.5 5.2"/></svg>
              <input id="full-name" name="name" type="text" placeholder="Your full name" autocomplete="name">
            </div>
            <small class="field-error" id="name-error"></small>
          </div>

          <div class="field">
            <label for="email">Email address</label>
            <div class="input-wrap">
              <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="5.5" width="17" height="13" rx="2"/><path d="m4.5 7 7.5 6 7.5-6"/></svg>
              <input id="email" name="email" type="email" placeholder="reader@example.com" autocomplete="email" required>
            </div>
            <small class="field-error" id="email-error"></small>
          </div>

          <div class="field">
            <div class="label-row"><label for="password">Password</label><a href="#" id="forgot-link">Forgot password?</a></div>
            <div class="input-wrap">
              <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4.5" y="10" width="15" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/><path d="M12 14v2"/></svg>
              <input id="password" name="password" type="password" placeholder="Enter your password" autocomplete="current-password" required minlength="6">
              <button class="password-toggle" type="button" id="toggle-password" aria-label="Show password">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
              </button>
            </div>
            <div class="password-hint" id="password-hint" hidden>Use at least 6 characters.</div>
            <small class="field-error" id="password-error"></small>
          </div>

          <div class="field" id="confirm-field" hidden>
            <label for="confirm-password">Confirm password</label>
            <div class="input-wrap">
              <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4.5" y="10" width="15" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
              <input id="confirm-password" name="confirm-password" type="password" placeholder="Re-enter your password" autocomplete="new-password">
            </div>
            <small class="field-error" id="confirm-error"></small>
          </div>

          <div class="form-extras" id="remember-row">
            <label class="check-label"><input type="checkbox" id="remember"><span class="custom-check"></span> Remember me</label>
            <span class="secure-note"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="10" width="14" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg> XAMPP Secure Session</span>
          </div>

          <div class="terms-row" id="terms-row" hidden>
            <label class="check-label"><input type="checkbox" id="terms"><span class="custom-check"></span> I agree to the <a href="#" class="terms-link">Terms</a> and <a href="#" class="terms-link">Privacy Policy</a></label>
          </div>

          <button class="submit-btn" type="submit"><span id="submit-label">Sign in to your account</span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>
          <p class="form-message" id="form-message" role="status" aria-live="polite"></p>
        </form>

        <div class="form-bottom"><span></span><p>LUMINA LITERARY SOCIETY</p><span></span></div>
        <p class="legal-note">By signing in, you agree to Lumina's <a href="#">Reader Terms</a> and <a href="#">Community Guidelines</a>.</p>
      </div>
      <div class="copyright">© <?php echo date('Y'); ?> Lumina Books · Handcrafted for Readers.</div>
    </section>
  </main>

  <script>
    const loginTab = document.getElementById('login-tab');
    const signupTab = document.getElementById('signup-tab');
    const formTitle = document.getElementById('form-title');
    const formSubtitle = document.getElementById('form-subtitle');
    const submitLabel = document.getElementById('submit-label');
    const nameField = document.getElementById('name-field');
    const confirmField = document.getElementById('confirm-field');
    const termsRow = document.getElementById('terms-row');
    const rememberRow = document.getElementById('remember-row');
    const passwordHint = document.getElementById('password-hint');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm-password');
    const fullName = document.getElementById('full-name');
    const message = document.getElementById('form-message');
    const authModeInput = document.getElementById('auth_mode_input');
    let mode = 'login';

    function setMode(nextMode) {
      mode = nextMode;
      authModeInput.value = mode;
      const signup = mode === 'signup';
      loginTab.classList.toggle('active', !signup);
      signupTab.classList.toggle('active', signup);
      formTitle.textContent = signup ? 'Create reader account' : 'Welcome back';
      formSubtitle.textContent = signup ? 'Join 45,000+ readers and begin exploring.' : 'Enter your details to access your account.';
      submitLabel.textContent = signup ? 'Complete Registration' : 'Sign in to Lumina Books';
      nameField.hidden = !signup;
      confirmField.hidden = !signup;
      termsRow.hidden = !signup;
      rememberRow.hidden = signup;
      passwordHint.hidden = !signup;
      fullName.required = signup;
      confirmPassword.required = signup;
      message.textContent = '';
      clearErrors();
    }

    loginTab.addEventListener('click', () => setMode('login'));
    signupTab.addEventListener('click', () => setMode('signup'));

    document.querySelectorAll('input[name="role"]').forEach((radio) => {
      radio.addEventListener('change', () => {
        document.querySelectorAll('.role-option').forEach((card) => card.classList.remove('selected'));
        radio.closest('.role-option').classList.add('selected');
      });
    });

    document.getElementById('toggle-password').addEventListener('click', (event) => {
      const button = event.currentTarget;
      const visible = password.type === 'password';
      password.type = visible ? 'text' : 'password';
      button.setAttribute('aria-label', visible ? 'Hide password' : 'Show password');
    });

    function clearErrors() {
      ['name-error', 'email-error', 'password-error', 'confirm-error'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.textContent = '';
      });
      document.querySelectorAll('.input-wrap').forEach(el => el.classList.remove('has-error'));
    }

    document.getElementById('auth-form').addEventListener('submit', (e) => {
      clearErrors();
      const email = document.getElementById('email').value.trim();
      let valid = true;

      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        document.getElementById('email-error').textContent = 'Please enter a valid email address.';
        valid = false;
      }
      if (password.value.length < 6) {
        document.getElementById('password-error').textContent = 'Password must be at least 6 characters.';
        valid = false;
      }
      if (mode === 'signup' && fullName.value.trim().length < 2) {
        document.getElementById('name-error').textContent = 'Please enter your full name.';
        valid = false;
      }

      if (!valid) {
        e.preventDefault();
      }
    });

    document.getElementById('forgot-link').addEventListener('click', (e) => {
      e.preventDefault();
      alert('Password reset link has been dispatched to your email address.');
    });
  </script>
</body>
</html>
