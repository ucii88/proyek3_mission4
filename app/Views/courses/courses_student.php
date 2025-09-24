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

<?php if (empty($courses)): ?>
    <div class="text-center py-5">
        <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
        <h4 class="text-muted">Belum ada mata kuliah</h4>
        <p class="text-muted">Belum ada mata kuliah yang tersedia untuk didaftar</p>
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($courses as $course): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card course-card h-100 position-relative">
                    <?php if (isset($enrolled) && in_array($course['course_id'], array_column($enrolled, 'course_id'))): ?>
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
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="modal fade" id="enrollConfirmationModal" tabindex="-1" aria-labelledby="enrollConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="enrollConfirmationModalLabel">Konfirmasi Pendaftaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="modalConfirmMessage"></p>
                <small class="text-muted">Pastikan Anda sudah yakin dengan pilihan ini.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="confirmEnrollBtn">Ya, Daftarkan</button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentStudentCredits = <?= $total_credits ?? 0 ?>;
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
        if (totalCreditsSpan) totalCreditsSpan.textContent = `${totalCredits}/24`;
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

    function showValidationError(message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show mt-3';
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
        
        let confirmMessage = `Jumlah mata kuliah: ${selectedCourses.length}<br>`;
        confirmMessage += `Total SKS baru: ${selectedCourses.reduce((sum, course) => sum + course.credits, 0)} SKS<br>`;
        confirmMessage += `Total SKS keseluruhan: ${totalCredits}/24 SKS<br><br>`;
        if (totalCredits > 20) {
            confirmMessage += `⚠️ **PERINGATAN:** Total SKS mendekati batas maksimum!<br><br>`;
        }

        const modalBody = document.getElementById('modalConfirmMessage');
        if (modalBody) modalBody.innerHTML = confirmMessage;
        
        const enrollModal = new bootstrap.Modal(document.getElementById('enrollConfirmationModal'));
        enrollModal.show();

        const confirmBtn = document.getElementById('confirmEnrollBtn');
        if (confirmBtn) {
            confirmBtn.onclick = () => {
                enrollModal.hide();
                this.submit();
            };
        }
    });

    function enrollSingleCourse(courseId, courseName, credits) {
        const totalCredits = currentStudentCredits + credits;
        if (totalCredits > 24) {
            showValidationError(`Tidak dapat mendaftar!\nTotal SKS akan menjadi ${totalCredits} yang melebihi batas maksimum 24 SKS.`);
            return;
        }

        let message = `Apakah Anda yakin ingin mendaftar mata kuliah "<strong>${esc(courseName)}</strong>" (${credits} SKS)?`;
        if (totalCredits > 20) {
            message += `<br><br>Perhatian: Total SKS Anda akan menjadi ${totalCredits}/24 SKS.`;
        }

        const modalBody = document.getElementById('modalConfirmMessage');
        if (modalBody) modalBody.innerHTML = message;
        
        const enrollModal = new bootstrap.Modal(document.getElementById('enrollConfirmationModal'));
        enrollModal.show();

        const confirmBtn = document.getElementById('confirmEnrollBtn');
        if (confirmBtn) {
            confirmBtn.onclick = () => {
                enrollModal.hide();
                window.location.href = `<?= base_url('/courses/enroll/') ?>${courseId}`;
            };
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateTotalCredits();
    });

    function esc(string) {
        return string.replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
    }
</script>