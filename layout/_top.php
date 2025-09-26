<?php
require_once '../helper/auth.php';

isLogin();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>BTIKP &mdash; Dapodik</title>
  <!-- Favicon SVG (utama) -->
  <link rel="icon" type="image/svg+xml" href="../assets/img/favicon.svg">
  <!-- Fallback PNG untuk browser lama / tools yang tidak mendukung SVG -->
  <link rel="alternate icon" href="../assets/img/logo.png" type="image/png">

  <!-- General CSS Files -->
  <link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/vendor/font-awesome/css/all.min.css">
  
  <!-- CSS Libraries -->
  <link rel="stylesheet" href="../assets/modules/jqvmap/dist/jqvmap.min.css">
  <link rel="stylesheet" href="../assets/modules/summernote/summernote-bs4.css">
  <link rel="stylesheet" href="../assets/modules/owlcarousel2/dist/assets/owl.carousel.min.css">
  <link rel="stylesheet" href="../assets/modules/owlcarousel2/dist/assets/owl.theme.default.min.css">
  <link rel="stylesheet" href="../assets/modules/datatables/datatables.min.css">
  <link rel="stylesheet" href="../assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/vendor/izitoast/css/iziToast.min.css">

  <!-- Template CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/components.css">
  
  <!-- Custom CSS (Diakhir untuk override) -->
  <link rel="stylesheet" href="../assets/custome/css/sekolah-tambah-url.css">
  <link rel="stylesheet" href="../assets/custome/css/sekolah-index.css">
</head>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <?php
      require_once '_header.php';
      require_once '_sidenav.php';
      ?>
      <!-- Main Content -->
      <div class="main-content">