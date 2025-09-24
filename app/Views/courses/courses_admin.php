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
            
            <?php if (empty($courses)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Belum ada mata kuliah</h4>
                    <p class="text-muted">Silakan tambah mata kuliah baru menggunakan form di atas</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($courses as $course): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card course-card h-100 position-relative">
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

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

<script>
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
    function validateCourseForm(courseName, credits, nameFieldId, creditsFieldId) {
        let isValid = true;
        clearFieldErrors();
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
</script>