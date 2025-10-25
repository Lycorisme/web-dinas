<?php
session_start();
require_once '../helper/connection.php';

 $username = mysqli_real_escape_string($connection, $_POST['username']);
 $password = $_POST['password'];
 $nama_pengguna = mysqli_real_escape_string($connection, $_POST['nama_pengguna']);

// Validasi password minimal 6 karakter
if (strlen($password) < 6) {
    $_SESSION['info'] = [
        'status' => 'error',
        'message' => 'Password minimal 6 karakter!'
    ];
    header("Location: tambah.php?error=1");
    exit;
}

// Cek apakah username sudah ada
 $check_query = "SELECT username FROM login WHERE username = '$username'";
 $check_result = mysqli_query($connection, $check_query);

if (mysqli_num_rows($check_result) > 0) {
    $_SESSION['info'] = [
        'status' => 'error',
        'message' => 'Username sudah digunakan!'
    ];
    header("Location: tambah.php?error=2");
    exit;
}

// Hash password menggunakan bcrypt
 $hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Insert dengan password yang sudah di-hash
 $query = "INSERT INTO login (username, password, nama_pengguna) VALUES ('$username', '$hashed_password', '$nama_pengguna')";

if (mysqli_query($connection, $query)) {
    $_SESSION['info'] = [
        'status' => 'success',
        'message' => 'User berhasil ditambahkan!'
    ];
    header("Location: index.php");
    exit;
} else {
    $_SESSION['info'] = [
        'status' => 'error',
        'message' => 'Terjadi kesalahan. Silakan coba lagi!'
    ];
    header("Location: tambah.php?error=1");
    exit;
}
?>  