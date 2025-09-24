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
        .is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }
        .modal-content {
            border-radius: 10px;
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
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .spinner-border-custom {
            width: 3rem;
            height: 3rem;
            color: white;
        }
    </style>
</head>
<body>
    <div class="loading-overlay" id="loadingOverlay">
        <div class="text-center">
            <div class="spinner-border spinner-border-custom" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="text-white mt-2">Processing...</div>
        </div>
    </div>
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
        <div id="alertContainer"></div>
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2><i class="fas fa-users me-2"></i>Manage Students</h2>
                    <p class="mb-0 opacity-75">Tambah, edit, dan kelola data mahasiswa</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="bg-white text-dark p-3 rounded">
                        <h6 class="mb-1">Total Mahasiswa: <span id="totalStudents">0</span></h6>
                        <small class="text-muted">Data terupdate secara real-time</small>
                    </div>
                </div>
            </div>
        </div>
        
        <?= $this->include('students/students_form') ?>
        <?= $this->include('students/students_table') ?>

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
                        <input type="hidden" name="confirm_logout" value="yes">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Ya, Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="viewStudentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-eye me-2"></i>Detail Mahasiswa
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="studentDetail">
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editStudentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>Edit Mahasiswa
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editStudentForm" novalidate>
                        <input type="hidden" id="edit_user_id" name="user_id">
                        <div class="mb-3">
                            <label for="edit_username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" id="edit_username" name="username" class="form-control" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" id="edit_email" name="email" class="form-control" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_full_name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" id="edit_full_name" name="full_name" class="form-control" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_entry_year" class="form-label">Tahun Masuk <span class="text-danger">*</span></label>
                            <input type="number" id="edit_entry_year" name="entry_year" class="form-control" 
                                   min="2000" max="<?= date('Y') ?>" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_password" class="form-label">Password (kosongkan jika tidak ingin ubah)</label>
                            <div class="input-group">
                                <input type="password" id="edit_password" name="password" class="form-control">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('edit_password')">
                                    <i class="fas fa-eye" id="edit_password-toggle-icon"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" form="editStudentForm" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>Konfirmasi Hapus
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Peringatan!</strong> Tindakan ini tidak dapat dibatalkan.
                    </div>
                    <div id="confirm-message"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="button" class="btn btn-danger" id="confirm-delete-btn">
                        <i class="fas fa-trash me-2"></i>Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let students = [];
        let selectedStudents = [];
        function updateStudentTable() {
            const tbody = document.getElementById('student-list');
            tbody.innerHTML = '';
            
            if (students.length === 0) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td colspan="7" class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada data mahasiswa</h5>
                        <p class="text-muted">Silakan tambah mahasiswa baru menggunakan form di atas</p>
                    </td>
                `;
                tbody.appendChild(tr);
                return;
            }
            students.forEach((student, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>
                        <input type="checkbox" class="student-checkbox" value="${student.user_id}" 
                               onchange="updateSelectedStudents()">
                    </td>
                    <td>${index + 1}</td>
                    <td>${esc(student.username)}</td>
                    <td>${esc(student.full_name)}</td>
                    <td>${esc(student.email)}</td>
                    <td>${esc(student.entry_year) || 'N/A'}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-info" onclick="viewStudent(${student.user_id})" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning" onclick="editStudent(${student.user_id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger" onclick="showDeleteConfirmation(${student.user_id}, '${esc(student.full_name)}')" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
            document.getElementById('totalStudents').textContent = students.length;
        }
        async function fetchStudents() {
            try {
                showLoading(true);
                const response = await fetch('<?= base_url('/students/getAll') ?>', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                if (Array.isArray(data)) {
                    students = data;
                } else {
                    throw new Error('Data received is not an array');
                }
                updateStudentTable();
                showAlert('Data mahasiswa berhasil dimuat', 'success');
                console.log('=== DATA MAHASISWA DARI DATABASE ===');
                console.table(students);
            } catch (error) {
                console.error('Error fetching students:', error);
                showAlert('Gagal memuat data mahasiswa: ' + error.message, 'danger');
                students = [];
                updateStudentTable();
            } finally {
                showLoading(false);
            }
        }
        document.getElementById('addStudentForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = {
                username: document.getElementById('username').value.trim(),
                email: document.getElementById('email').value.trim(),
                password: document.getElementById('password').value.trim(),
                full_name: document.getElementById('full_name').value.trim(),
                entry_year: document.getElementById('entry_year').value
            };
            if (!validateStudentForm(formData)) {
                return;
            }
            try {
                showLoading(true);
                const response = await fetch('<?= base_url('/students/create') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                });
                const result = await response.json();
                if (response.ok && result.success) {
                    showAlert('Mahasiswa berhasil ditambahkan!', 'success');
                    document.getElementById('addStudentForm').reset();
                    clearValidationErrors();
                    await fetchStudents();
                } else {
                    if (result.errors) {
                        displayValidationErrors(result.errors);
                    } else {
                        showAlert(result.error || 'Gagal menambahkan mahasiswa', 'danger');
                    }
                }
            } catch (error) {
                console.error('Error creating student:', error);
                showAlert('Terjadi kesalahan sistem: ' + error.message, 'danger');
            } finally {
                showLoading(false);
            }
        });
        document.getElementById('editStudentForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = {
                user_id: document.getElementById('edit_user_id').value,
                username: document.getElementById('edit_username').value.trim(),
                email: document.getElementById('edit_email').value.trim(),
                full_name: document.getElementById('edit_full_name').value.trim(),
                entry_year: document.getElementById('edit_entry_year').value,
                password: document.getElementById('edit_password').value.trim()
            };
            if (!validateStudentForm(formData, true)) {
                return;
            }
            try {
                showLoading(true);
                const response = await fetch('<?= base_url('/students/update') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                });
                const result = await response.json();
                if (response.ok && result.success) {
                    showAlert('Data mahasiswa berhasil diperbarui!', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('editStudentModal')).hide();
                    await fetchStudents();
                } else {
                    if (result.errors) {
                        displayValidationErrors(result.errors, 'edit_');
                    } else {
                        showAlert(result.error || 'Gagal memperbarui data mahasiswa', 'danger');
                    }
                }
            } catch (error) {
                console.error('Error updating student:', error);
                showAlert('Terjadi kesalahan sistem: ' + error.message, 'danger');
            } finally {
                showLoading(false);
            }
        });
        function validateStudentForm(data, isEdit = false) {
            clearValidationErrors(isEdit ? 'edit_' : '');
            let isValid = true;
            const prefix = isEdit ? 'edit_' : '';
            if (!data.username) {
                showFieldError(prefix + 'username', 'Username wajib diisi');
                isValid = false;
            } else if (data.username.length < 3) {
                showFieldError(prefix + 'username', 'Username harus minimal 3 karakter');
                isValid = false;
            } else if (data.username.length > 50) {
                showFieldError(prefix + 'username', 'Username maksimal 50 karakter');
                isValid = false;
            } else if (!/^[a-zA-Z0-9_]+$/.test(data.username)) {
                showFieldError(prefix + 'username', 'Username hanya boleh mengandung huruf, angka, dan underscore');
                isValid = false;
            }
            if (!data.email) {
                showFieldError(prefix + 'email', 'Email wajib diisi');
                isValid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.email)) {
                showFieldError(prefix + 'email', 'Format email tidak valid');
                isValid = false;
            }
            if (!isEdit || data.password) {
                if (!data.password) {
                    showFieldError(prefix + 'password', 'Password wajib diisi');
                    isValid = false;
                } else if (data.password.length < 6) {
                    showFieldError(prefix + 'password', 'Password harus minimal 6 karakter');
                    isValid = false;
                }
            }
            if (!data.full_name) {
                showFieldError(prefix + 'full_name', 'Nama lengkap wajib diisi');
                isValid = false;
            } else if (data.full_name.length < 2) {
                showFieldError(prefix + 'full_name', 'Nama lengkap harus minimal 2 karakter');
                isValid = false;
            } else if (data.full_name.length > 100) {
                showFieldError(prefix + 'full_name', 'Nama lengkap maksimal 100 karakter');
                isValid = false;
            }
            const currentYear = new Date().getFullYear();
            if (!data.entry_year) {
                showFieldError(prefix + 'entry_year', 'Tahun masuk wajib diisi');
                isValid = false;
            } else if (data.entry_year < 2000 || data.entry_year > currentYear) {
                showFieldError(prefix + 'entry_year', `Tahun masuk harus antara 2000 - ${currentYear}`);
                isValid = false;
            }
            return isValid;
        }
        function showFieldError(fieldId, message) {
            const field = document.getElementById(fieldId);
            const feedback = field.parentElement.querySelector('.invalid-feedback') || 
                           field.parentElement.parentElement.querySelector('.invalid-feedback');
            field.classList.add('is-invalid');
            if (feedback) {
                feedback.textContent = message;
            }
        }
        function clearValidationErrors(prefix = '') {
            const fields = ['username', 'email', 'password', 'full_name', 'entry_year'];
            fields.forEach(field => {
                const element = document.getElementById(prefix + field);
                if (element) {
                    element.classList.remove('is-invalid');
                    const feedback = element.parentElement.querySelector('.invalid-feedback') || 
                                   element.parentElement.parentElement.querySelector('.invalid-feedback');
                    if (feedback) {
                        feedback.textContent = '';
                    }
                }
            });
        }
        function displayValidationErrors(errors, prefix = '') {
            Object.keys(errors).forEach(field => {
                showFieldError(prefix + field, errors[field]);
            });
        }
        async function viewStudent(userId) {
            try {
                showLoading(true);
                const response = await fetch(`<?= base_url('/students/view/') ?>${userId}`);
                const data = await response.json();
                if (response.ok && data.student) {
                    const student = data.student;
                    const enrolledCourses = data.enrolled_courses || [];
                    let coursesHtml = '';
                    if (enrolledCourses.length > 0) {
                        coursesHtml = '<ul class="list-group">';
                        enrolledCourses.forEach(course => {
                            coursesHtml += `<li class="list-group-item d-flex justify-content-between align-items-center">
                                ${esc(course.course_name)}
                                <span class="badge bg-primary rounded-pill">${course.credits} SKS</span>
                            </li>`;
                        });
                        coursesHtml += '</ul>';
                        coursesHtml += `<div class="mt-2"><small class="text-muted">Total: ${enrolledCourses.reduce((sum, course) => sum + parseInt(course.credits), 0)} SKS</small></div>`;
                    } else {
                        coursesHtml = '<p class="text-muted">Belum ada mata kuliah yang diambil</p>';
                    }
                    document.getElementById('studentDetail').innerHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-user me-2"></i>Informasi Personal</h6>
                                <table class="table table-sm">
                                    <tr><td><strong>Username:</strong></td><td>${esc(student.username)}</td></tr>
                                    <tr><td><strong>Nama Lengkap:</strong></td><td>${esc(student.full_name)}</td></tr>
                                    <tr><td><strong>Email:</strong></td><td>${esc(student.email)}</td></tr>
                                    <tr><td><strong>Tahun Masuk:</strong></td><td>${esc(student.entry_year) || 'N/A'}</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-graduation-cap me-2"></i>Mata Kuliah yang Diambil</h6>
                                ${coursesHtml}
                            </div>
                        </div>
                    `;
                    new bootstrap.Modal(document.getElementById('viewStudentModal')).show();
                } else {
                    showAlert(data.error || 'Gagal memuat data mahasiswa', 'danger');
                }
            } catch (error) {
                console.error('Error viewing student:', error);
                showAlert('Terjadi kesalahan sistem: ' + error.message, 'danger');
            } finally {
                showLoading(false);
            }
        }
        async function editStudent(userId) {
            try {
                showLoading(true);
                const response = await fetch(`<?= base_url('/students/view/') ?>${userId}`);
                const data = await response.json();
                if (response.ok && data.student) {
                    const student = data.student;
                    document.getElementById('edit_user_id').value = student.user_id;
                    document.getElementById('edit_username').value = student.username;
                    document.getElementById('edit_email').value = student.email;
                    document.getElementById('edit_full_name').value = student.full_name;
                    document.getElementById('edit_entry_year').value = student.entry_year || '';
                    document.getElementById('edit_password').value = '';
                    
                    clearValidationErrors('edit_');
                    new bootstrap.Modal(document.getElementById('editStudentModal')).show();
                } else {
                    showAlert(data.error || 'Gagal memuat data untuk edit', 'danger');
                }
            } catch (error) {
                console.error('Error loading student for edit:', error);
                showAlert('Terjadi kesalahan sistem: ' + error.message, 'danger');
            } finally {
                showLoading(false);
            }
        }
        function showDeleteConfirmation(userId, fullName) {
            document.getElementById('confirm-message').innerHTML = `
                <p>Apakah Anda yakin ingin menghapus mahasiswa:</p>
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title text-primary"><i class="fas fa-user me-2"></i>${esc(fullName)}</h6>
                        <p class="card-text"><small class="text-muted">ID: ${userId}</small></p>
                    </div>
                </div>
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Semua data termasuk enrollment akan dihapus permanen.
                    </small>
                </div>
            `;
            document.getElementById('confirm-delete-btn').onclick = () => deleteStudentConfirmed(userId);
            new bootstrap.Modal(document.getElementById('confirmDeleteModal')).show();
        }
        async function deleteStudentConfirmed(userId) {
            try {
                showLoading(true);
                const response = await fetch(`<?= base_url('/students/delete/') ?>${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });
                const result = await response.json();
                if (response.ok && result.success) {
                    showAlert('Mahasiswa berhasil dihapus!', 'success');
                    await fetchStudents();
                } else {
                    showAlert(result.error || 'Gagal menghapus mahasiswa', 'danger');
                }
            } catch (error) {
                console.error('Error deleting student:', error);
                showAlert('Terjadi kesalahan sistem: ' + error.message, 'danger');
            } finally {
                showLoading(false);
                bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal')).hide();
            }
        }
        function showBulkDeleteConfirmation() {
            if (selectedStudents.length === 0) {
                showAlert('Pilih mahasiswa yang ingin dihapus', 'warning');
                return;
            }
            const selectedStudentData = students.filter(s => selectedStudents.includes(s.user_id.toString()));
            let studentList = '<ul class="list-group">';
            selectedStudentData.forEach(student => {
                studentList += `<li class="list-group-item">${esc(student.full_name)} (${esc(student.username)})</li>`;
            });
            studentList += '</ul>';
            document.getElementById('confirm-message').innerHTML = `
                <p><strong>Hapus ${selectedStudents.length} mahasiswa:</strong></p>
                ${studentList}
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Semua data dan enrollment akan dihapus permanen.
                    </small>
                </div>
            `;
            document.getElementById('confirm-delete-btn').onclick = bulkDeleteConfirmed;
            new bootstrap.Modal(document.getElementById('confirmDeleteModal')).show();
        }
        async function bulkDeleteConfirmed() {
            try {
                showLoading(true);
                const response = await fetch('<?= base_url('/students/bulk-delete') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        student_ids: selectedStudents
                    })
                });
                const result = await response.json();
                if (response.ok) {
                    showAlert(`${result.deleted_count} mahasiswa berhasil dihapus!`, 'success');
                    selectedStudents = [];
                    updateSelectedStudents();
                    await fetchStudents();
                } else {
                    showAlert(result.error || 'Gagal menghapus mahasiswa', 'danger');
                }
            } catch (error) {
                console.error('Error bulk deleting students:', error);
                showAlert('Terjadi kesalahan sistem: ' + error.message, 'danger');
            } finally {
                showLoading(false);
                bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal')).hide();
            }
        }
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.student-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            updateSelectedStudents();
        }
        function updateSelectedStudents() {
            const checkboxes = document.querySelectorAll('.student-checkbox:checked');
            selectedStudents = Array.from(checkboxes).map(cb => cb.value);
            document.getElementById('selectedCount').textContent = selectedStudents.length;
            document.getElementById('bulkDeleteBtn').disabled = selectedStudents.length === 0;
            const totalCheckboxes = document.querySelectorAll('.student-checkbox').length;
            const selectAll = document.getElementById('selectAll');
            if (selectedStudents.length === 0) {
                selectAll.indeterminate = false;
                selectAll.checked = false;
            } else if (selectedStudents.length === totalCheckboxes) {
                selectAll.indeterminate = false;
                selectAll.checked = true;
            } else {
                selectAll.indeterminate = true;
            }
        }
        function refreshStudentTable() {
            fetchStudents();
        }
        function exportStudentData() {
            if (students.length === 0) {
                showAlert('Tidak ada data untuk diekspor', 'warning');
                return;
            }
            const csvContent = generateCSV(students);
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', `students_${new Date().toISOString().slice(0,10)}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            showAlert('Data mahasiswa berhasil diekspor!', 'success');
        }
        function generateCSV(data) {
            const headers = ['Username', 'Nama Lengkap', 'Email', 'Tahun Masuk'];
            const csvContent = [
                headers.join(','),
                ...data.map(student => [
                    `"${student.username}"`,
                    `"${student.full_name}"`,
                    `"${student.email}"`,
                    `"${student.entry_year || 'N/A'}"`
                ].join(','))
            ].join('\n');
            return csvContent;
        }
        function togglePasswordVisibility(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '-toggle-icon');
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        function showLogoutConfirmation() {
            const modal = new bootstrap.Modal(document.getElementById('logoutModal'));
            modal.show();
        }
        function showLoading(show) {
            document.getElementById('loadingOverlay').style.display = show ? 'flex' : 'none';
        }
        function showAlert(message, type = 'info') {
            const alertContainer = document.getElementById('alertContainer');
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            alertContainer.appendChild(alertDiv);
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
        function esc(string) {
            if (!string) return '';
            return String(string).replace(/&/g, "&amp;")
                                .replace(/</g, "&lt;")
                                .replace(/>/g, "&gt;")
                                .replace(/"/g, "&quot;")
                                .replace(/'/g, "&#039;");
        }
        document.addEventListener('DOMContentLoaded', function() {
            fetchStudents();
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                if (link.href === window.location.href) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
            console.log('Student management page loaded successfully at ' + new Date().toLocaleString('id-ID', { timeZone: 'Asia/Jakarta' }));
        });
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('hidden.bs.modal', function() {
                const forms = this.querySelectorAll('form');
                forms.forEach(form => {
                    form.reset();
                    clearValidationErrors();
                    clearValidationErrors('edit_');
                });
            });
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const activeModal = document.querySelector('.modal.show');
                if (activeModal) {
                    const modal = bootstrap.Modal.getInstance(activeModal);
                    if (modal) modal.hide();
                }
            }
        });
        window.addEventListener('unhandledrejection', function(e) {
            console.error('Unhandled promise rejection:', e.reason);
            showAlert('Terjadi kesalahan sistem. Silakan refresh halaman dan coba lagi.', 'danger');
        });
    </script>
</body>
</html>