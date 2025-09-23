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
            transition: all 0.3s ease;
        }
        .form-control:focus {
            background: rgba(255,255,255,0.95);
            border-color: transparent;
            box-shadow: 0 0 0 0.2rem rgba(255,255,255,0.5);
            transform: translateY(-2px);
        }
        .form-control.is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
            background: rgba(255,200,200,0.9);
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
            color: #495057;
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
            position: relative;
            overflow: hidden;
        }
        .btn-primary:hover {
            background: rgba(255,255,255,0.3);
            border-color: rgba(255,255,255,0.5);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        .btn-primary:disabled {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.2);
            color: rgba(255,255,255,0.6);
            transform: none;
            cursor: not-allowed;
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
            transition: all 0.3s ease;
        }
        .btn-outline-secondary:hover {
            background: rgba(255,255,255,0.2);
            color: #333;
        }
  
        .loading-spinner {
            display: none;
            width: 1rem;
            height: 1rem;
            margin-right: 0.5rem;
        }
        
      
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        .shake {
            animation: shake 0.6s ease-in-out;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .pulse {
            animation: pulse 1s ease-in-out;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-form">
            <div class="login-header">
                <i class="fas fa-graduation-cap fa-4x mb-4 pulse"></i>
                <h2>Sistem Akademik</h2>
                <p>Silakan login untuk melanjutkan</p>
            </div>
            
           
            <div id="alertContainer"></div>
            
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
            
            <form method="post" action="<?= base_url('auth/login') ?>" id="loginForm">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" id="email" class="form-control" 
                               value="<?= old('email') ?>" required placeholder="Masukkan email">
                    </div>
                    <div class="invalid-feedback" id="email_error"></div>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" id="password" class="form-control" 
                               required placeholder="Masukkan password">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback" id="password_error"></div>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg" id="loginButton">
                        <span class="loading-spinner spinner-border spinner-border-sm" id="loadingSpinner"></span>
                        <i class="fas fa-sign-in-alt me-2" id="loginIcon"></i>
                        <span id="loginText">Login</span>
                    </button>
                </div>
            </form>
            
        
            <div class="mt-4 text-center">
                <small class="text-white-50">
                    <i class="fas fa-info-circle me-1"></i>
                    Demo: admin@test.com / student@test.com (password: 123456)
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
  
        const loginData = {
            email: '',
            password: '',
            isValid: false,
            attempts: 0,
            maxAttempts: 5
        };

        const validationRules = {
            email: {
                required: true,
                pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                message: 'Format email tidak valid'
            },
            password: {
                required: true,
                minLength: 6,
                message: 'Password harus minimal 6 karakter'
            }
        };

    
        function showFieldError(fieldId, message) {
            const field = document.getElementById(fieldId);
            const errorDiv = document.getElementById(fieldId + '_error');
            
            field.classList.add('is-invalid');
            field.classList.add('shake');
            
            if (errorDiv) {
                errorDiv.textContent = message;
            }
            
 
            setTimeout(() => {
                field.classList.remove('shake');
            }, 600);
        }

        function clearFieldError(fieldId) {
            const field = document.getElementById(fieldId);
            const errorDiv = document.getElementById(fieldId + '_error');
            
            field.classList.remove('is-invalid');
            if (errorDiv) {
                errorDiv.textContent = '';
            }
        }

        function clearAllErrors() {
            clearFieldError('email');
            clearFieldError('password');
        }

        function showAlert(message, type = 'danger') {
            const alertContainer = document.getElementById('alertContainer');
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
 
            alertContainer.innerHTML = '';
            alertContainer.appendChild(alertDiv);
            

            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        function setLoadingState(isLoading) {
            const loginButton = document.getElementById('loginButton');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const loginIcon = document.getElementById('loginIcon');
            const loginText = document.getElementById('loginText');
            
            if (isLoading) {
                loginButton.disabled = true;
                loadingSpinner.style.display = 'inline-block';
                loginIcon.style.display = 'none';
                loginText.textContent = 'Memproses...';
                loginButton.classList.add('pulse');
            } else {
                loginButton.disabled = false;
                loadingSpinner.style.display = 'none';
                loginIcon.style.display = 'inline';
                loginText.textContent = 'Login';
                loginButton.classList.remove('pulse');
            }
        }

    
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
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

  
        document.getElementById('email').addEventListener('input', function() {
            loginData.email = this.value.trim();
            if (this.classList.contains('is-invalid')) {
                validateField('email');
            }
        });

        document.getElementById('password').addEventListener('input', function() {
            loginData.password = this.value.trim();
            if (this.classList.contains('is-invalid')) {
                validateField('password');
            }
        });

        // Form validation
        function validateField(fieldName) {
            const field = document.getElementById(fieldName);
            const value = field.value.trim();
            const rules = validationRules[fieldName];
            
            if (rules.required && !value) {
                showFieldError(fieldName, `${fieldName === 'email' ? 'Email' : 'Password'} wajib diisi`);
                return false;
            }
            
            if (fieldName === 'email' && value && !rules.pattern.test(value)) {
                showFieldError(fieldName, rules.message);
                return false;
            }
            
            if (fieldName === 'password' && value && value.length < rules.minLength) {
                showFieldError(fieldName, rules.message);
                return false;
            }
            
            clearFieldError(fieldName);
            return true;
        }

        function validateForm() {
            clearAllErrors();
            
            const emailValid = validateField('email');
            const passwordValid = validateField('password');
            
            loginData.isValid = emailValid && passwordValid;
            return loginData.isValid;
        }

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Update login data
            loginData.email = document.getElementById('email').value.trim();
            loginData.password = document.getElementById('password').value.trim();
            
            if (!validateForm()) {
                showAlert('Mohon perbaiki kesalahan pada form', 'danger');
                return;
            }
            
            if (loginData.attempts >= loginData.maxAttempts) {
                showAlert(`Terlalu banyak percobaan login. Silakan coba lagi dalam beberapa menit.`, 'warning');
                return;
            }
            
            setLoadingState(true);
            
            loginData.attempts++;
            
            setTimeout(() => {
                this.submit();
            }, 1000); 
        });

       
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('email').focus();
        });

        
        function saveEmail(email) {
            if (typeof(Storage) !== "undefined" && email) {
                localStorage.setItem('rememberedEmail', email);
            }
        }

        function loadRememberedEmail() {
            if (typeof(Storage) !== "undefined") {
                const rememberedEmail = localStorage.getItem('rememberedEmail');
                if (rememberedEmail) {
                    document.getElementById('email').value = rememberedEmail;
                    loginData.email = rememberedEmail;
                }
            }
        }

     
        function detectCapsLock(event) {
            const capsLockWarning = document.getElementById('capsLockWarning');
            
            if (event.getModifierState && event.getModifierState('CapsLock')) {
                if (!capsLockWarning) {
                    const warning = document.createElement('div');
                    warning.id = 'capsLockWarning';
                    warning.className = 'alert alert-warning mt-2';
                    warning.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Caps Lock aktif';
                    document.getElementById('password').parentElement.parentElement.appendChild(warning);
                }
            } else if (capsLockWarning) {
                capsLockWarning.remove();
            }
        }

        document.getElementById('password').addEventListener('keypress', detectCapsLock);

        
        document.addEventListener('keydown', function(e) {
            
            if (e.key === 'Enter' && document.activeElement.id === 'email') {
                e.preventDefault();
                document.getElementById('password').focus();
            }
            
    
            if (e.key === 'Escape') {
                clearAllErrors();
                document.getElementById('loginForm').reset();
                document.getElementById('email').focus();
            }
        });

      
        let isSubmitting = false;
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
            isSubmitting = true;
            
       
            setTimeout(() => {
                isSubmitting = false;
                setLoadingState(false);
            }, 3000);
        });

      
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert:not(#alertContainer .alert)');
            alerts.forEach(function(alert) {
                try {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                } catch (e) {
                    console.log('Alert auto-close: ', e.message);
                }
            });
        }, 5000);

      
        document.addEventListener('DOMContentLoaded', function() {
            loadRememberedEmail();
            
        
            const formElements = document.querySelectorAll('.form-control');
            formElements.forEach(element => {
                element.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                });
                
                element.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                  
                    if (this.id === 'email' && this.value.trim()) {
                        saveEmail(this.value.trim());
                    }
                });
            });
            
            console.log('Login page initialized successfully');
            console.log('Max login attempts:', loginData.maxAttempts);
        });


        window.addEventListener('unhandledrejection', function(e) {
            console.error('Unhandled promise rejection:', e.reason);
            showAlert('Terjadi kesalahan sistem. Silakan refresh halaman dan coba lagi.', 'danger');
        });

        window.addEventListener('error', function(e) {
            console.error('Global error:', e.error);
            setLoadingState(false);
        });

        window.addEventListener('online', function() {
            showAlert('Koneksi internet tersedia', 'success');
        });

        window.addEventListener('offline', function() {
            showAlert('Koneksi internet terputus', 'warning');
            setLoadingState(false);
        });

        document.querySelectorAll('.form-control').forEach(input => {
     
            input.addEventListener('blur', function() {
                this.value = this.value.trim();
            });
            
        
            input.addEventListener('focus', function() {
                if (this.classList.contains('is-invalid')) {
                    clearFieldError(this.id);
                }
            });
        });

      
        document.addEventListener('keydown', function(e) {
       
            if (e.ctrlKey && e.shiftKey && e.key === 'A') {
                e.preventDefault();
                document.getElementById('email').value = 'admin@test.com';
                document.getElementById('password').value = '123456';
                showAlert('Demo admin credentials filled', 'info');
            }
            
       
            if (e.ctrlKey && e.shiftKey && e.key === 'S') {
                e.preventDefault();
                document.getElementById('email').value = 'student@test.com';
                document.getElementById('password').value = '123456';
                showAlert('Demo student credentials filled', 'info');
            }
        });


        if ('ontouchstart' in window) {
            document.querySelectorAll('.btn').forEach(button => {
                button.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.98)';
                });
                
                button.addEventListener('touchend', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        }
    </script>
</body>
</html>