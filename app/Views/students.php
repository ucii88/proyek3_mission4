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

        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2><i class="fas fa-users me-2"></i>Manage Students</h2>
                    <p class="mb-0 opacity-75">Tambah, edit, dan kelola data mahasiswa</p>
                </div>
            </div>
        </div>

        <!-- Tambah Mahasiswa -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-plus me-2"></i>Tambah Mahasiswa Baru</h5>
            </div>
            <div class="card-body">
                <form method="post" action="<?= base_url('/students/create') ?>" id="addStudentForm">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" name="username" id="username" class="form-control" value="<?= old('username') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control" value="<?= old('email') ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Nama Lengkap</label>
                                <input type="text" name="full_name" id="full_name" class="form-control" value="<?= old('full_name') ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="entry_year" class="form-label">Tahun Masuk</label>
                                <input type="number" name="entry_year" id="entry_year" class="form-control" value="<?= old('entry_year') ?>" min="2000" max="<?= date('Y') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary d-block w-100">
                                    <i class="fas fa-plus me-2"></i>Tambah
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Daftar Mahasiswa -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-list me-2"></i>Daftar Mahasiswa</h5>
                <div>
                    <button class="btn btn-outline-primary btn-sm" onclick="refreshStudentTable()">
                        <i class="fas fa-refresh me-1"></i>Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if (isset($students) && !empty($students)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Username</th>
                                    <th>Nama Lengkap</th>
                                    <th>Email</th>
                                    <th>Tahun Masuk</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($students as $student): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($student['username']) ?></td>
                                    <td><?= esc($student['full_name']) ?></td>
                                    <td><?= esc($student['email']) ?></td>
                                    <td><?= esc($student['entry_year'] ?? 'N/A') ?></td>
                                    <td>
                                        <button class="btn btn-info btn-sm" onclick="viewStudent(<?= $student['user_id'] ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-warning btn-sm" onclick="editStudent(<?= $student['user_id'] ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteStudent(<?= $student['user_id'] ?>, '<?= esc($student['full_name']) ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">Belum ada data mahasiswa</h4>
                        <p class="text-muted">Silakan tambah mahasiswa baru menggunakan form di atas</p>
                    </div>
                <?php endif; ?>
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

    <!-- Modal View Student -->
    <div class="modal fade" id="viewStudentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Mahasiswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="studentDetail">
                        <!-- Content will be loaded here -->
                    </div>
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

        function viewStudent(userId) {
            // Implement view student functionality
            fetch(`<?= base_url('/students/view/') ?>${userId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.student) {
                        const student = data.student;
                        const enrolledCourses = data.enrolled_courses || [];
                        
                        let coursesHtml = '';
                        if (enrolledCourses.length > 0) {
                            coursesHtml = '<ul class="list-group">';
                            enrolledCourses.forEach(course => {
                                coursesHtml += `<li class="list-group-item">${course.course_name} (${course.credits} SKS)</li>`;
                            });
                            coursesHtml += '</ul>';
                        } else {
                            coursesHtml = '<p class="text-muted">Belum ada mata kuliah yang diambil</p>';
                        }

                        document.getElementById('studentDetail').innerHTML = `
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Informasi Personal</h6>
                                    <p><strong>Username:</strong> ${student.username}</p>
                                    <p><strong>Nama Lengkap:</strong> ${student.full_name}</p>
                                    <p><strong>Email:</strong> ${student.email}</p>
                                    <p><strong>Tahun Masuk:</strong> ${student.entry_year || 'N/A'}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Mata Kuliah yang Diambil</h6>
                                    ${coursesHtml}
                                </div>
                            </div>
                        `;
                        
                        new bootstrap.Modal(document.getElementById('viewStudentModal')).show();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal memuat data mahasiswa');
                });
        }

        function editStudent(userId) {
            // Implement edit student functionality
            alert('Edit functionality will be implemented');
        }

        function deleteStudent(userId, fullName) {
            if (confirm(`Apakah Anda yakin ingin menghapus mahasiswa "${fullName}"?`)) {
                fetch(`<?= base_url('/students/delete/') ?>${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Gagal menghapus mahasiswa');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan');
                });
            }
        }

        function refreshStudentTable() {
            location.reload();
        }
        
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