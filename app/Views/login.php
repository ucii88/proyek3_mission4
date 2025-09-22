<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Akademik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #99affdff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
        }
        .login-form {
            width: 100%;
        }
        .login-header {
            text-align: center;
            margin-bottom: 3rem;
            color: white;
        }
        .login-header h2 {
            color: white;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        .login-header p {
            color: rgba(255,255,255,0.9);
            margin-bottom: 0;
            font-size: 1rem;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }
        .login-header i {
            color: white;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        .form-floating {
            margin-bottom: 1.5rem;
        }
        .form-control {
            background: rgba(255,255,255,0.9);
            border: none;
            border-radius: 12px;
            padding: 1rem;
            backdrop-filter: blur(10px);
            font-size: 1rem;
        }
        .form-control:focus {
            background: rgba(255,255,255,0.95);
            border-color: transparent;
            box-shadow: 0 0 0 0.2rem rgba(255,255,255,0.5);
        }
        .form-control::placeholder {
            color: rgba(0,0,0,0.6);
        }
        .form-label {
            color: white;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        .input-group {
            margin-bottom: 1.5rem;
        }
        .input-group-text {
            background: rgba(255,255,255,0.9);
            border: none;
            border-radius: 12px 0 0 12px;
        }
        .input-group .form-control {
            border-radius: 0 12px 12px 0;
        }
        .btn-primary {
            background: rgba(255,255,255,0.2);
            border: 2px solid rgba(255,255,255,0.3);
            color: white;
            padding: 0.85rem;
            font-weight: 600;
            border-radius: 12px;
            backdrop-filter: blur(10px);
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: rgba(255,255,255,0.3);
            border-color: rgba(255,255,255,0.5);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        .alert {
            border-radius: 12px;
            margin-bottom: 1.5rem;
            background: rgba(255,255,255,0.9);
            border: none;
            backdrop-filter: blur(10px);
        }
        .btn-outline-secondary {
            background: rgba(255,255,255,0.1);
            border: none;
            color: #666;
        }
        .btn-outline-secondary:hover {
            background: rgba(255,255,255,0.2);
            color: #333;
        }
        .is-invalid { border-color: red !important; }
        .error-msg { color: red; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-form">
            <div class="login-header">
                <i class="fas fa-graduation-cap fa-4x mb-4"></i>
                <h2>Sistem Akademik</h2>
                <p>Silakan login untuk melanjutkan</p>
            </div>
            
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="post" action="<?= base_url('auth/login') ?>" id="loginForm">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" id="email" class="form-control" 
                               value="<?= old('email') ?>" required placeholder="Masukkan email">
                    </div>
                    <div class="error-msg" id="email_error"></div>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" id="password" class="form-control" 
                               required placeholder="Masukkan password">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="error-msg" id="password_error"></div>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const toggleIcon = this.querySelector('i');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        });
        
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            let valid = true;

            document.getElementById('email_error').textContent = '';
            document.getElementById('password_error').textContent = '';
            emailInput.classList.remove('is-invalid');
            passwordInput.classList.remove('is-invalid');

            const email = emailInput.value.trim();
            const password = passwordInput.value.trim();

            if (!email || !email.includes('@')) {
                document.getElementById('email_error').textContent = 'Email tidak valid';
                emailInput.classList.add('is-invalid');
                valid = false;
            }
            if (!password || password.length < 6) {
                document.getElementById('password_error').textContent = 'Password harus minimal 6 karakter';
                passwordInput.classList.add('is-invalid');
                valid = false;
            }

            if (!valid) e.preventDefault();
        });
        
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>