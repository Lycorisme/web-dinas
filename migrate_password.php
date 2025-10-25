<?php
require_once 'helper/connection.php';

echo "<h1>Migrasi Password ke Hash</h1>";

// Ambil semua user yang passwordnya belum di-hash
 $query = "SELECT * FROM login WHERE password NOT LIKE '$2y$%'";
 $result = mysqli_query($connection, $query);

 $total_users = mysqli_num_rows($result);
 $success_count = 0;
 $error_count = 0;

echo "<p>Ditemukan $total_users user dengan password belum di-hash</p>";

if ($total_users > 0) {
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>ID</th><th>Username</th><th>Status</th></tr>";
    
    while ($user = mysqli_fetch_assoc($result)) {
        $id = $user['id'];
        $username = $user['username'];
        $plain_password = $user['password'];
        
        // Hash password
        $hashed_password = password_hash($plain_password, PASSWORD_BCRYPT);
        
        // Update password
        $update_query = "UPDATE login SET password = '$hashed_password' WHERE id = $id";
        
        if (mysqli_query($connection, $update_query)) {
            echo "<tr><td>$id</td><td>$username</td><td style='color: green;'>Berhasil di-hash</td></tr>";
            $success_count++;
        } else {
            echo "<tr><td>$id</td><td>$username</td><td style='color: red;'>Gagal: " . mysqli_error($connection) . "</td></tr>";
            $error_count++;
        }
    }
    
    echo "</table>";
    
    echo "<h3>Hasil Migrasi:</h3>";
    echo "<p>• Berhasil: $success_count user</p>";
    echo "<p>• Gagal: $error_count user</p>";
    
    if ($error_count == 0) {
        echo "<p style='color: green; font-weight: bold;'>Semua password berhasil dimigrasi ke hash!</p>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>Ada $error_count password yang gagal dimigrasi. Periksa error di atas.</p>";
    }
} else {
    echo "<p style='color: green; font-weight: bold;'>Semua password sudah dalam bentuk hash!</p>";
}

echo "<p><a href='user/index.php'>Kembali ke Manajemen User</a></p>";
?>