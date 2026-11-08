<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ease Mart — Authentication</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"/>
    <style>
        :root {
            --em-green: #689f38;
            --em-green-hover: #558b2f;
            --em-light-green: #dcedc8;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: linear-gradient(135deg, #ffffff 0%, var(--em-light-green) 50%, var(--em-green) 100%);
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }

        .auth-wrapper {
            position: relative;
            width: 100%;
            max-width: 450px;
            padding: 0 16px;
        }

        .auth-container {
            background: rgba(255,255,255,0.97);
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
            padding: 60px 40px 40px;
            transition: opacity 0.35s ease, transform 0.35s ease;
        }

        .auth-container.hidden {
            display: none;
            opacity: 0;
            transform: translateY(16px);
        }

        .auth-header {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            background: var(--em-green);
            color: #fff;
            padding: 12px 60px;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
            font-weight: 600;
            font-size: 1.2rem;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            white-space: nowrap;
        }

        .logo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 20px 0 24px;
        }

        .logo-container img {
            max-height: 110px;
            margin-bottom: 12px;
        }

        .instruction-text {
            font-size: 1.25rem;
            font-weight: 500;
            color: #333;
        }

        .form-group {
            position: relative;
            margin-bottom: 18px;
        }

        .form-control {
            border-radius: 30px;
            padding: 13px 50px 13px 22px;
            border: 1.5px solid #ced4da;
            transition: all 0.3s;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: var(--em-green);
            box-shadow: 0 0 0 0.2rem rgba(104,159,56,0.18);
            outline: none;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .input-icon {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.1rem;
            color: #6c757d;
            cursor: pointer;
            user-select: none;
        }

        .btn-auth {
            background: var(--em-green);
            color: #fff;
            border: none;
            border-radius: 30px;
            padding: 13px;
            width: 100%;
            font-weight: 600;
            font-size: 1rem;
            margin-top: 6px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-auth:hover {
            background: var(--em-green-hover);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(104,159,56,0.3);
        }

        .btn-auth:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .auth-footer {
            text-align: center;
            margin-top: 22px;
            font-size: 0.9rem;
        }

        .auth-footer a {
            color: var(--em-green);
            font-weight: 600;
            text-decoration: none;
        }

        .auth-footer a:hover {
            color: var(--em-green-hover);
            text-decoration: underline;
        }

        .error-msg {
            color: #dc3545;
            font-size: 0.875rem;
            text-align: center;
            min-height: 20px;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>

<div class="auth-wrapper">

    <!-- LOGIN -->
    <div id="login-container" class="auth-container">
        <div class="auth-header">Login</div>

        <div class="logo-container">
            <img src="assets/images/banner-transparent-bg.png" alt="Ease Mart Logo">
            <div class="instruction-text">Log in to Ease Mart</div>
        </div>

        <form id="loginForm" novalidate>
            <div class="form-group">
                <input type="text" id="login-username" class="form-control" placeholder="Username" autocomplete="username" required>
                <i class="bi bi-person input-icon" id="login-user-icon"></i>
            </div>
            <div class="form-group">
                <input type="password" id="login-password" class="form-control" placeholder="Password" autocomplete="current-password" required>
                <i class="bi bi-lock input-icon" id="login-pass-icon" onclick="togglePassword('login-password', this)"></i>
            </div>
            <div class="error-msg" id="login-error"></div>
            <button type="submit" class="btn-auth" id="login-btn">Sign In</button>
            <div class="auth-footer">
                Don't have an account? <a href="#" onclick="showRegister(event)">Register</a>
            </div>
        </form>
    </div>

    <!-- REGISTER -->
    <div id="register-container" class="auth-container hidden">
        <div class="auth-header">Register</div>

        <div class="logo-container">
            <img src="assets/images/banner-transparent-bg.png" alt="Ease Mart Logo">
            <div class="instruction-text">Create an account</div>
        </div>

        <form id="registerForm" novalidate>
            <div class="form-group">
                <input type="text" id="reg-username" class="form-control" placeholder="Username" autocomplete="username" required>
                <i class="bi bi-person input-icon"></i>
            </div>
            <div class="form-group">
                <input type="email" id="reg-email" class="form-control" placeholder="Email" autocomplete="email" required>
                <i class="bi bi-envelope input-icon"></i>
            </div>
            <div class="form-group">
                <input type="password" id="reg-password" class="form-control" placeholder="Password" autocomplete="new-password" required>
                <i class="bi bi-lock input-icon" id="reg-pass-icon" onclick="togglePassword('reg-password', this)"></i>
            </div>
            <div class="error-msg" id="register-error"></div>
            <button type="submit" class="btn-auth" id="register-btn">Sign Up</button>
            <div class="auth-footer">
                Already have an account? <a href="#" onclick="showLogin(event)">Login</a>
            </div>
        </form>
    </div>

</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4" style="border-radius:20px;border:none;">
            <div class="modal-body">
                <i class="bi bi-check-circle-fill" style="font-size:4rem;color:var(--em-green);"></i>
                <h3 class="mt-3 fw-bold">Account Created!</h3>
                <p class="text-muted">You can now log in with your credentials.</p>
                <button type="button" class="btn-auth mt-2" data-bs-dismiss="modal" style="max-width:200px;margin:0 auto;">Continue</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    /* ── toggle password visibility ── */
    function togglePassword(inputId, icon) {
        const input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'bi bi-eye-slash input-icon';
        } else {
            input.type = 'password';
            icon.className = 'bi bi-lock input-icon';
        }
    }

    /* ── switch views ── */
    function showRegister(e) {
        e.preventDefault();
        document.getElementById('login-container').classList.add('hidden');
        document.getElementById('register-container').classList.remove('hidden');
        document.getElementById('login-error').textContent = '';
        document.getElementById('loginForm').reset();
    }

    function showLogin(e) {
        e.preventDefault();
        document.getElementById('register-container').classList.add('hidden');
        document.getElementById('login-container').classList.remove('hidden');
        document.getElementById('register-error').textContent = '';
        document.getElementById('registerForm').reset();
    }

    /* ── helpers ── */
    function setLoading(btnId, loading) {
        const btn = document.getElementById(btnId);
        btn.disabled = loading;
        btn.textContent = loading ? 'Please wait…' : (btnId === 'login-btn' ? 'Sign In' : 'Sign Up');
    }

    /* ── LOGIN ── */
    document.getElementById('loginForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const errorDiv = document.getElementById('login-error');
        errorDiv.textContent = '';

        const username = document.getElementById('login-username').value.trim();
        const password = document.getElementById('login-password').value;

        if (!username || !password) {
            errorDiv.textContent = 'Please fill in all fields.';
            return;
        }

        setLoading('login-btn', true);

        const formData = new FormData();
        formData.append('username', username);
        formData.append('password', password);

        fetch('auth.php?action=login', { method: 'POST', body: formData })
            .then(res => res.text())
            .then(data => {
                const response = data.trim();
                if (response === 'success') {
                    window.location.href = 'index.php';
                } else {
                    errorDiv.textContent = 'Invalid username or password.';
                    setLoading('login-btn', false);
                }
            })
            .catch(() => {
                errorDiv.textContent = 'Connection error. Please try again.';
                setLoading('login-btn', false);
            });
    });

    /* ── REGISTER ── */
    document.getElementById('registerForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const errorDiv = document.getElementById('register-error');
        errorDiv.textContent = '';

        const username = document.getElementById('reg-username').value.trim();
        const email    = document.getElementById('reg-email').value.trim();
        const password = document.getElementById('reg-password').value;

        if (!username || !email || !password) {
            errorDiv.textContent = 'Please fill in all fields.';
            return;
        }

        if (password.length < 6) {
            errorDiv.textContent = 'Password must be at least 6 characters.';
            return;
        }

        setLoading('register-btn', true);

        const formData = new FormData();
        formData.append('username', username);
        formData.append('email', email);
        formData.append('password', password);

        fetch('auth.php?action=register', { method: 'POST', body: formData })
            .then(res => res.text())
            .then(data => {
                const response = data.trim();
                if (response === 'registered') {
                    document.getElementById('registerForm').reset();
                    const modal = new bootstrap.Modal(document.getElementById('successModal'));
                    modal.show();
                    document.getElementById('successModal').addEventListener('hidden.bs.modal', function () {
                        showLogin(new Event('click'));
                    }, { once: true });
                } else {
                    errorDiv.textContent = 'Username or email already taken. Try another.';
                    setLoading('register-btn', false);
                }
            })
            .catch(() => {
                errorDiv.textContent = 'Connection error. Please try again.';
                setLoading('register-btn', false);
            });
    });
</script>
</body>
</html>
