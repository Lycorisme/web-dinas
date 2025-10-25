<?php
// FILE: test_debug.php - Simpan di folder /sekolah/
// Untuk debugging masalah koneksi dan query

echo "<h1>DEBUG TEST - Dapodik Database</h1>";

// Test 1: Cek path dan file
echo "<h2>1. Path dan File Check</h2>";
echo "Current file: " . __FILE__ . "<br>";
echo "Current directory: " . __DIR__ . "<br>";
echo "Working directory: " . getcwd() . "<br>";

$connection_path = '../helper/connection.php';
echo "Connection path: " . $connection_path . "<br>";
echo "Connection file exists: " . (file_exists($connection_path) ? 'YES' : 'NO') . "<br>";

// Test 2: Include connection
echo "<h2>2. Connection Test</h2>";
try {
    require_once $connection_path;
    echo "Connection file included: SUCCESS<br>";
    
    if (isset($connection) && $connection) {
        echo "Connection variable exists: YES<br>";
        echo "Connection type: " . gettype($connection) . "<br>";
        
        // Test connection
        if (mysqli_ping($connection)) {
            echo "Database connection: ACTIVE<br>";
            echo "MySQL version: " . mysqli_get_server_info($connection) . "<br>";
            
            // Test 3: Database dan Table
            echo "<h2>3. Database Test</h2>";
            $db_name = mysqli_get_server_info($connection);
            $current_db = mysqli_query($connection, "SELECT DATABASE()");
            $db_result = mysqli_fetch_row($current_db);
            echo "Current database: " . $db_result[0] . "<br>";
            
            // Test table exists
            $table_check = mysqli_query($connection, "SHOW TABLES LIKE 'scraping_urls'");
            echo "Table 'scraping_urls' exists: " . (mysqli_num_rows($table_check) > 0 ? 'YES' : 'NO') . "<br>";
            
            // Test 4: Data Test
            if (mysqli_num_rows($table_check) > 0) {
                echo "<h2>4. Data Test</h2>";
                
                // Total records
                $total_query = mysqli_query($connection, "SELECT COUNT(*) as total FROM scraping_urls");
                $total_result = mysqli_fetch_assoc($total_query);
                echo "Total records in scraping_urls: " . $total_result['total'] . "<br>";
                
                // Active records
                $active_query = mysqli_query($connection, "SELECT COUNT(*) as total FROM scraping_urls WHERE status = 'active'");
                $active_result = mysqli_fetch_assoc($active_query);
                echo "Active records: " . $active_result['total'] . "<br>";
                
                // Status breakdown
                $status_query = mysqli_query($connection, "SELECT status, COUNT(*) as count FROM scraping_urls GROUP BY status");
                echo "Status breakdown:<br>";
                while ($status_row = mysqli_fetch_assoc($status_query)) {
                    echo "- " . $status_row['status'] . ": " . $status_row['count'] . "<br>";
                }
                
                // Test 5: Actual Data
                echo "<h2>5. Sample Data</h2>";
                $sample_query = mysqli_query($connection, "SELECT id, url, description, status FROM scraping_urls ORDER BY id LIMIT 10");
                if (mysqli_num_rows($sample_query) > 0) {
                    echo "<table border='1' cellpadding='5'>";
                    echo "<tr><th>ID</th><th>URL</th><th>Description</th><th>Status</th></tr>";
                    while ($sample_row = mysqli_fetch_assoc($sample_query)) {
                        echo "<tr>";
                        echo "<td>" . $sample_row['id'] . "</td>";
                        echo "<td>" . substr($sample_row['url'], 0, 50) . "...</td>";
                        echo "<td>" . $sample_row['description'] . "</td>";
                        echo "<td>" . $sample_row['status'] . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "No data found in table<br>";
                }
            }
        } else {
            echo "Database connection: FAILED - " . mysqli_error($connection) . "<br>";
        }
    } else {
        echo "Connection variable: NOT SET<br>";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "<br>";
}

// Test 6: JSON Response Test
echo "<h2>6. JSON Response Test</h2>";
echo "<a href='get_active_urls.php' target='_blank'>Test get_active_urls.php (normal)</a><br>";
echo "<a href='get_active_urls.php?debug=1' target='_blank'>Test get_active_urls.php (debug mode)</a><br>";

// Test 7: Browser Console Test
echo "<h2>7. Browser Console Test</h2>";
echo "<button onclick='testAjax()'>Test AJAX Request</button>";
echo "<div id='ajax-result'></div>";

echo "<script>
function testAjax() {
    console.log('Testing AJAX request...');
    fetch('get_active_urls.php?debug=1')
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            return response.text();
        })
        .then(data => {
            console.log('Response data:', data);
            document.getElementById('ajax-result').innerHTML = '<pre>' + data + '</pre>';
        })
        .catch(error => {
            console.error('AJAX Error:', error);
            document.getElementById('ajax-result').innerHTML = '<pre style=\"color:red\">Error: ' + error + '</pre>';
        });
}
</script>";
?>