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
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-plus me-2"></i>Tambah Mata Kuliah Baru</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="<?= base_url('/courses/create') ?>" id="addCourseForm" novalidate>
                        <?= csrf_field() ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="course_name" class="form-label">Nama Mata Kuliah <span class="text-danger">*</span></label>
                                    <input type="text" name="course_name" id="course_name" class="form-control" 
                                           placeholder="Masukkan nama mata kuliah" maxlength="100" required>
                                    <div class="invalid-feedback" id="course_name_error"></div>
                                    <div class="form-text">Minimal 3 karakter, maksimal 100 karakter</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="credits" class="form-label">SKS <span class="text-danger">*</span></label>
                                    <input type="number" name="credits" id="credits" class="form-control" 
                                           min="1" max="6" placeholder="1-6" required>
                                    <div class="invalid-feedback" id="credits_error"></div>
                                    <div class="form-text">1 - 6 SKS</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary d-block w-100">
                                        <i class="fas fa-plus me-2"></i>Tambah Mata Kuliah
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
                <?php else: ?>
                    <p class="text-muted">Belum ada mata kuliah yang tersedia untuk didaftar</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php if (session()->get('role') === 'student'): ?>
                <?php 
                $hasAvailableCourses = false;
                foreach ($courses as $course) {
                    if (!isset($enrolled) || !in_array($course['course_id'], array_column($enrolled, 'course_id'))) {
                        $hasAvailableCourses = true;
                        break;
                    }
                }
                ?>
                
                <?php if ($hasAvailableCourses): ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-check-square me-2"></i>Pilih Mata Kuliah untuk Didaftar</h5>
                        <form id="enrollForm" method="post" action="<?= base_url('/courses/enrollMultiple') ?>">
                            <?= csrf_field() ?>
                            <div class="row">
                                <?php foreach ($courses as $course): ?>
                                    <?php if (!isset($enrolled) || !in_array($course['course_id'], array_column($enrolled, 'course_id'))): ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="course_ids[]" 
                                                       value="<?= $course['course_id'] ?>" 
                                                       id="course_<?= $course['course_id'] ?>" 
                                                       onchange="updateTotalCredits()" 
                                                       data-credits="<?= $course['credits'] ?>"
                                                       data-course-name="<?= esc($course['course_name']) ?>">
                                                <label class="form-check-label" for="course_<?= $course['course_id'] ?>">
                                                    <strong><?= esc($course['course_name']) ?></strong> (<?= $course['credits'] ?> SKS)
                                                </label>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="credit-counter">
                                    Total SKS: <span id="totalCredits"><?= $total_credits ?? 0 ?>/24</span>
                                    <br><span id="selectedCredits" class="text-muted">Dipilih: 0 SKS</span>
                                    <small class="text-muted d-block">Batas maksimal: 24 SKS per semester</small>
                                </div>
                                <button type="submit" class="btn btn-success" id="submitEnrollBtn">
                                    <i class="fas fa-user-plus me-2"></i>Daftar Mata Kuliah
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>Semua mata kuliah sudah Anda daftar. 
                        <a href="<?= base_url('/mycourses') ?>" class="alert-link">Lihat mata kuliah Anda</a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="row">
                <?php foreach ($courses as $course): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card course-card h-100 position-relative">
                            <?php if (session()->get('role') === 'student' && isset($enrolled) && in_array($course['course_id'], array_column($enrolled, 'course_id'))): ?>
                                <span class="badge bg-success enrolled-badge">Terdaftar</span>
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
                                        <button class="btn btn-warning btn-sm flex-fill" 
                                                onclick="editCourse(<?= $course['course_id'] ?>, '<?= esc($course['course_name']) ?>', <?= $course['credits'] ?>)"
                                                title="Edit mata kuliah">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </button>
                                        <button class="btn btn-danger btn-sm flex-fill" 
                                                onclick="showDeleteConfirmation(<?= $course['course_id'] ?>, '<?= esc($course['course_name']) ?>', <?= $course['credits'] ?>)"
                                                title="Hapus mata kuliah">
                                            <i class="fas fa-trash me-1"></i>Hapus
                                        </button>
                                    <?php elseif (session()->get('role') === 'student'): ?>
                                        <?php if (!isset($enrolled) || !in_array($course['course_id'], array_column($enrolled, 'course_id'))): ?>
                                            <button class="btn btn-success w-100" 
                                                    onclick="enrollSingleCourse(<?= $course['course_id'] ?>, '<?= esc($course['course_name']) ?>', <?= $course['credits'] ?>)"
                                                    title="Daftar mata kuliah ini">
                                                <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-secondary w-100" disabled title="Sudah terdaftar">
                                                <i class="fas fa-check me-2"></i>Sudah Terdaftar
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

    <!-- Edit Modal untuk Admin -->
    <?php if (session()->get('role') === 'admin'): ?>
        <div class="modal fade" id="editModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-edit me-2"></i>Edit Mata Kuliah
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="post" action="<?= base_url('/courses/update') ?>" id="editCourseForm" novalidate>
                        <?= csrf_field() ?>
                        <div class="modal-body">
                            <input type="hidden" name="course_id" id="edit_course_id">
                            <div class="mb-3">
                                <label for="edit_course_name" class="form-label">Nama Mata Kuliah <span class="text-danger">*</span></label>
                                <input type="text" name="course_name" id="edit_course_name" class="form-control" 
                                       maxlength="100" placeholder="Masukkan nama mata kuliah" required>
                                <div class="invalid-feedback" id="edit_course_name_error"></div>
                                <div class="form-text">Minimal 3 karakter, maksimal 100 karakter</div>
                            </div>
                            <div class="mb-3">
                                <label for="edit_credits" class="form-label">SKS <span class="text-danger">*</span></label>
                                <input type="number" name="credits" id="edit_credits" class="form-control" 
                                       min="1" max="6" placeholder="1-6" required>
                                <div class="invalid-feedback" id="edit_credits_error"></div>
                                <div class="form-text">1 - 6 SKS</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Batal
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Mata Kuliah
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-triangle text-danger me-2"></i>Konfirmasi Penghapusan
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Peringatan!</strong> Tindakan ini tidak dapat dibatalkan.
                        </div>
                        <p class="mb-3">Apakah Anda yakin ingin menghapus mata kuliah:</p>
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title text-primary" id="deleteCourseInfo">
                                    <i class="fas fa-book me-2"></i><span id="deleteCourseName"></span>
                                </h6>
                                <p class="card-text"><strong>SKS:</strong> <span id="deleteCourseCredits"></span></p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Mata kuliah hanya dapat dihapus jika tidak ada mahasiswa yang terdaftar.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Batal
                        </button>
                        <form id="deleteForm" method="post" style="display:inline;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="confirm_delete" value="yes">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-2"></i>Ya, Hapus Permanent
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Modal Logout -->
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

        function updateTotalCredits() {
            const checkboxes = document.querySelectorAll('#enrollForm input[type="checkbox"]:checked');
            let selectedCredits = 0;
            selectedCourses = [];
            
            checkboxes.forEach(checkbox => {
                const credits = parseInt(checkbox.getAttribute('data-credits'));
                const courseName = checkbox.getAttribute('data-course-name');
                selectedCredits += credits;
                selectedCourses.push({
                    id: checkbox.value,
                    name: courseName,
                    credits: credits
                });
            });
            
            const totalCredits = currentStudentCredits + selectedCredits;
            const totalCreditsSpan = document.getElementById('totalCredits');
            const selectedCreditsSpan = document.getElementById('selectedCredits');
            const submitBtn = document.getElementById('submitEnrollBtn');
            
            if (totalCreditsSpan) totalCreditsSpan.textContent = totalCredits;
            if (selectedCreditsSpan) selectedCreditsSpan.textContent = `Dipilih: ${selectedCredits} SKS`;
            
            if (submitBtn) {
                if (totalCredits > 24) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Melebihi Batas SKS (24)';
                    submitBtn.className = 'btn btn-danger';
                } else if (totalCredits > 20) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Mendekati Batas - Daftar';
                    submitBtn.className = 'btn btn-warning';
                } else {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-user-plus me-2"></i>Daftar Mata Kuliah';
                    submitBtn.className = 'btn btn-success';
                }
            }
        }

        document.getElementById('addCourseForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const courseName = document.getElementById('course_name').value.trim();
            const credits = document.getElementById('credits').value.trim();
            
            if (validateCourseForm(courseName, credits, 'course_name', 'credits')) {
                this.submit();
            }
        });

        document.getElementById('editCourseForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const courseName = document.getElementById('edit_course_name').value.trim();
            const credits = document.getElementById('edit_credits').value.trim();
            
            if (validateCourseForm(courseName, credits, 'edit_course_name', 'edit_credits')) {
                this.submit();
            }
        });

        document.getElementById('enrollForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (selectedCourses.length === 0) {
                showValidationError('Pilih setidaknya satu mata kuliah untuk didaftar');
                return;
            }
            
            const totalCredits = currentStudentCredits + selectedCourses.reduce((sum, course) => sum + course.credits, 0);
            
            if (totalCredits > 24) {
                showValidationError(`Total SKS akan menjadi ${totalCredits} yang melebihi batas maksimum 24 SKS.`);
                return;
            }
            
            let confirmMessage = `Konfirmasi Pendaftaran:\n\n`;
            confirmMessage += `Jumlah mata kuliah: ${selectedCourses.length}\n`;
            confirmMessage += `Total SKS baru: ${selectedCourses.reduce((sum, course) => sum + course.credits, 0)}\n`;
            confirmMessage += `Total SKS keseluruhan: ${totalCredits}/24\n\n`;
            
            if (totalCredits > 20) {
                confirmMessage += `⚠️ PERINGATAN: Total SKS mendekati batas maksimum!\n\n`;
            }
            
            confirmMessage += `Lanjutkan pendaftaran?`;
            
            if (confirm(confirmMessage)) {
                this.submit();
            }
        });

        function validateCourseForm(courseName, credits, nameFieldId, creditsFieldId) {
            let isValid = true;
            clearFieldErrors();

            // Course name validation
            if (!courseName) {
                showFieldError(nameFieldId, 'Nama mata kuliah wajib diisi');
                isValid = false;
            } else if (courseName.length < 3) {
                showFieldError(nameFieldId, 'Nama mata kuliah harus minimal 3 karakter');
                isValid = false;
            } else if (courseName.length > 100) {
                showFieldError(nameFieldId, 'Nama mata kuliah maksimal 100 karakter');
                isValid = false;
            } else if (!/^[a-zA-Z0-9\s\-&(),.']+$/.test(courseName)) {
                showFieldError(nameFieldId, 'Nama mata kuliah mengandung karakter yang tidak diperbolehkan');
                isValid = false;
            }

            // Credits validation
            if (!credits) {
                showFieldError(creditsFieldId, 'SKS wajib diisi');
                isValid = false;
            } else if (isNaN(credits) || credits < 1 || credits > 6) {
                showFieldError(creditsFieldId, 'SKS harus antara 1-6');
                isValid = false;
            }

            return isValid;
        }

        function showFieldError(fieldId, message) {
            const field = document.getElementById(fieldId);
            const errorDiv = document.getElementById(fieldId + '_error');
            
            field.classList.add('is-invalid');
            if (errorDiv) {
                errorDiv.textContent = message;
            }
        }

        function clearFieldErrors() {
            document.querySelectorAll('.is-invalid').forEach(field => {
                field.classList.remove('is-invalid');
            });
            document.querySelectorAll('.invalid-feedback').forEach(error => {
                error.textContent = '';
            });
        }

        function showValidationError(message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="fas fa-exclamation-triangle me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const container = document.querySelector('.container');
            const firstChild = container.firstElementChild;
            container.insertBefore(alertDiv, firstChild);
            
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        function editCourse(id, name, credits) {
            document.getElementById('edit_course_id').value = id;
            document.getElementById('edit_course_name').value = name;
            document.getElementById('edit_credits').value = credits;
            clearFieldErrors();
            
            const modal = new bootstrap.Modal(document.getElementById('editModal'));
            modal.show();
        }

        function showDeleteConfirmation(courseId, courseName, credits) {
            document.getElementById('deleteCourseName').textContent = courseName;
            document.getElementById('deleteCourseCredits').textContent = credits;
            document.getElementById('deleteForm').action = `<?= base_url('/courses/delete/') ?>${courseId}`;
            
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }

        function enrollSingleCourse(courseId, courseName, credits) {
            const totalCredits = currentStudentCredits + credits;
            let message = `Apakah Anda yakin ingin mendaftar mata kuliah "${courseName}" (${credits} SKS)?`;
            
            if (totalCredits > 24) {
                showValidationError(`Tidak dapat mendaftar!\nTotal SKS akan menjadi ${totalCredits} yang melebihi batas maksimum 24 SKS.`);
                return;
            }
            
            if (totalCredits > 20) {
                message += `\n\nPerhatian: Total SKS Anda akan menjadi ${totalCredits}/24 SKS`;
            }
            
            if (confirm(message)) {
                window.location.href = `<?= base_url('/courses/enroll/') ?>${courseId}`;
            }
        }

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

        document.getElementById('editModal')?.addEventListener('hidden.bs.modal', function() {
            clearFieldErrors();
        });

        let formSubmitted = false;
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (formSubmitted) {
                    e.preventDefault();
                    return false;
                }
                formSubmitted = true;
                
                setTimeout(() => {
                    formSubmitted = false;
                }, 5000);
            });
        });

        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn && !submitBtn.disabled) {
                    const originalText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
                    
                    setTimeout(() => {
                        if (submitBtn.disabled) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }
                    }, 3000);
                }
            });
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const activeModal = document.querySelector('.modal.show');
                if (activeModal) {
                    const modal = bootstrap.Modal.getInstance(activeModal);
                    if (modal) {
                        modal.hide();
                    }
                }
            }
        });

        window.addEventListener('unhandledrejection', function(e) {
            console.error('Unhandled promise rejection:', e.reason);
            showValidationError('Terjadi kesalahan sistem. Silakan refresh halaman dan coba lagi.');
        });

        document.addEventListener('DOMContentLoaded', function() {
            setActiveMenu();
            if (document.getElementById('totalCredits')) {
                updateTotalCredits();
            }
            
            console.log('Courses page loaded successfully at ' + new Date().toLocaleString('id-ID', { timeZone: 'Asia/Jakarta' }));
            console.log('Current user role:', '<?= session()->get('role') ?>');
            console.log('Total courses:', courses.length);
            console.table('Courses Data:', courses); 
            
            <?php if (session()->get('role') === 'student'): ?>
            console.log('Current student credits:', currentStudentCredits);
            <?php endif; ?>
        });
    </script>
</body>
</html>