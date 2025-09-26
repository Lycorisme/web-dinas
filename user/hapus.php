<?php
require_once '../helper/connection.php';

$id = $_GET['id'];

// Cegah hapus user pertama (admin default)
if ($id == 1) {
    $_SESSION['info'] = [
        'status' => 'error',
        'message' => 'User default tidak dapat dihapus!'
    ];
    header("Location: index.php");
    exit;
}

$query = "DELETE FROM login WHERE id = $id";

if (mysqli_query($connection, $query)) {
    $_SESSION['info'] = [
        'status' => 'success',
        'message' => 'User berhasil dihapus!'
    ];
    header("Location: index.php");
} else {
    $_SESSION['info'] = [
        'status' => 'error',
        'message' => 'Terjadi kesalahan. Silakan coba lagi!'
    ];
    header("Location: index.php");
}
?>