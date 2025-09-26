<?php
require_once '../helper/connection.php';

$id = $_POST['id'];
$username = mysqli_real_escape_string($connection, $_POST['username']);
$password = $_POST['password']; // Password disimpan sebagai plain text
$nama_pengguna = mysqli_real_escape_string($connection, $_POST['nama_pengguna']);

// Cek apakah username sudah ada (kecuali user ini sendiri)
$check_query = "SELECT username FROM login WHERE username = '$username' AND id != $id";
$check_result = mysqli_query($connection, $check_query);

if (mysqli_num_rows($check_result) > 0) {
    header("Location: edit.php?id=$id&error=2");
    exit;
}

// Cek apakah password diubah
if (!empty($password)) {
    // Validasi password minimal 6 karakter
    if (strlen($password) < 6) {
        header("Location: edit.php?id=$id&error=1");
        exit;
    }
    
    // Update password tanpa hashing
    $query = "UPDATE login SET username = '$username', password = '$password', nama_pengguna = '$nama_pengguna' WHERE id = $id";
} else {
    $query = "UPDATE login SET username = '$username', nama_pengguna = '$nama_pengguna' WHERE id = $id";
}

if (mysqli_query($connection, $query)) {
    $_SESSION['info'] = [
        'status' => 'success',
        'message' => 'User berhasil diperbarui!'
    ];
    header("Location: index.php");
} else {
    header("Location: edit.php?id=$id&error=1");
}
?>