<?php
// dashboard/widgets/map-widget.php
?>
<div class="card">
    <div class="card-header">
        <h4>Peta Sebaran Sekolah</h4>
        <div class="card-header-action">
            <div class="btn-group" role="group" aria-label="Map Controls">
                <button class="btn btn-sm btn-outline-secondary" id="theme-default" title="Default">
                    <i class="fas fa-map"></i>
                </button>
                <button class="btn btn-sm btn-outline-secondary" id="theme-dark" title="Dark Mode">
                    <i class="fas fa-moon"></i>
                </button>
                <button class="btn btn-sm btn-outline-secondary" id="theme-satellite" title="Satellite">
                    <i class="fas fa-satellite"></i>
                </button>
                <button class="btn btn-sm btn-outline-primary" id="refresh-map">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div id="school-map" style="height: 500px; width: 100%; border-radius: 8px; position: relative;"></div>
        <div class="mt-3">
            <div class="row">
                <div class="col-md-8">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> 
                        Klik marker untuk melihat detail sekolah.
                    </small>
                </div>
                <div class="col-md-4 text-right">
                    <div class="legend">
                        <small class="text-muted">
                            <i class="fas fa-circle text-success"></i> SD &nbsp;
                            <i class="fas fa-circle text-warning"></i> SMP &nbsp;
                            <i class="fas fa-circle text-primary"></i> SMA &nbsp;
                            <i class="fas fa-circle text-purple"></i> SMK
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>