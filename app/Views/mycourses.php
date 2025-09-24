<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses - Sistem Akademik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <script>
        (function() {
            'use strict';
            console.log('🛡️ Loading browser dialog blocker...');
            
            // Override IMMEDIATELY
            const originalConfirm = window.confirm;
            const originalAlert = window.alert;
            
            window.confirm = function(message) {
                console.error('🚫 BLOCKED: Browser confirm() with message:', message);
                setTimeout(function() {
                    showCustomConfirmModal('Konfirmasi', message);
                }, 0);
                return false;
            };
            
            window.alert = function(message) {
                console.error('🚫 BLOCKED: Browser alert() with message:', message);
                setTimeout(function() {
                    showCustomAlert(message);
                }, 0);
            };
            
            console.log('🛡️ Browser dialogs blocked successfully');
        })();
    </script>
    <style>
        body { background-color: #f8f9fa; }
        .navbar { box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .page-header {
            background: #28a745;
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
        .nav-item .nav-link.active {
            background-color: #3c59dbff;
            color: white !important;
            border-radius: 5px;
        }
        .is-invalid {
            border-color: #dc3545 !important;
        }
        .modal-content {
            border-radius: 10px;
        }
        .credit-summary {
            background: rgba(255,255,255,0.9);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .confirm-unenroll {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 1rem;
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
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/courses') ?>">
                            <i class="fas fa-book me-1"></i>All Courses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('/mycourses') ?>">
                            <i class="fas fa-user-graduate me-1"></i>My Courses
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

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2><i class="fas fa-user-graduate me-2"></i>My Courses</h2>
                    <p class="mb-0 opacity-75">Kelola mata kuliah yang sudah Anda ambil</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="credit-summary bg-white text-dark p-3 rounded">
                        <h5 class="mb-1">Total SKS: <?= $total_credits ?? 0 ?>/24</h5>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar <?= ($total_credits ?? 0) > 20 ? 'bg-warning' : 'bg-success' ?>" 
                                 style="width: <?= min(100, (($total_credits ?? 0) / 24) * 100) ?>%"></div>
                        </div>
                        <small class="text-muted">Batas maksimal: 24 SKS</small>
                    </div>
                </div>
            </div>
        </div>

        <?php if (empty($enrolled_courses)): ?>
            <div class="text-center py-5">
                <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Belum ada mata kuliah yang diambil</h4>
                <p class="text-muted">Silakan kunjungi halaman "All Courses" untuk mendaftar mata kuliah</p>
                <a href="<?= base_url('/courses') ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Daftar Mata Kuliah
                </a>
            </div>
        <?php else: ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-times-circle me-2"></i>Batalkan Pendaftaran Mata Kuliah</h5>
                </div>
                <div class="card-body">
                    <form id="unenrollForm" method="post" action="<?= base_url('/courses/unenrollMultiple') ?>">
                        <?= csrf_field() ?>
                        <div class="row" id="courseCheckboxes">
                            <?php foreach ($enrolled_courses as $course): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="course_ids[]" 
                                               value="<?= $course['course_id'] ?>" 
                                               id="unenroll_<?= $course['course_id'] ?>"
                                               data-course-name="<?= esc($course['course_name']) ?>"
                                               data-credits="<?= $course['credits'] ?>"
                                               onchange="updateUnenrollSelection()">
                                        <label class="form-check-label" for="unenroll_<?= $course['course_id'] ?>">
                                            <strong><?= esc($course['course_name']) ?></strong> (<?= $course['credits'] ?> SKS)
                                            <small class="text-muted d-block">
                                                Terdaftar: <?= date('d/m/Y', strtotime($course['enroll_date'])) ?>
                                            </small>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div id="unenrollSummary" class="text-muted">
                                Pilih mata kuliah yang ingin dibatalkan
                            </div>
                            <button type="submit" class="btn btn-danger" id="unenrollBtn" disabled>
                                <i class="fas fa-trash me-2"></i>Batalkan Pendaftaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-list me-2"></i>Mata Kuliah yang Diambil</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($enrolled_courses as $course): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card course-card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="fas fa-book me-2 text-success"></i>
                                            <?= esc($course['course_name']) ?>
                                        </h5>
                                        <p class="card-text">
                                            <i class="fas fa-credit-card me-2 text-info"></i>
                                            <strong><?= $course['credits'] ?> SKS</strong>
                                        </p>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-2"></i>
                                                Terdaftar: <?= date('d M Y', strtotime($course['enroll_date'])) ?>
                                            </small>
                                        </p>
                                        
                                        <div class="d-grid">
                                            <button class="btn btn-danger" 
                                                    onclick="showUnenrollConfirmation(<?= $course['course_id'] ?>, '<?= esc($course['course_name']) ?>', <?= $course['credits'] ?>)">
                                                <i class="fas fa-times me-2"></i>Batalkan
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="modal fade" id="genericConfirmModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="genericConfirmTitle">Konfirmasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="genericConfirmMessage"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="button" class="btn btn-danger" id="genericConfirmBtn">
                        <i class="fas fa-check me-2"></i>Konfirmasi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="multipleUnenrollModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>Konfirmasi Pembatalan Pendaftaran
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="confirm-unenroll">
                        <p class="mb-3"><strong>Mata kuliah yang akan dibatalkan:</strong></p>
                        <div id="multipleUnenrollList"></div>
                        <div class="mt-3 p-3 bg-warning bg-opacity-10 rounded">
                            <small class="text-muted">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Tindakan ini akan membatalkan pendaftaran Anda dari mata kuliah tersebut.
                                Anda dapat mendaftar kembali kapan saja melalui halaman "All Courses"
                            </small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmMultipleUnenroll">
                        <i class="fas fa-trash me-2"></i>Ya, Batalkan Semua
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="unenrollModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>Konfirmasi Pembatalan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="confirm-unenroll">
                        <p class="mb-3">Apakah Anda yakin ingin membatalkan pendaftaran mata kuliah:</p>
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title text-primary" id="unenrollCourseName"></h6>
                                <p class="card-text"><strong>SKS:</strong> <span id="unenrollCredits"></span></p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Anda dapat mendaftar kembali kapan saja melalui halaman "All Courses"
                            </small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <form id="singleUnenrollForm" method="post" style="display:inline;">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Ya, Batalkan
                        </button>
                    </form>
                </div>
            </div>
        </div>
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
     
        function showCustomConfirmModal(title, message, onConfirm) {
            const existingModal = document.getElementById('customConfirmModal');
            if (existingModal) existingModal.remove();

            const modalHTML = `
                <div class="modal fade" id="customConfirmModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">${title}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">${message.replace(/\n/g, '<br>')}</div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="button" class="btn btn-danger" id="customConfirmBtn">OK</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            document.body.insertAdjacentHTML('beforeend', modalHTML);

            const modal = document.getElementById('customConfirmModal');
            const confirmBtn = document.getElementById('customConfirmBtn');
            confirmBtn.addEventListener('click', function() {
                if (onConfirm) onConfirm();
                bootstrap.Modal.getInstance(modal).hide();
            });

            new bootstrap.Modal(modal).show();
            modal.addEventListener('hidden.bs.modal', function() {
                modal.remove();
            });
        }

        function showCustomAlert(message) {
            showCustomConfirmModal('Peringatan', message, null);
        }


        let enrolledCourses = <?= json_encode($enrolled_courses ?? []) ?>;
        let selectedForUnenroll = [];

    
        function updateUnenrollSelection() {
            const checkboxes = document.querySelectorAll('#courseCheckboxes input[type="checkbox"]:checked');
            const summaryDiv = document.getElementById('unenrollSummary');
            const unenrollBtn = document.getElementById('unenrollBtn');

            selectedForUnenroll = [];
            let totalCredits = 0;

            checkboxes.forEach(checkbox => {
                const courseName = checkbox.getAttribute('data-course-name');
                const credits = parseInt(checkbox.getAttribute('data-credits'));
                selectedForUnenroll.push({ id: checkbox.value, name: courseName, credits: credits });
                totalCredits += credits;
            });

            if (selectedForUnenroll.length > 0) {
                summaryDiv.innerHTML = `
                    <strong>Dipilih: ${selectedForUnenroll.length} mata kuliah</strong><br>
                    <small>Total SKS yang akan dibatalkan: ${totalCredits}</small>
                `;
                unenrollBtn.disabled = false;
                unenrollBtn.classList.remove('btn-secondary');
                unenrollBtn.classList.add('btn-danger');
            } else {
                summaryDiv.innerHTML = 'Pilih mata kuliah yang ingin dibatalkan';
                unenrollBtn.disabled = true;
                unenrollBtn.classList.remove('btn-danger');
                unenrollBtn.classList.add('btn-secondary');
            }
        }

     
        function initializeFormHandling() {
            const unenrollForm = document.getElementById('unenrollForm');
            if (unenrollForm) {
                unenrollForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    if (selectedForUnenroll.length === 0) {
                        showValidationError('Pilih setidaknya satu mata kuliah untuk dibatalkan pendaftarannya');
                        return false;
                    }

                    showMultipleUnenrollConfirmation(this.querySelectorAll('input[type="checkbox"]:checked'));
                    return false;
                });
            }
        }

   
        function showUnenrollConfirmation(courseId, courseName, credits) {
            document.getElementById('unenrollCourseName').textContent = courseName;
            document.getElementById('unenrollCredits').textContent = credits;
            document.getElementById('singleUnenrollForm').action = `<?= base_url('/courses/unenroll/') ?>${courseId}`;
            new bootstrap.Modal(document.getElementById('unenrollModal')).show();
        }


        function showLogoutConfirmation() {
            new bootstrap.Modal(document.getElementById('logoutModal')).show();
        }

    
        function showValidationError(message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="fas fa-exclamation-triangle me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.container').insertBefore(alertDiv, document.querySelector('.container').firstElementChild);
            setTimeout(() => alertDiv.remove(), 5000);
        }

        function showCustomConfirmation(title, message, onConfirm) {
            document.getElementById('genericConfirmTitle').textContent = title;
            document.getElementById('genericConfirmMessage').innerHTML = message.replace(/\n/g, '<br>');
            const confirmBtn = document.getElementById('genericConfirmBtn');
            const newConfirmBtn = confirmBtn.cloneNode(true);
            confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
            newConfirmBtn.addEventListener('click', function() {
                if (onConfirm) onConfirm();
                bootstrap.Modal.getInstance(document.getElementById('genericConfirmModal')).hide();
            });
            new bootstrap.Modal(document.getElementById('genericConfirmModal')).show();
        }

        function showMultipleUnenrollConfirmation(checkboxes) {
            const courseData = [];
            let totalCredits = 0;

            checkboxes.forEach(checkbox => {
                const courseName = checkbox.getAttribute('data-course-name');
                const credits = parseInt(checkbox.getAttribute('data-credits'));
                courseData.push({ name: courseName, credits: credits });
                totalCredits += credits;
            });

            let listHtml = '<ul class="list-group">';
            courseData.forEach((course, index) => {
                listHtml += `<li class="list-group-item d-flex justify-content-between align-items-center">
                    ${index + 1}. ${course.name}
                    <span class="badge bg-primary rounded-pill">${course.credits} SKS</span>
                </li>`;
            });
            listHtml += '</ul>';
            listHtml += `<div class="mt-3"><strong>Total: ${courseData.length} mata kuliah (${totalCredits} SKS)</strong></div>`;

            document.getElementById('multipleUnenrollList').innerHTML = listHtml;

            document.getElementById('confirmMultipleUnenroll').onclick = function() {
                document.getElementById('unenrollForm').submit();
                const modal = bootstrap.Modal.getInstance(document.getElementById('multipleUnenrollModal'));
                if (modal) modal.hide();
            };

            new bootstrap.Modal(document.getElementById('multipleUnenrollModal')).show();
        }


        function setActiveMenu() {
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                if (link.href === window.location.href) link.classList.add('active');
                else link.classList.remove('active');
            });
        }

   
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(alert => {
                try { new bootstrap.Alert(alert).close(); } catch (e) {}
            });
        }, 5000);


        let formSubmitted = false;
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (formSubmitted) {
                    e.preventDefault();
                    return false;
                }
                formSubmitted = true;
                setTimeout(() => formSubmitted = false, 3000);
            });
        });

        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn && !submitBtn.disabled) {
                    const originalText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
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
                if (activeModal) bootstrap.Modal.getInstance(activeModal).hide();
            }
        });

   
        document.addEventListener('DOMContentLoaded', function() {
            initializeFormHandling();
            setActiveMenu();
            console.log('My Courses page loaded successfully - ALL BROWSER DIALOGS BLOCKED');
            console.log('Total enrolled courses:', enrolledCourses.length);
            console.log('Total credits:', <?= $total_credits ?? 0 ?>);
        });

   
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('hidden.bs.modal', function() {
                const forms = this.querySelectorAll('form');
                forms.forEach(form => {
                    if (form.id !== 'singleUnenrollForm') form.reset();
                });
            });
        });

      
        window.addEventListener('unhandledrejection', function(e) {
            console.error('Unhandled promise rejection:', e.reason);
            showValidationError('Terjadi kesalahan sistem. Silakan refresh halaman dan coba lagi.');
        });
    </script>
</body>
</html>