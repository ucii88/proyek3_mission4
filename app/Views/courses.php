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
    </style>
</head>
<body>
    <!-- Navigation  -->
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

        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2><i class="fas fa-book me-2"></i>
                        <?= session()->get('role') === 'admin' ? 'Manage Courses' : 'Daftar Mata Kuliah' ?>
                    </h2>
                    <p class="mb-0 opacity-75">
                        <?= session()->get('role') === 'admin' 
                            ? 'Tambah, edit, dan kelola semua mata kuliah'
                            : 'Lihat dan daftarkan diri ke mata kuliah yang tersedia' ?>
                    </p>
                </div>
            </div>
        </div>

        <?php if (session()->get('role') === 'admin'): ?>
            <!-- Admim-->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-plus me-2"></i>Tambah Mata Kuliah Baru</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="<?= base_url('/courses/create') ?>" id="addCourseForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="course_name" class="form-label">Nama Mata Kuliah</label>
                                    <input type="text" name="course_name" id="course_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="credits" class="form-label">SKS</label>
                                    <input type="number" name="credits" id="credits" class="form-control" min="1" max="6" required>
                                </div>
                            </div>
                            <div class="col-md-3">
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
        <?php endif; ?>

        <!-- Daftar Mata Kuliah -->
        <?php if (empty($courses)): ?>
            <div class="text-center py-5">
                <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Belum ada mata kuliah</h4>
                <?php if (session()->get('role') === 'admin'): ?>
                    <p class="text-muted">Silakan tambah mata kuliah baru menggunakan form di atas</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($courses as $course): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card course-card h-100 position-relative">
                            <?php if (session()->get('role') === 'student' && isset($enrolled) && in_array($course['course_id'], array_column($enrolled, 'course_id'))): ?>
                                <span class="badge bg-success enrolled-badge">Enrolled</span>
                            <?php endif; ?>
                            
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-book me-2 text-primary"></i>
                                    <?= esc($course['course_name']) ?>
                                </h5>
                                <p class="card-text">
                                    <i class="fas fa-credit-card me-2 text-info"></i>
                                    <strong><?= $course['credits'] ?> SKS</strong>
                                </p>
                                
                                <div class="d-flex gap-2 mt-3">
                                    <?php if (session()->get('role') === 'admin'): ?>
                                        <!-- Admin -->
                                        <button class="btn btn-warning btn-sm flex-fill" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editModal" 
                                                onclick="editCourse(<?= $course['course_id'] ?>, '<?= esc($course['course_name']) ?>', <?= $course['credits'] ?>)">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </button>
                                        <button class="btn btn-danger btn-sm flex-fill" 
                                                onclick="deleteCourse(<?= $course['course_id'] ?>, '<?= esc($course['course_name']) ?>')">
                                            <i class="fas fa-trash me-1"></i>Delete
                                        </button>
                                    <?php elseif (session()->get('role') === 'student'): ?>
                                        <!-- Mahasiswa -->
                                        <?php if (!isset($enrolled) || !in_array($course['course_id'], array_column($enrolled, 'course_id'))): ?>
                                            <button class="btn btn-success w-100" 
                                                    onclick="enrollCourse(<?= $course['course_id'] ?>, '<?= esc($course['course_name']) ?>')">
                                                <i class="fas fa-user-plus me-2"></i>Enroll
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-secondary w-100" disabled>
                                                <i class="fas fa-check me-2"></i>Already Enrolled
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Edit bagian admin -->
    <?php if (session()->get('role') === 'admin'): ?>
        <div class="modal fade" id="editModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Mata Kuliah</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="post" action="<?= base_url('/courses/update') ?>" id="editCourseForm">
                        <div class="modal-body">
                            <input type="hidden" name="course_id" id="edit_course_id">
                            <div class="mb-3">
                                <label for="edit_course_name" class="form-label">Nama Mata Kuliah</label>
                                <input type="text" name="course_name" id="edit_course_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_credits" class="form-label">SKS</label>
                                <input type="number" name="credits" id="edit_credits" class="form-control" min="1" max="6" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        
        function editCourse(id, name, credits) {
            document.getElementById('edit_course_id').value = id;
            document.getElementById('edit_course_name').value = name;
            document.getElementById('edit_credits').value = credits;
        }
        
        function deleteCourse(id, name) {
            if (confirm(`Apakah Anda yakin ingin menghapus mata kuliah "${name}"?`)) {
                window.location.href = `<?= base_url('/courses/delete/') ?>${id}`;
            }
        }
        
        function enrollCourse(id, name) {
            if (confirm(`Apakah Anda yakin ingin mendaftar mata kuliah "${name}"?`)) {
                window.location.href = `<?= base_url('/courses/enroll/') ?>${id}`;
            }
        }
        
        document.getElementById('addCourseForm')?.addEventListener('submit', function(e) {
            const courseName = document.getElementById('course_name').value.trim();
            const credits = document.getElementById('credits').value;
            
            if (!courseName || courseName.length < 3) {
                e.preventDefault();
                alert('Nama mata kuliah harus minimal 3 karakter');
                return false;
            }
            
            if (credits < 1 || credits > 6) {
                e.preventDefault();
                alert('SKS harus antara 1-6');
                return false;
            }
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