<?php
// Mengatur zona waktu default
date_default_timezone_set("Asia/Jakarta");

// Parsing file config.ini
$config = parse_ini_file('config.ini', true);

// Mengambil detail koneksi dari array hasil parsing
$host = $config['database']['host'];
$user = $config['database']['username'];
$pass = $config['database']['password'];
$db = $config['database']['database'];

// Membuat koneksi ke database
$connection = mysqli_connect($host, $user, $pass, $db);

// Memeriksa koneksi
if (mysqli_connect_errno()) {
  // Jika koneksi gagal, hentikan skrip dan tampilkan pesan error
  die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set karakter set untuk koneksi
mysqli_set_charset($connection, "utf8");
?>