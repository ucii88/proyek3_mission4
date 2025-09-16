<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Sistem Akademik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .navbar { box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .page-header {
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
            transform: translateY(-3px);
        }
    </style>
</head>
<body>
    <!-- Navigation (consistent with dashboard and courses) -->
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
                        <a class="nav-link" href="<?= base_url('/dashboard') ?>">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/courses') ?>">
                            <i class="fas fa-book me-1"></i>Manage Courses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('/students') ?>">
                            <i class="fas fa-users me-1"></i>Manage Students
                        </a>
                    </li>
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
                                <a class="dropdown-item text-danger" href="<?= base_url('/auth/logout') ?>" onclick="return confirm('Yakin logout?')">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="container mt-4">
        <!-- Alerts if any -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i><?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Page Header -->
        <div class="page-header">
            <h2 class="mb-1">Manage Students</h2>
            <p class="mb-0">Tambah, edit, dan hapus data mahasiswa</p>
        </div>

        <!-- Add Student Form -->
        <div class="card feature-card mb-4">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-user-plus me-2"></i>Tambah Mahasiswa Baru</h5>
                <form method="post" action="<?= base_url('/students/create') ?>" id="addStudentForm">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" id="username" class="form-control" required placeholder="Username">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" required placeholder="Email">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required placeholder="Password">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" name="full_name" id="full_name" class="form-control" required placeholder="Full Name">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="entry_year" class="form-label">Entry Year</label>
                            <input type="number" name="entry_year" id="entry_year" class="form-control" required placeholder="Entry Year" min="2000" max="<?= date('Y') ?>">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add Student
                    </button>
                </form>
            </div>
        </div>

        <!-- Students Table -->
        <div class="card feature-card">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-list me-2"></i>Daftar Mahasiswa</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Entry Year</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($students)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data mahasiswa</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><?= esc($student['user_id']) ?></td>
                                        <td><?= esc($student['full_name']) ?></td>
                                        <td><?= esc($student['email']) ?></td>
                                        <td><?= esc($student['entry_year']) ?></td>
                                        <td>
                                            <a href="<?= base_url('/students/edit/' . $student['user_id']) ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="<?= base_url('/students/delete/' . $student['user_id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus mahasiswa ini?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Form validation for add student
        document.getElementById('addStudentForm')?.addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            const fullName = document.getElementById('full_name').value.trim();
            const entryYear = document.getElementById('entry_year').value;
            
            if (!username || username.length < 3) {
                e.preventDefault();
                alert('Username harus minimal 3 karakter');
                return false;
            }
            
            if (!email || !email.includes('@')) {
                e.preventDefault();
                alert('Email tidak valid');
                return false;
            }
            
            if (!password || password.length < 6) {
                e.preventDefault();
                alert('Password harus minimal 6 karakter');
                return false;
            }
            
            if (!fullName || fullName.length < 2) {
                e.preventDefault();
                alert('Nama lengkap harus minimal 2 karakter');
                return false;
            }
            
            if (!entryYear || entryYear < 2000 || entryYear > new Date().getFullYear()) {
                e.preventDefault();
                alert('Tahun masuk harus antara 2000 - ' + new Date().getFullYear());
                return false;
            }
        });
        
        // Auto-hide alerts
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