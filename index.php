<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="theme-color" content="#111827" />
  <title>Access | Sign in or create account</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="index.css" />
</head>
<body>
  <main class="page-shell">
    <section class="visual-panel" aria-label="Welcome">
      <a class="brand" href="#" aria-label="Nexa home">
        <span class="brand-mark"><span></span><span></span><span></span><span></span></span>
        <span>NEXA<span class="brand-dot">.</span></span>
      </a>

      <div class="visual-copy">
        <span class="eyebrow"><span class="live-dot"></span> ONE WORKSPACE. EVERY POSSIBILITY.</span>
        <h1>Make room for<br><em>what’s next.</em></h1>
        <p>A simpler, smarter way to access your workspace. Sign in and pick up right where you left off.</p>
      </div>

      <div class="preview-card" aria-hidden="true">
        <div class="preview-top">
          <div class="mini-avatar">N</div>
          <div><strong>Your workspace</strong><small>Everything in one place</small></div>
          <span class="preview-status">● Active</span>
        </div>
        <div class="preview-divider"></div>
        <div class="preview-stats">
          <div><small>PROJECTS</small><strong>12 <span>↗</span></strong></div>
          <div><small>TEAM MEMBERS</small><strong>08 <span class="muted-number">/ 10</span></strong></div>
          <div class="stat-visual"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>
        </div>
      </div>

      <div class="visual-footer">
        <div class="avatar-stack" aria-hidden="true"><span>J</span><span>M</span><span>A</span><span>+</span></div>
        <p><strong>Built for teams that move forward.</strong><br>Secure access, without the friction.</p>
        <span class="footer-sparkle">✳</span>
      </div>
    </section>

    <section class="form-panel">
      <div class="mobile-brand brand">
        <span class="brand-mark"><span></span><span></span><span></span><span></span></span>
        <span>NEXA<span class="brand-dot">.</span></span>
      </div>

      <div class="auth-wrap">
        <div class="form-heading">
          <span class="eyebrow form-eyebrow">WELCOME TO NEXA</span>
          <h2 id="form-title">Welcome back</h2>
          <p id="form-subtitle">Enter your details to access your account.</p>
        </div>

        <div class="mode-switch" role="tablist" aria-label="Account action">
          <button class="mode-btn active" id="login-tab" type="button" role="tab" aria-selected="true">Sign in</button>
          <button class="mode-btn" id="signup-tab" type="button" role="tab" aria-selected="false">Create account</button>
        </div>

        <form id="auth-form" novalidate>
          <fieldset class="role-field">
            <legend>Sign in as</legend>
            <div class="role-options">
              <label class="role-option selected" id="user-role-card">
                <input type="radio" name="role" value="user" checked>
                <span class="role-icon user-icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.4"/><path d="M5.5 20c.5-3.4 2.7-5.2 6.5-5.2s6 1.8 6.5 5.2"/></svg>
                </span>
                <span class="role-text"><strong>User</strong><small>Personal workspace</small></span>
                <span class="radio-dot"></span>
              </label>
              <label class="role-option" id="admin-role-card">
                <input type="radio" name="role" value="admin">
                <span class="role-icon admin-icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24"><path d="M12 3 19 6v5.2c0 4.4-2.8 7.6-7 9.8-4.2-2.2-7-5.4-7-9.8V6l7-3Z"/><path d="m9 12 2 2 4-4"/></svg>
                </span>
                <span class="role-text"><strong>Admin</strong><small>Manage workspace</small></span>
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
              <input id="email" name="email" type="email" placeholder="you@example.com" autocomplete="email" required>
            </div>
            <small class="field-error" id="email-error"></small>
          </div>

          <div class="field">
            <div class="label-row"><label for="password">Password</label><a href="#" id="forgot-link">Forgot password?</a></div>
            <div class="input-wrap">
              <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4.5" y="10" width="15" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/><path d="M12 14v2"/></svg>
              <input id="password" name="password" type="password" placeholder="Enter your password" autocomplete="current-password" required minlength="8">
              <button class="password-toggle" type="button" id="toggle-password" aria-label="Show password">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
              </button>
            </div>
            <div class="password-hint" id="password-hint" hidden>Use at least 8 characters.</div>
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
            <span class="secure-note"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="10" width="14" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg> Secure sign in</span>
          </div>
          <div class="terms-row" id="terms-row" hidden>
            <label class="check-label"><input type="checkbox" id="terms"><span class="custom-check"></span> I agree to the <a href="#" class="terms-link">Terms</a> and <a href="#" class="terms-link">Privacy Policy</a></label>
          </div>

          <button class="submit-btn" type="submit"><span id="submit-label">Sign in to your account</span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>
          <p class="form-message" id="form-message" role="status" aria-live="polite"></p>
        </form>

        <div class="form-bottom"><span></span><p>YOUR SPACE. YOUR PACE.</p><span></span></div>
        <p class="legal-note">By continuing, you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.</p>
      </div>
      <div class="copyright">© 2026 Nexa Workspace <span>·</span> Made for better work.</div>
    </section>
  </main>

  <script>
    // UI demo only: connect this form to your authentication backend before production.
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
    let mode = 'login';

    function setMode(nextMode) {
      mode = nextMode;
      const signup = mode === 'signup';
      loginTab.classList.toggle('active', !signup);
      signupTab.classList.toggle('active', signup);
      loginTab.setAttribute('aria-selected', String(!signup));
      signupTab.setAttribute('aria-selected', String(signup));
      formTitle.textContent = signup ? 'Create your account' : 'Welcome back';
      formSubtitle.textContent = signup ? 'A few details and you’re ready to go.' : 'Enter your details to access your account.';
      submitLabel.textContent = signup ? 'Create your account' : 'Sign in to your account';
      nameField.hidden = !signup;
      confirmField.hidden = !signup;
      termsRow.hidden = !signup;
      rememberRow.hidden = signup;
      passwordHint.hidden = !signup;
      fullName.required = signup;
      confirmPassword.required = signup;
      password.autocomplete = signup ? 'new-password' : 'current-password';
      message.textContent = '';
      clearErrors();
    }
    loginTab.addEventListener('click', () => setMode('login'));
    signupTab.addEventListener('click', () => setMode('signup'));

    document.querySelectorAll('input[name="role"]').forEach((radio) => {
      radio.addEventListener('change', () => {
        document.querySelectorAll('.role-option').forEach((card) => card.classList.remove('selected'));
        radio.closest('.role-option').classList.add('selected');
        message.textContent = radio.value === 'admin'
          ? 'Admin access selected. Your backend should verify admin permissions.'
          : '';
        message.classList.toggle('info', radio.value === 'admin');
      });
    });

    document.getElementById('toggle-password').addEventListener('click', (event) => {
      const button = event.currentTarget;
      const visible = password.type === 'password';
      password.type = visible ? 'text' : 'password';
      button.setAttribute('aria-label', visible ? 'Hide password' : 'Show password');
      button.classList.toggle('is-visible', visible);
    });

    function clearErrors() {
      ['name-error', 'email-error', 'password-error', 'confirm-error'].forEach(id => {
        document.getElementById(id).textContent = '';
      });
      document.querySelectorAll('.input-wrap').forEach(el => el.classList.remove('has-error'));
    }
    function setError(input, errorId, text) {
      document.getElementById(errorId).textContent = text;
      input.closest('.field').querySelector('.input-wrap').classList.add('has-error');
    }

    document.getElementById('auth-form').addEventListener('submit', (event) => {
      event.preventDefault();
      clearErrors();
      message.classList.remove('success', 'info');
      message.textContent = '';
      const email = document.getElementById('email');
      let valid = true;

      if (mode === 'signup' && fullName.value.trim().length < 2) {
        setError(fullName, 'name-error', 'Please enter your full name.');
        valid = false;
      }
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
        setError(email, 'email-error', 'Enter a valid email address.');
        valid = false;
      }
      if (password.value.length < 8) {
        setError(password, 'password-error', 'Password must be at least 8 characters.');
        valid = false;
      }
      if (mode === 'signup' && confirmPassword.value !== password.value) {
        setError(confirmPassword, 'confirm-error', 'Passwords do not match.');
        valid = false;
      }
      if (mode === 'signup' && !document.getElementById('terms').checked) {
        message.textContent = 'Please accept the Terms and Privacy Policy to continue.';
        valid = false;
      }
      if (!valid) return;

      const role = document.querySelector('input[name="role"]:checked').value;
      message.classList.add('success');
      message.textContent = mode === 'signup'
        ? `Form validated for ${role} sign up. Connect a backend to create the account.`
        : `Form validated for ${role} sign in. Connect a backend to authenticate securely.`;
    });

    document.getElementById('forgot-link').addEventListener('click', (event) => {
      event.preventDefault();
      message.classList.add('info');
      message.textContent = 'Password reset needs a backend flow. Connect your reset-password endpoint here.';
    });

    document.querySelectorAll('.terms-link, .legal-note a, .brand[href="#"]').forEach(link => {
      link.addEventListener('click', event => event.preventDefault());
    });
  </script>
</body>
</html>
