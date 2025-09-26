<?php
require_once '../helper/connection.php';

$username = mysqli_real_escape_string($connection, $_POST['username']);
$password = $_POST['password']; // Password disimpan sebagai plain text
$nama_pengguna = mysqli_real_escape_string($connection, $_POST['nama_pengguna']);

// Validasi password minimal 6 karakter
if (strlen($password) < 6) {
    header("Location: tambah.php?error=1");
    exit;
}

// Cek apakah username sudah ada
$check_query = "SELECT username FROM login WHERE username = '$username'";
$check_result = mysqli_query($connection, $check_query);

if (mysqli_num_rows($check_result) > 0) {
    header("Location: tambah.php?error=2");
    exit;
}

// Insert password tanpa hashing
$query = "INSERT INTO login (username, password, nama_pengguna) VALUES ('$username', '$password', '$nama_pengguna')";

if (mysqli_query($connection, $query)) {
    $_SESSION['info'] = [
        'status' => 'success',
        'message' => 'User berhasil ditambahkan!'
    ];
    header("Location: index.php");
} else {
    header("Location: tambah.php?error=1");
}
?>