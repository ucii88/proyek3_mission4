<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Akademik</title>
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
        #logoutDialog { 
            display: none; 
            position: fixed; 
            top: 50%; 
            left: 50%; 
            transform: translate(-50%, -50%); 
            background: white; 
            padding: 20px; 
            border: 1px solid #ccc; 
            box-shadow: 0 0 10px rgba(0,0,0,0.5); 
            z-index: 1000; 
        }
        #logoutDialog button { margin: 10px; }
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
                    
                    <?php if ($role === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/courses') ?>">
                                <i class="fas fa-book me-1"></i>Manage Courses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/students') ?>">
                                <i class="fas fa-users me-1"></i>Manage Students
                            </a>
                        </li>
                    <?php elseif ($role === 'student'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/courses') ?>">
                                <i class="fas fa-book me-1"></i>Courses
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i><?= session()->get('full_name') ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><h6 class="dropdown-header">Role: <?= ucfirst(session()->get('role')) ?></h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <button class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">
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
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="welcome-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-2">
                        <i class="fas fa-hand-wave me-2"></i>
                        Selamat Datang, <?= session()->get('full_name') ?>!
                    </h2>
                    <p class="mb-0 opacity-75">
                        <?php if ($role === 'admin'): ?>
                            Anda masuk sebagai Administrator. Anda memiliki akses penuh untuk mengelola sistem.
                        <?php else: ?>
                            Anda masuk sebagai Mahasiswa. Silakan kelola mata kuliah Anda.
                        <?php endif; ?>
                    </p>
                </div>
                <div class="col-md-4 text-center">
                    <i class="fas <?= $role === 'admin' ? 'fa-user-shield' : 'fa-user-graduate' ?> fa-4x opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- Info user -->
        <div class="row">
            <div class="col-md-4">
                <div class="user-info">
                    <h5><i class="fas fa-id-card me-2 text-primary"></i>Informasi Akun</h5>
                    <hr>
                    <p><strong>Nama:</strong> <?= session()->get('full_name') ?></p>
                    <p><strong>Email:</strong> <?= session()->get('email') ?></p>
                    <p><strong>Username:</strong> <?= session()->get('username') ?></p>
                    <p><strong>Role:</strong> 
                        <span class="badge bg-<?= $role === 'admin' ? 'danger' : 'primary' ?>">
                            <?= ucfirst($role) ?>
                        </span>
                    </p>
                    <?php if ($role === 'student' && session()->get('entry_year')): ?>
                        <p><strong>Tahun Masuk:</strong> <?= session()->get('entry_year') ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="row">
                    <?php if ($role === 'admin'): ?>
                        <!-- Fitur Admin -->
                        <div class="col-md-6 mb-3">
                            <div class="card feature-card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-book fa-3x text-primary mb-3"></i>
                                    <h5>Manage Courses</h5>
                                    <p class="text-muted">Tambah, edit, dan hapus mata kuliah</p>
                                    <a href="<?= base_url('/courses') ?>" class="btn btn-primary">
                                        <i class="fas fa-arrow-right me-2"></i>Buka
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="card feature-card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-users fa-3x text-success mb-3"></i>
                                    <h5>Manage Students</h5>
                                    <p class="text-muted">Tambah, edit, dan hapus data mahasiswa</p>
                                    <a href="<?= base_url('/students') ?>" class="btn btn-success">
                                        <i class="fas fa-arrow-right me-2"></i>Buka
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                    <?php else: ?>
                        <!-- Fitur Mahasiswa -->
                        <div class="col-md-12 mb-3">
                            <div class="card feature-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-book-open fa-3x text-info mb-3"></i>
                                    <h5>Mata Kuliah</h5>
                                    <p class="text-muted">Lihat daftar mata kuliah dan daftarkan diri Anda</p>
                                    <a href="<?= base_url('/courses') ?>" class="btn btn-info text-white">
                                        <i class="fas fa-arrow-right me-2"></i>Lihat Mata Kuliah
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

  <!-- Modal Logout -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin logout dari sistem?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="<?= base_url('/auth/logout') ?>" method="post" style="display:inline;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="confirm_logout" value="yes">
                        <button type="submit" class="btn btn-danger">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Menu aktif
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                navLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });

        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        function esc(string) {
            return string.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }
    </script>
</body>
</html>