<div class="card mb-4">
    <div class="card-header">
        <h5><i class="fas fa-plus me-2"></i>Tambah Mahasiswa Baru</h5>
    </div>
    <div class="card-body">
        <form id="addStudentForm" novalidate>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" id="username" name="username" class="form-control" required>
                        <div class="invalid-feedback"></div>
                        <div class="form-text">Minimal 3 karakter, unik</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" id="email" name="email" class="form-control" required>
                        <div class="invalid-feedback"></div>
                        <div class="form-text">Format email yang valid</div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" id="password" name="password" class="form-control" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('password')">
                                <i class="fas fa-eye" id="password-toggle-icon"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback"></div>
                        <div class="form-text">Minimal 6 karakter</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" id="full_name" name="full_name" class="form-control" required>
                        <div class="invalid-feedback"></div>
                        <div class="form-text">Minimal 2 karakter</div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="entry_year" class="form-label">Tahun Masuk <span class="text-danger">*</span></label>
                        <input type="number" id="entry_year" name="entry_year" class="form-control" 
                               min="2000" max="<?= date('Y') ?>" value="<?= date('Y') ?>" required>
                        <div class="invalid-feedback"></div>
                        <div class="form-text">2000 - <?= date('Y') ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary d-block w-100">
                            <i class="fas fa-plus me-2"></i>Tambah Mahasiswa
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>