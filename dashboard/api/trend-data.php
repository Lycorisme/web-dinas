<?php
// dashboard/api/trend-data.php
require_once '../../helper/connection.php';
header('Content-Type: application/json');

// Get trend data from scraping logs (last 30 days)
$sql = "SELECT 
            DATE(started_at) as tanggal,
            COUNT(*) as total_batch,
            SUM(success_count) as total_success,
            SUM(failed_count) as total_failed
        FROM scraping_logs 
        WHERE started_at >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
        GROUP BY DATE(started_at)
        ORDER BY tanggal";

$result = mysqli_query($connection, $sql);
$trend_data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $trend_data[] = [
        'date' => $row['tanggal'],
        'batch' => intval($row['total_batch']),
        'success' => intval($row['total_success']),
        'failed' => intval($row['total_failed'])
    ];
}

echo json_encode($trend_data);
?>