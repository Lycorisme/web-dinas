<?php
header('Content-Type: application/json');
require_once '../helper/connection.php';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

try {
    switch ($action) {
        case 'cancel_process':
            $json_data = file_get_contents('php://input');
            $request_data = json_decode($json_data, true);
            $urlIndukId = $request_data['url_induk_id'] ?? null;

            if ($urlIndukId) {
                $stmt = $connection->prepare("UPDATE import_log SET status = 'cancelled' WHERE url_induk_id = ? AND status = 'running'");
                $stmt->bind_param("i", $urlIndukId);
                $stmt->execute();
                echo json_encode(['success' => true, 'message' => 'Proses berhasil dibatalkan.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'URL Induk ID tidak valid saat pembatalan.']);
            }
            break;

        case 'save_url_induk':
            saveUrlInduk($connection, $input);
            break;
            
        case 'trigger_scraper':
            triggerScraper($connection, $input);
            break;
            
        // UBAH: Tambahkan case baru untuk cascading dropdown
        case 'get_kecamatan_for_kabupaten':
            getKecamatanForKabupaten($connection, $input);
            break;
            
        case 'check_data':
            checkDataAvailability($connection, $input);
            break;
            
        case 'get_progress':
            getProgress($connection, $input);
            break;
            
        case 'import_to_scraping_urls':
            importToScrapingUrls($connection, $input);
            break;
            
        default:
            throw new Exception('Action tidak valid');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function saveUrlInduk($connection, $input) {
    $url = $input['url'] ?? '';
    
    if (empty($url)) {
        throw new Exception('URL tidak boleh kosong');
    }
    
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        throw new Exception('Format URL tidak valid');
    }
    
    $check_query = "SELECT id FROM url_induk_scrape WHERE url = ?";
    $check_stmt = mysqli_prepare($connection, $check_query);
    mysqli_stmt_bind_param($check_stmt, 's', $url);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($check_result) > 0) {
        $existing = mysqli_fetch_assoc($check_result);
        echo json_encode([
            'success' => true,
            'message' => 'URL sudah ada, menggunakan data yang sudah ada',
            'url_induk_id' => $existing['id']
        ]);
        return;
    }
    
    $insert_query = "INSERT INTO url_induk_scrape (url, description, status, created_at, updated_at) VALUES (?, ?, 'active', NOW(), NOW())";
    $description = 'URL Induk Dapodik - ' . date('Y-m-d H:i:s');
    
    $insert_stmt = mysqli_prepare($connection, $insert_query);
    mysqli_stmt_bind_param($insert_stmt, 'ss', $url, $description);
    
    if (mysqli_stmt_execute($insert_stmt)) {
        $url_induk_id = mysqli_insert_id($connection);
        echo json_encode([
            'success' => true,
            'message' => 'URL Induk berhasil disimpan',
            'url_induk_id' => $url_induk_id
        ]);
    } else {
        throw new Exception('Gagal menyimpan URL Induk: ' . mysqli_error($connection));
    }
}

function triggerScraper($connection, $input) {
    $scraper_type = $input['scraper_type'] ?? '';
    $url_induk_id = $input['url_induk_id'] ?? 0;
    $selected_ids = $input['selected_ids'] ?? [];
    
    if (empty($scraper_type) || empty($url_induk_id)) {
        throw new Exception('Parameter tidak lengkap');
    }
    
    $script_map = [
        'kabupaten' => 'import_url_kabupaten.pyw',
        'kecamatan' => 'import_url_kecamatan.pyw',
        'sekolah' => 'import_url_sekolah.pyw',
        'transfer' => 'import_url_main.pyw'
    ];
    
    if (!isset($script_map[$scraper_type])) {
        throw new Exception('Tipe scraper tidak valid');
    }
    
    $script_path = __DIR__ . '/' . $script_map[$scraper_type];
    
    if (!file_exists($script_path)) {
        throw new Exception("Script Python tidak ditemukan: {$script_map[$scraper_type]}");
    }
    
    $command = "python \"{$script_path}\" --url_induk_id {$url_induk_id} --max_retries 300";
    
    if (!empty($selected_ids)) {
        $sanitized_ids = array_map('intval', $selected_ids);
        $ids_string = implode(',', $sanitized_ids);
        $command .= " --ids \"{$ids_string}\"";
    }

    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $full_command = "start /B {$command} 2>&1";
    } else {
        $full_command = "{$command} > /dev/null 2>&1 &";
    }
    
    error_log("Executing command: {$full_command}");
    shell_exec($full_command);
    
    echo json_encode([
        'success' => true,
        'message' => "Scraper {$scraper_type} berhasil dijalankan",
        'command' => $command
    ]);
}

// UBAH: Fungsi ini dimodifikasi untuk menerima filter kabupaten
function checkDataAvailability($connection, $input) {
    $data_type = $input['data_type'] ?? '';
    $url_induk_id = $input['url_induk_id'] ?? 0;
    $kabupaten_id = $input['kabupaten_id'] ?? null;
    $kecamatan_id = $input['kecamatan_id'] ?? null;
    
    if (empty($data_type) || empty($url_induk_id)) {
        throw new Exception('Parameter tidak lengkap');
    }
    
    $data = [];
    $has_data = false;
    
    $params = [$url_induk_id];
    $types = 'i';

    switch ($data_type) {
        case 'kabupaten':
            $query = "SELECT id, kode_kabupaten, nama_kabupaten, url FROM kabupaten_scrape 
                     WHERE url_induk_id = ? AND status = 'active' ORDER BY nama_kabupaten";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            break;
            
        case 'kecamatan':
            $query = "SELECT kc.id, kc.kode_kecamatan, kc.nama_kecamatan, kc.url 
                     FROM kecamatan_scrape kc
                     JOIN kabupaten_scrape kb ON kc.kabupaten_scrape_id = kb.id
                     WHERE kb.url_induk_id = ? AND kc.status = 'active'";
            if ($kabupaten_id) {
                $query .= " AND kb.id = ?";
                $params[] = $kabupaten_id;
                $types .= 'i';
            }
            $query .= " ORDER BY kc.nama_kecamatan";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            break;
            
        case 'sekolah':
            $query = "SELECT s.id, s.npsn, s.nama_sekolah, s.url, s.jenjang
                     FROM sekolah_scrape s
                     JOIN kecamatan_scrape kc ON s.kecamatan_scrape_id = kc.id
                     JOIN kabupaten_scrape kb ON kc.kabupaten_scrape_id = kb.id
                     WHERE kb.url_induk_id = ? AND s.status = 'active'";
            if ($kabupaten_id) {
                $query .= " AND kb.id = ?";
                $params[] = $kabupaten_id;
                $types .= 'i';
            }
            if ($kecamatan_id) {
                $query .= " AND kc.id = ?";
                $params[] = $kecamatan_id;
                $types .= 'i';
            }
            $query .= " ORDER BY s.nama_sekolah";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            break;
            
        default:
            throw new Exception('Tipe data tidak valid');
    }
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        $has_data = count($data) > 0;
    }
    
    echo json_encode([
        'success' => true,
        'has_data' => $has_data,
        'data' => $data,
        'count' => count($data)
    ]);
}

// UBAH: Tambahkan fungsi baru ini untuk cascading dropdown
function getKecamatanForKabupaten($connection, $input) {
    $kabupaten_id = $input['kabupaten_id'] ?? null;
    
    if (empty($kabupaten_id)) {
        throw new Exception('ID Kabupaten tidak boleh kosong');
    }

    $query = "SELECT id, nama_kecamatan FROM kecamatan_scrape WHERE kabupaten_scrape_id = ? AND status = 'active' ORDER BY nama_kecamatan ASC";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'i', $kabupaten_id);

    $data = [];
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }

    echo json_encode(['success' => true, 'data' => $data]);
}


function getProgress($connection, $input) {
    $process_type = $input['process_type'] ?? '';
    $url_induk_id = $input['url_induk_id'] ?? 0;
    
    if (empty($process_type) || empty($url_induk_id)) {
        throw new Exception('Parameter tidak lengkap');
    }
    
    $query = "SELECT * FROM import_log 
             WHERE process_type = ? AND url_induk_id = ? 
             ORDER BY started_at DESC LIMIT 1";
    
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'si', $process_type, $url_induk_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $status = $row['status'];
        $total_processed = (int)$row['total_processed'];
        $total_success = (int)$row['total_success'];
        $total_failed = (int)$row['total_failed'];
        $error_message = $row['error_message'];
        
        $percentage = 0;
        if ($status === 'completed' || $status === 'failed') {
            $percentage = 100;
        } else if ($status === 'running') {
            if ($total_processed > 0 && ($total_success + $total_failed) > 0) {
                 $percentage = (($total_success + $total_failed) / $total_processed) * 100;
            } else {
                 $percentage = 25;
            }
        }
        
        $status_message = '';
        $completed = false;
        $success = false;
        
        switch ($status) {
            case 'running':
                $status_message = "Sedang memproses {$process_type}... ({$total_success} dari {$total_processed} selesai)";
                if ($total_processed == 0) {
                    $status_message = "Sedang memulai proses {$process_type}...";
                }
                break;
            case 'completed':
                $status_message = "Proses {$process_type} selesai! ({$total_success} berhasil, {$total_failed} gagal)";
                $percentage = 100;
                $completed = true;
                $success = true;
                break;
            case 'failed':
                $status_message = "Proses {$process_type} gagal: " . ($error_message ?: 'Unknown error');
                $percentage = 100;
                $completed = true;
                $success = false;
                break;
            default:
                $status_message = "Status tidak dikenal";
        }
        
        echo json_encode([
            'success' => true,
            'progress' => [
                'percentage' => min(100, max(0, $percentage)),
                'status' => $status_message,
                'completed' => $completed,
                'success' => $success,
                'error' => $error_message,
                'total_processed' => $total_processed,
                'total_success' => $total_success,
                'total_failed' => $total_failed
            ]
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'progress' => [
                'percentage' => 0,
                'status' => "Menunggu proses {$process_type} dimulai...",
                'completed' => false,
                'success' => false,
                'error' => null,
                'total_processed' => 0,
                'total_success' => 0,
                'total_failed' => 0
            ]
        ]);
    }
}

function importToScrapingUrls($connection, $input) {
    $import_type = $input['import_type'] ?? '';
    $selected_ids = $input['selected_ids'] ?? [];
    $data_type = $input['data_type'] ?? '';
    $url_induk_id = $input['url_induk_id'] ?? 0;
    
    if (empty($import_type) || empty($data_type) || empty($url_induk_id)) {
        throw new Exception('Parameter tidak lengkap');
    }
    
    if ($data_type !== 'sekolah') {
        throw new Exception('Import ke scraping_urls hanya tersedia untuk data sekolah');
    }
    
    $where_clause = "kb.url_induk_id = ? AND s.status = 'active'";
    $params = [$url_induk_id];
    $param_types = 'i';
    
    if ($import_type === 'selected' && !empty($selected_ids)) {
        $placeholders = str_repeat('?,', count($selected_ids) - 1) . '?';
        $where_clause .= " AND s.id IN ({$placeholders})";
        $params = array_merge($params, $selected_ids);
        $param_types .= str_repeat('i', count($selected_ids));
    }
    
    $query = "SELECT s.id, s.npsn, s.nama_sekolah, s.url, s.jenjang
             FROM sekolah_scrape s
             JOIN kecamatan_scrape kc ON s.kecamatan_scrape_id = kc.id
             JOIN kabupaten_scrape kb ON kc.kabupaten_scrape_id = kb.id
             WHERE {$where_clause}
             ORDER BY s.nama_sekolah";
    
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, $param_types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $sekolah_data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $sekolah_data[] = $row;
    }
    
    if (empty($sekolah_data)) {
        throw new Exception('Tidak ada data sekolah yang ditemukan untuk diimport');
    }
    
    $inserted = 0;
    $updated = 0;
    $errors = 0;
    
    mysqli_autocommit($connection, false);
    
    try {
        foreach ($sekolah_data as $sekolah) {
            $check_query = "SELECT id, sekolah_scrape_id FROM scraping_urls WHERE url = ?";
            $check_stmt = mysqli_prepare($connection, $check_query);
            mysqli_stmt_bind_param($check_stmt, 's', $sekolah['url']);
            mysqli_stmt_execute($check_stmt);
            $check_result = mysqli_stmt_get_result($check_stmt);
            
            if ($existing = mysqli_fetch_assoc($check_result)) {
                if ($existing['sekolah_scrape_id'] != $sekolah['id']) {
                    $update_query = "UPDATE scraping_urls SET sekolah_scrape_id = ?, description = ?, updated_at = NOW() WHERE url = ?";
                    $update_stmt = mysqli_prepare($connection, $update_query);
                    mysqli_stmt_bind_param($update_stmt, 'iss', $sekolah['id'], $sekolah['nama_sekolah'], $sekolah['url']);
                    
                    if (mysqli_stmt_execute($update_stmt) && mysqli_stmt_affected_rows($update_stmt) > 0) {
                        $updated++;
                    }
                }
            } else {
                $insert_query = "INSERT INTO scraping_urls (sekolah_scrape_id, url, description, status, created_at, updated_at) 
                               VALUES (?, ?, ?, 'active', NOW(), NOW())";
                $insert_stmt = mysqli_prepare($connection, $insert_query);
                mysqli_stmt_bind_param($insert_stmt, 'iss', $sekolah['id'], $sekolah['url'], $sekolah['nama_sekolah']);
                
                if (mysqli_stmt_execute($insert_stmt)) {
                    $inserted++;
                } else {
                    $errors++;
                }
            }
        }
        
        if (!empty($sekolah_data)) {
            $sekolah_ids = array_column($sekolah_data, 'id');
            $placeholders = str_repeat('?,', count($sekolah_ids) - 1) . '?';
            $update_status_query = "UPDATE sekolah_scrape SET status = 'active', updated_at = NOW() WHERE id IN ({$placeholders})";
            $update_status_stmt = mysqli_prepare($connection, $update_status_query);
            $types = str_repeat('i', count($sekolah_ids));
            mysqli_stmt_bind_param($update_status_stmt, $types, ...$sekolah_ids);
            mysqli_stmt_execute($update_status_stmt);
        }
        
        mysqli_commit($connection);
        
        $log_query = "INSERT INTO import_log (process_type, url_induk_id, total_processed, total_success, total_failed, status, started_at, completed_at) 
                     VALUES ('transfer', ?, ?, ?, ?, 'completed', NOW(), NOW())";
        $log_stmt = mysqli_prepare($connection, $log_query);
        $total_processed = count($sekolah_data);
        $total_success = $inserted + $updated;
        $total_failed = $errors;
        mysqli_stmt_bind_param($log_stmt, 'iiii', $url_induk_id, $total_processed, $total_success, $total_failed);
        mysqli_stmt_execute($log_stmt);
        
        echo json_encode([
            'success' => true,
            'message' => "Import berhasil: {$inserted} ditambahkan, {$updated} diperbarui",
            'inserted' => $inserted,
            'updated' => $updated,
            'errors' => $errors,
            'total' => count($sekolah_data)
        ]);
        
    } catch (Exception $e) {
        mysqli_rollback($connection);
        throw new Exception('Gagal import ke scraping_urls: ' . $e->getMessage());
    } finally {
        mysqli_autocommit($connection, true);
    }
}
?>