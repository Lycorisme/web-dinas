<?php
// dashboard/widgets/filter-widget.php
?>
<div class="card">
    <div class="card-body">
        <div class="row align-items-end">
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label>Wilayah</label>
                    <select class="form-control" id="filter-wilayah">
                        <option value="">Semua Wilayah</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label>Jenjang Pendidikan</label>
                    <select class="form-control" id="filter-jenjang">
                        <option value="">Semua Jenjang</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label>Status Sekolah</label>
                    <select class="form-control" id="filter-status">
                        <option value="">Semua Status</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <button class="btn btn-primary btn-block" id="apply-filter">
                        <i class="fas fa-filter"></i> Terapkan Filter
                        <div class="spinner-border spinner-border-sm d-none" role="status" id="filter-spinner"></div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>