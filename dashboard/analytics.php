<?php
// dashboard/analytics.php
require_once '../layout/_top.php';
require_once '../helper/connection.php';
?>

<link rel="stylesheet" href="../assets/vendor/leaflet/css/leaflet.css" />
<link rel="stylesheet" href="assets/css/analytics.css">

<section class="section">
  <div class="section-header">
    <h1>Dashboard Analytics</h1>
    <div class="section-header-breadcrumb">
    </div>
  </div>

  <div class="row mb-2">
    <div class="col-12">
      <?php include 'widgets/filter-widget.php'; ?>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <?php include 'widgets/stats-cards.php'; ?>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-6 col-md-12">
      <div class="card">
        <div class="card-header">
          <h4>Data PTK (Pendidik & Tenaga Kependidikan)</h4>
        </div>
        <div class="card-body chart-container">
          <canvas id="ptkChart"></canvas>
        </div>
      </div>
    </div>
    <div class="col-lg-6 col-md-12">
      <div class="card">
        <div class="card-header">
          <h4>Data Peserta Didik per Jenjang</h4>
        </div>
        <div class="card-body chart-container">
          <canvas id="pdChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <?php include 'widgets/map-widget.php'; ?>
    </div>
  </div>
</section>

<script src="../assets/vendor/chart.js/chart.min.js"></script>
<script src="../assets/vendor/leaflet/js/leaflet.js"></script>
<script src="assets/js/analytics-main.js"></script>
<script src="assets/js/charts/ptk-chart.js"></script>
<script src="assets/js/charts/pd-chart.js"></script>
<script src="assets/js/maps/school-map.js"></script>
<script src="assets/js/filters/dynamic-filter.js"></script>

<?php require_once '../layout/_bottom.php'; ?>