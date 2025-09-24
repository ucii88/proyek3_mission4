<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Sistem Akademik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .welcome-card {
            background: #3c59dbff;
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .feature-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .user-info {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .nav-item .nav-link.active {
            background-color: #3c59dbff;
            color: white !important;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="<?= base_url('/dashboard') ?>">
                <i class="fas fa-graduation-cap me-2"></i>Sistem Akademik
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('/dashboard') ?>">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/courses') ?>">
                            <i class="fas fa-book me-1"></i>All Courses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/mycourses') ?>">
                            <i class="fas fa-user-graduate me-1"></i>My Courses
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i><?= esc($full_name) ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><h6 class="dropdown-header">Role: Student</h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <button class="dropdown-item text-danger" onclick="showLogoutConfirmation()">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
    
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= esc(session()->getFlashdata('success')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        
        <div class="welcome-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-2">
                        <i class="fas fa-hand-wave me-2"></i>
                        Selamat Datang, <?= esc($full_name) ?>!
                    </h2>
                    <p class="mb-0 opacity-75">
                        Anda masuk sebagai Mahasiswa. Total SKS: <?= esc($total_credits) ?>
                    </p>
                </div>
                <div class="col-md-4 text-center">
                    <i class="fas fa-user-graduate fa-4x opacity-50"></i>
                </div>
            </div>
        </div>

 
        <div class="row">
            <div class="col-md-4">
                <div class="user-info">
                    <h5><i class="fas fa-id-card me-2 text-primary"></i>Informasi Akun</h5>
                    <hr>
                    <p><strong>Nama:</strong> <?= esc($full_name) ?></p>
                    <p><strong>Email:</strong> <?= esc($email) ?></p>
                    <p><strong>Role:</strong> <span class="badge bg-primary">Student</span></p>
                    <p><strong>Tahun Masuk:</strong> <?= esc($entry_year) ?></p>
                </div>
            </div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card feature-card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-book fa-3x text-primary mb-3"></i>
                                <h5>All Courses</h5>
                                <a href="<?= base_url('/courses') ?>" class="btn btn-primary">Buka</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card feature-card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-user-graduate fa-3x text-success mb-3"></i>
                                <h5>My Courses</h5>
                                <a href="<?= base_url('/mycourses') ?>" class="btn btn-primary">Buka</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

   
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin logout?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="<?= base_url('/auth/logout') ?>" method="post">
                        <input type="hidden" name="confirm_logout" value="yes">
                        <button type="submit" class="btn btn-danger">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const userData = {
            role: '<?= esc($role) ?>',
            full_name: '<?= esc($full_name) ?>'
        };

        function setActiveMenu() {
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                if (link.href === window.location.href) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        }

        function showLogoutConfirmation() {
            const modal = new bootstrap.Modal(document.getElementById('logoutModal'));
            modal.show();
        }

        function updateWelcomeMessage() {
            const welcomeElement = document.querySelector('.welcome-card h2');
            const currentHour = new Date().getHours();
            let greeting = 'Selamat Datang';
            if (currentHour < 12) greeting = 'Selamat Pagi';
            else if (currentHour < 17) greeting = 'Selamat Siang';
            else greeting = 'Selamat Malam';
            if (welcomeElement) {
                const nameElement = welcomeElement.innerHTML.split(',')[1];
                welcomeElement.innerHTML = `<i class="fas fa-hand-wave me-2"></i>${greeting},${nameElement}`;
            }
        }

        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = button.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                ripple.style.cssText = `
                    position: absolute;
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    background: rgba(255,255,255,0.3);
                    border-radius: 50%;
                    transform: scale(0);
                    animation: ripple 0.6s linear;
                    pointer-events: none;
                `;
                button.style.position = 'relative';
                button.style.overflow = 'hidden';
                button.appendChild(ripple);
                setTimeout(() => ripple.remove(), 600);
            });
        });

        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);

        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                try {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                } catch (e) {
                    console.log('Alert auto-close:', e.message);
                }
            });
        }, 5000);

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                const activeModal = document.querySelector('.modal.show');
                if (activeModal) {
                    const modal = bootstrap.Modal.getInstance(activeModal);
                    if (modal) modal.hide();
                }
            }
            if (e.ctrlKey && e.key === 'l') {
                e.preventDefault();
                showLogoutConfirmation();
            }
        });

        window.addEventListener('unhandledrejection', e => {
            console.error('Unhandled promise rejection:', e.reason);
        });

        window.addEventListener('error', e => {
            console.error('Global error:', e.error);
        });

        window.addEventListener('load', () => {
            const loadTime = performance.now();
            console.log(`Dashboard loaded in ${loadTime.toFixed(2)}ms`);
        });

        document.addEventListener('DOMContentLoaded', () => {
            setActiveMenu();
            updateWelcomeMessage();
            document.documentElement.style.scrollBehavior = 'smooth';
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('SW registered:', reg))
                    .catch(err => console.log('SW registration failed:', err));
            }
        });

        function esc(string) {
            return string.replace(/&/g, "&amp;")
                        .replace(/</g, "&lt;")
                        .replace(/>/g, "&gt;")
                        .replace(/"/g, "&quot;")
                        .replace(/'/g, "&#039;");
        }

        document.getElementById('logoutModal')?.addEventListener('shown.bs.modal', function() {
            this.querySelector('.btn-secondary').focus();
        });

        document.getElementById('logoutModal')?.addEventListener('hidden.bs.modal', function() {
            clearTimeout(window.logoutTimeout);
        });
    </script>
</body>
</html>