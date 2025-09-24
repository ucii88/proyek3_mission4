<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses - Sistem Akademik</title>
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
        .course-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .course-card:hover {
            transform: translateY(-3px);
        }
        .enrolled-badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .nav-item .nav-link.active {
            background-color: #3c59dbff;
            color: white !important;
            border-radius: 5px;
        }
        .is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }
        .modal-content {
            border-radius: 10px;
        }
        .credit-counter {
            font-size: 1.1em;
            font-weight: bold;
        }
        .over-limit {
            color: #dc3545 !important;
            font-weight: bold;
        }
        .near-limit {
            color: #fd7e14 !important;
            font-weight: bold;
        }
        .invalid-feedback {
            display: none;
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 0.25rem;
        }
        .is-invalid ~ .invalid-feedback {
            display: block;
        }
    </style>
</head>
<body>
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
                    <?php if (session()->get('role') === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link active" href="<?= base_url('/courses') ?>">
                                <i class="fas fa-book me-1"></i>Manage Courses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/students') ?>">
                                <i class="fas fa-users me-1"></i>Manage Students
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link active" href="<?= base_url('/courses') ?>">
                                <i class="fas fa-book me-1"></i>All Courses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/mycourses') ?>">
                                <i class="fas fa-user-graduate me-1"></i>My Courses
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
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle me-2"></i><?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $field => $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2><i class="fas fa-book me-2"></i>
                        <?= session()->get('role') === 'admin' ? 'Manage Courses' : 'All Courses' ?>
                    </h2>
                    <p class="mb-0 opacity-75">
                        <?= session()->get('role') === 'admin' 
                            ? 'Tambah, edit, dan kelola semua mata kuliah'
                            : 'Lihat daftar mata kuliah dan daftarkan diri Anda' ?>
                    </p>
                </div>
                <?php if (session()->get('role') === 'student'): ?>
                <div class="col-md-4 text-end">
                    <div class="bg-white text-dark p-3 rounded">
                        <h6 class="mb-1">Total SKS Anda: <?= $total_credits ?? 0 ?>/24</h6>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar <?= ($total_credits ?? 0) > 20 ? 'bg-warning' : 'bg-success' ?>" 
                                 style="width: <?= min(100, (($total_credits ?? 0) / 24) * 100) ?>%"></div>
                        </div>
                        <small class="text-muted">Batas maksimal: 24 SKS</small>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (session()->get('role') === 'admin'): ?>
            <?= $this->include('courses/courses_admin') ?>
        <?php else: ?>
            <?= $this->include('courses/courses_student') ?>
        <?php endif; ?>
        
    </div>
    <div class="modal fade" id="logoutModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-sign-out-alt me-2"></i>Konfirmasi Logout
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin logout dari sistem?</p>
                    <small class="text-muted">Anda akan diarahkan kembali ke halaman login.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <form action="<?= base_url('/auth/logout') ?>" method="post" style="display:inline;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="confirm_logout" value="yes">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Ya, Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let courses = <?= json_encode($courses ?? []) ?>;
        let currentStudentCredits = <?= session()->get('role') === 'student' && isset($total_credits) ? $total_credits : 0 ?>;
        let selectedCourses = [];
        function showLogoutConfirmation() {
            const modal = new bootstrap.Modal(document.getElementById('logoutModal'));
            modal.show();
        }
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
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
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
            setActiveMenu();
            console.log('Courses page loaded successfully at ' + new Date().toLocaleString('id-ID', { timeZone: 'Asia/Jakarta' }));
            console.log('Current user role:', '<?= session()->get('role') ?>');
            console.log('Total courses:', courses.length);
            console.table('Courses Data:', courses);
            <?php if (session()->get('role') === 'student'): ?>
            console.log('Current student credits:', currentStudentCredits);
            <?php endif; ?>
        });
        function esc(string) {
            return string.replace(/&/g, "&amp;")
                        .replace(/</g, "&lt;")
                        .replace(/>/g, "&gt;")
                        .replace(/"/g, "&quot;")
                        .replace(/'/g, "&#039;");
        }
    </script>
</body>
</html>