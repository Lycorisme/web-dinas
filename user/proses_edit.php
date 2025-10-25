<?php
session_start();
require_once '../helper/connection.php';

 $id = $_POST['id'];
 $username = mysqli_real_escape_string($connection, $_POST['username']);
 $password = $_POST['password'];
 $nama_pengguna = mysqli_real_escape_string($connection, $_POST['nama_pengguna']);

// Cek apakah user yang sedang login bukan ID 1 dan mencoba mengedit user ID 1
if ($_SESSION['login']['id'] != 1 && $id == 1) {
    $_SESSION['info'] = [
        'status' => 'error',
        'message' => 'Anda tidak memiliki izin untuk mengedit user ini!'
    ];
    header("Location: index.php");
    exit;
}

// Cek apakah username sudah ada (kecuali user ini sendiri)
 $check_query = "SELECT username FROM login WHERE username = '$username' AND id != $id";
 $check_result = mysqli_query($connection, $check_query);

if (mysqli_num_rows($check_result) > 0) {
    $_SESSION['info'] = [
        'status' => 'error',
        'message' => 'Username sudah digunakan!'
    ];
    header("Location: edit.php?id=$id&error=2");
    exit;
}

// Cek apakah password diubah
if (!empty($password)) {
    // Validasi password minimal 6 karakter
    if (strlen($password) < 6) {
        $_SESSION['info'] = [
            'status' => 'error',
            'message' => 'Password minimal 6 karakter!'
        ];
        header("Location: edit.php?id=$id&error=1");
        exit;
    }
    
    // Hash password baru
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    
    // Update dengan password baru yang sudah di-hash
    $query = "UPDATE login SET username = '$username', password = '$hashed_password', nama_pengguna = '$nama_pengguna' WHERE id = $id";
} else {
    // Update tanpa mengubah password
    $query = "UPDATE login SET username = '$username', nama_pengguna = '$nama_pengguna' WHERE id = $id";
}

if (mysqli_query($connection, $query)) {
    $_SESSION['info'] = [
        'status' => 'success',
        'message' => 'User berhasil diperbarui!'
    ];
    header("Location: index.php");
    exit;
} else {
    $_SESSION['info'] = [
        'status' => 'error',
        'message' => 'Terjadi kesalahan. Silakan coba lagi!'
    ];
    header("Location: edit.php?id=$id&error=1");
    exit;
}
?>