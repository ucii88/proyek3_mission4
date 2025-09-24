<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-list me-2"></i>Daftar Mahasiswa</h5>
        <div>
            <button class="btn btn-outline-primary btn-sm" onclick="refreshStudentTable()">
                <i class="fas fa-refresh me-1"></i>Refresh
            </button>
            <button class="btn btn-outline-success btn-sm" onclick="exportStudentData()">
                <i class="fas fa-download me-1"></i>Export
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped" id="student-table">
                <thead class="table-dark">
                    <tr>
                        <th>
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                        </th>
                        <th>#</th>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Tahun Masuk</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="student-list">
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                <span class="text-muted">Dipilih: <span id="selectedCount">0</span> mahasiswa</span>
            </div>
            <div>
                <button class="btn btn-danger btn-sm" id="bulkDeleteBtn" onclick="showBulkDeleteConfirmation()" disabled>
                    <i class="fas fa-trash me-1"></i>Hapus Dipilih
                </button>
            </div>
        </div>
    </div>
</div>