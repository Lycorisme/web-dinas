<?php

/**
 * ===============================================================
 * IMPORT HANDLER.PHP – VERSI REFACTOR (OKTOBER 2025)
 * ===============================================================
 * - Logika dirombak total untuk stabilitas.
 * - Path ke chromedriver.exe dikirim EKSPLISIT ke Python.
 * - ✅ FIX: getProgress() dirombak untuk menangani log_id
 * dan menampilkan status progres & selesai dengan benar.
 * ===============================================================
 */

header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(30); // PHP script time limit (proses background akan jalan terus)
ini_set('max_execution_time', 30);

require_once '../helper/connection.php';

/* ===============================================================
    HELPER: PENCARI PATH
    =============================================================== */
function getPythonPath() {
    // 1. Prioritas Venv Lokal
    $venvPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
                'venv' . DIRECTORY_SEPARATOR . 'Scripts' . DIRECTORY_SEPARATOR . 'python.exe';
    
    if (file_exists($venvPath)) {
        return realpath($venvPath);
    }

    // 2. Fallback (jika venv tidak ada, walau seharusnya ada)
    return 'python';
}

function getChromeDriverPath() {
    // Path EKSPLISIT ke chromedriver
    $driverPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
                  'driver' . DIRECTORY_SEPARATOR . 'chromedriver.exe';
    
    if (file_exists($driverPath)) {
        return realpath($driverPath);
    }
    
    // Jika tidak ada, kembalikan string kosong atau path default
    // Tapi kita akan melempar error agar jelas
    return null;
}


/* ===============================================================
    EKSEKUSI COMMAND DI BACKGROUND (WINDOWS)
    =============================================================== */
function executeBackgroundCommand($pythonPath, $scriptPath, $args) {
    // Pengecekan file_exists() yang benar ada di triggerScraper().
    
    // Hanya untuk Windows
    if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
        throw new Exception("Metode eksekusi ini hanya untuk Windows.");
    }

    // Buat batch file sementara untuk menjalankan perintah
    $batchFile = sys_get_temp_dir() . '\\run_dapo_scraper_' . time() . '_' . rand(1000,9999) . '.bat';
    
    // Tentukan file log output untuk Python
    // Pastikan folder log ini bisa ditulis oleh server
    $projectRoot = realpath(__DIR__ . '/..');
    $logOutput = $projectRoot . '\\log_python_output.log';
    
    // Bangun argumen command line
    $cmdArgs = [];
    foreach ($args as $arg) {
        $cmdArgs[] = escapeshellarg($arg);
    }
    $argString = implode(' ', $cmdArgs);
    
    // Isi batch file
    // 1. Pindah ke direktori root proyek (PENTING!)
    // 2. Jalankan python dari venv dengan argumen
    // 3. Alihkan SEMUA output (stdout & stderr) ke file log
    $batchContent = "@echo off\n";
    $batchContent .= "cd /d \"$projectRoot\"\n";
    $batchContent .= "\"$pythonPath\" \"$scriptPath\" $argString >> \"$logOutput\" 2>&1\n";
    
    if (file_put_contents($batchFile, $batchContent) === false) {
        throw new Exception("Gagal membuat file batch sementara.");
    }

    // Jalankan batch file di background menggunakan 'start /B'
    // Ini adalah cara paling reliable di Windows untuk PHP
    $exec = "start /B cmd /C \"$batchFile\"";

    // Log perintah yang dijalankan untuk debug
    $logFile = $projectRoot . '\\log_php_exec.log';
    $logContent = date('[Y-m-d H:i:s] ') . "=== EXECUTING COMMAND ===" . PHP_EOL;
    $logContent .= "Batch File: $batchFile" . PHP_EOL;
    $logContent .= "Command: $exec" . PHP_EOL;
    $logContent .= "Batch Content:" . PHP_EOL . $batchContent . PHP_EOL;
    $logContent .= "================================" . PHP_EOL;
    file_put_contents($logFile, $logContent, FILE_APPEND);

    // Jalankan dan tutup koneksi
    pclose(popen($exec, "r"));
    
    // Beri waktu 1-2 detik agar file batch sempat dieksekusi
    sleep(2);
    return true;
}


/* ===============================================================
    MAIN HANDLER
    =============================================================== */
$input  = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

try {
    switch ($action) {
        case 'trigger_scraper':
            triggerScraper($connection, $input);
            break;

        case 'cancel_process':
            $urlIndukId = $input['url_induk_id'] ?? null;
            if ($urlIndukId) {
                $stmt = $connection->prepare(
                    "UPDATE import_log SET status='cancelled'
                     WHERE url_induk_id=? AND status='running'"
                );
                $stmt->bind_param("i", $urlIndukId);
                $stmt->execute();
                echo json_encode(['success'=>true,'message'=>'Proses berhasil dibatalkan.']);
            } else {
                echo json_encode(['success'=>false,'message'=>'URL Induk ID tidak valid.']);
            }
            break;

        case 'save_url_induk':
            saveUrlInduk($connection, $input);
            break;

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
            throw new Exception('Action tidak valid: ' . $action);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}

if (isset($connection) && $connection instanceof mysqli) {
    $connection->close();
}

/* ===============================================================
    FUNCTION: triggerScraper
    =============================================================== */
function triggerScraper($connection, $input) {
    // 1. Dapatkan Path Python Venv
    $python_path = getPythonPath();
    if (!$python_path || !file_exists($python_path)) {
        throw new Exception("Python venv tidak ditemukan di: " . $python_path);
    }

    // 2. Dapatkan Path ChromeDriver (BARU & WAJIB)
    $driver_path = getChromeDriverPath();
    if (!$driver_path) {
        throw new Exception(
            "FATAL: chromedriver.exe tidak ditemukan! " .
            "Harap letakkan di folder 'driver' di root proyek Anda."
        );
    }

    $scraper_type = $input['scraper_type'] ?? '';
    $url_induk_id = $input['url_induk_id'] ?? 0;
    $selected_ids = $input['selected_ids'] ?? [];
    $user_id       = $input['user_id'] ?? 1;
    $max_retries   = $input['max_retries'] ?? 300; 

    if (empty($scraper_type)) {
        throw new Exception('Parameter scraper_type tidak boleh kosong');
    }

    // Tentukan skrip Python yang akan dijalankan
    $map = [
        'kabupaten' => 'import_url_kabupaten.pyw',
        'kecamatan' => 'import_url_kecamatan.pyw',
        'sekolah'   => 'import_url_sekolah.pyw',
        'transfer'  => 'transfer_to_scraping_urls.pyw'
    ];
    if (!isset($map[$scraper_type])) {
        throw new Exception('Tipe scraper tidak valid: ' . $scraper_type);
    }

    $script_name = $map[$scraper_type];
    
    // --- PENGECEKAN YANG BENAR ADA DI SINI ---
    $script_path_absolute = __DIR__ . DIRECTORY_SEPARATOR . $script_name;
    if (!file_exists($script_path_absolute)) {
        throw new Exception("Script Python tidak ditemukan di path absolut: $script_path_absolute");
    }
    
    // Path ini UNTUK PERINTAH CMD (relatif dari root proyek)
    $script_path_for_cmd = 'sekolah' . DIRECTORY_SEPARATOR . $script_name;


    $args = [];
    $log_id = null;

    // Tambahkan argumen berdasarkan tipe scraper
    if ($scraper_type === 'sekolah') {
        if (empty($selected_ids)) throw new Exception('kecamatan_ids wajib untuk scraping sekolah');
        $args[] = "--kecamatan_id=" . implode(',', $selected_ids);
    } elseif ($scraper_type === 'kecamatan') {
        if (empty($selected_ids)) throw new Exception('kabupaten_ids wajib untuk scraping kecamatan');
        $args[] = "--kabupaten_id=" . implode(',', $selected_ids);
    } else {
        if (empty($url_induk_id)) throw new Exception('url_induk_id wajib diisi');
        $args[] = "--url_induk_id=" . intval($url_induk_id);
    }

    // Argumen WAJIB untuk semua scraper
    $args[] = "--user_id=" . intval($user_id);
    $args[] = "--max_retries=" . intval($max_retries);
    
    // Argumen KUNCI BARU: Kirim path driver ke Python
    $args[] = "--driver_path=" . $driver_path;
    
    // Khusus untuk 'transfer', kita tidak perlu log ID
    if ($scraper_type !== 'transfer') {
        // Tentukan ID url_induk untuk log 'kecamatan' dan 'sekolah'
        // Kita akan ambil dari ID kabupaten/kecamatan pertama
        if (in_array($scraper_type, ['sekolah', 'kecamatan']) && $url_induk_id == 0) {
            $temp_id = $selected_ids[0];
            if ($scraper_type === 'sekolah') {
                // Ambil url_induk_id dari kecamatan
                $stmt = $connection->prepare("SELECT kb.url_induk_id FROM kecamatan_scrape kc JOIN kabupaten_scrape kb ON kc.kabupaten_scrape_id = kb.id WHERE kc.id = ?");
                $stmt->bind_param("i", $temp_id);
            } else { // 'kecamatan'
                // Ambil url_induk_id dari kabupaten
                $stmt = $connection->prepare("SELECT url_induk_id FROM kabupaten_scrape WHERE id = ?");
                $stmt->bind_param("i", $temp_id);
            }
            $stmt->execute();
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) {
                $url_induk_id = $row['url_induk_id'];
            }
            $stmt->close();
        }
        
        // Buat log entry 'running' SEKARANG
        $stmt = $connection->prepare("
            INSERT INTO import_log(user_id, process_type, url_induk_id, status, started_at, created_at)
            VALUES(?, ?, ?, 'running', NOW(), NOW())
        ");
        $stmt->bind_param("isi", $user_id, $scraper_type, $url_induk_id);
        $stmt->execute();
        $log_id = $connection->insert_id;
        $stmt->close();
        
        // Kirim log_id ke skrip Python agar bisa di-update
        $args[] = "--log_id=" . $log_id;
    }

    // Kirim path RELATIF (script_path_for_cmd) ke fungsi execute
    $success = executeBackgroundCommand($python_path, $script_path_for_cmd, $args);
    if (!$success) throw new Exception("Gagal menjalankan proses background.");

    echo json_encode([
        'success'=>true,
        'message'=>"Scraper {$scraper_type} berhasil dijalankan.",
        'log_id'=>$log_id, // Kirim log_id kembali ke front-end
        'python_path'=>$python_path,
        'driver_path'=>$driver_path
    ]);
}

/* ===============================================================
    FUNCTION: saveUrlInduk
    (Tidak berubah)
    =============================================================== */
function saveUrlInduk($connection, $input) {
    $url = $input['url'] ?? '';
    if (empty($url)) throw new Exception('URL tidak boleh kosong');
    if (!filter_var($url, FILTER_VALIDATE_URL)) throw new Exception('Format URL tidak valid');

    $check = $connection->prepare("SELECT id FROM url_induk_scrape WHERE url = ?");
    $check->bind_param('s', $url);
    $check->execute();
    $res = $check->get_result();

    if ($exist = $res->fetch_assoc()) {
        echo json_encode([
            'success'=>true,
            'message'=>'URL sudah ada',
            'url_induk_id'=>$exist['id']
        ]);
        return;
    }

    $desc = 'URL Induk Dapodik - ' . date('Y-m-d H:i:s');
    $ins = $connection->prepare("
        INSERT INTO url_induk_scrape (url, description, status, created_at, updated_at)
        VALUES (?, ?, 'active', NOW(), NOW())");
    $ins->bind_param('ss', $url, $desc);

    if ($ins->execute()) {
        echo json_encode([
            'success'=>true,
            'message'=>'URL Induk berhasil disimpan',
            'url_induk_id'=>$connection->insert_id
        ]);
    } else {
        throw new Exception('Gagal menyimpan URL Induk');
    }
}

/* ===============================================================
    FUNCTION: getKecamatanForKabupaten
    (Tidak berubah)
    =============================================================== */
function getKecamatanForKabupaten($connection, $input) {
    $kabupaten_id = $input['kabupaten_id'] ?? null;
    if (empty($kabupaten_id)) throw new Exception('ID Kabupaten tidak boleh kosong');

    $stmt = $connection->prepare("
        SELECT id, nama_kecamatan
        FROM kecamatan_scrape
        WHERE kabupaten_scrape_id = ? AND status = 'active'
        ORDER BY nama_kecamatan");
    $stmt->bind_param('i', $kabupaten_id);
    $stmt->execute();
    $res = $stmt->get_result();

    $data = [];
    while ($row = $res->fetch_assoc()) $data[] = $row;

    echo json_encode(['success'=>true,'data'=>$data]);
}

/* ===============================================================
    FUNCTION: checkDataAvailability
    (Tidak berubah)
    =============================================================== */
function checkDataAvailability($connection, $input) {
    $data_type = $input['data_type'] ?? '';
    $url_induk_id = $input['url_induk_id'] ?? 0;
    $kabupaten_id = $input['kabupaten_id'] ?? null;
    $kecamatan_id = $input['kecamatan_id'] ?? null;

    if (empty($data_type) || empty($url_induk_id))
        throw new Exception('Parameter tidak lengkap');

    $params = [$url_induk_id];
    $types  = 'i';
    $data   = [];

    switch ($data_type) {
        case 'kabupaten':
            $query = "SELECT id, kode_kabupaten, nama_kabupaten, url
                        FROM kabupaten_scrape
                        WHERE url_induk_id = ? AND status = 'active'
                        ORDER BY nama_kabupaten";
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
            break;

        default:
            throw new Exception('Tipe data tidak valid');
    }

    $stmt = $connection->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) $data[] = $row;

    echo json_encode([
        'success'=>true,
        'has_data'=>count($data)>0,
        'data'=>$data,
        'count'=>count($data)
    ]);
}

/* ===============================================================
    FUNCTION: getProgress
    (LOGIKA DIPERBARUI UNTUK MENANGANI LOG_ID)
    =============================================================== */
function getProgress($connection, $input) {
    $process_type = $input['process_type'] ?? '';
    
    // PENTING: Untuk 'kecamatan' & 'sekolah', kita tidak pakai url_induk_id
    // kita pakai log_id yang dikirim dari front-end
    $log_id = $input['log_id'] ?? null;

    if (empty($process_type))
        throw new Exception('Parameter tidak lengkap');

    // ======= Logika Baru untuk multi-ID (kecamatan, sekolah)
    if (in_array($process_type, ['sekolah', 'kecamatan'])) {
        if (empty($log_id)) {
            // Jika tidak ada log_id (panggilan pertama sebelum trigger)
            echo json_encode(['success'=>true, 'progress'=>['percentage'=>0, 'status'=>"Menunggu proses dimulai...", 'completed'=>false, 'success'=>false, 'error'=>null]]);
            return;
        }

        // Ambil data log berdasarkan ID yang kita pantau
        $stmt = $connection->prepare("
            SELECT * FROM import_log
            WHERE id = ? AND process_type = ?
            LIMIT 1
        ");
        $stmt->bind_param('is', $log_id, $process_type);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $status = $row['status'];
            $total_processed = (int)$row['total_processed']; // total item (mis. 10 kab)
            $total_success = (int)$row['total_success'];     // item yg sukses (mis. 5 kab)
            $total_failed = (int)$row['total_failed'];       // item yg gagal (mis. 1 kab)
            $error_message = $row['error_message'];

            $percentage = 0;
            $completed = false;
            $success = false;

            switch ($status) {
                case 'running':
                    $status_message = "Proses {$process_type} berjalan... ({$total_success} sukses / {$total_processed} item)";
                    if ($total_processed > 0) {
                        $percentage = floor(($total_success + $total_failed) / $total_processed * 100);
                    } else {
                        $percentage = 50; // default progress jika 0 item
                    }
                    if ($percentage >= 100) $percentage = 99; // Jangan 100% jika masih running
                    $completed = false;
                    break;
                
                case 'completed':
                    $status_message = "Proses {$process_type} selesai! ({$total_success} sukses / {$total_processed} item)";
                    $percentage = 100;
                    $completed = true;
                    $success = true;
                    break;

                case 'failed':
                    $status_message = "Proses {$process_type} gagal. ({$total_success} sukses, {$total_failed} gagal)";
                    if (!empty($error_message)) {
                        $status_message = "Proses gagal: " . $error_message;
                    }
                    $percentage = 100;
                    $completed = true;
                    $success = false;
                    break;
                
                case 'cancelled':
                    $status_message = "Proses dibatalkan.";
                    $percentage = 100;
                    $completed = true;
                    break;

                default:
                    $status_message = "Menunggu proses dimulai...";
            }

            echo json_encode([
                'success'=>true,
                'progress'=>[
                    'percentage'=>$percentage,
                    'status'=>$status_message,
                    'completed'=>$completed,
                    'success'=>$success,
                    'error'=>$error_message,
                    'total_processed'=>$total_processed,
                    'total_success'=>$total_success,
                    'total_failed'=>$total_failed
                ]
            ]);

        } else {
            // Log tidak ditemukan
             echo json_encode(['success'=>false, 'message'=>'Log ID tidak ditemukan.']);
        }
        return;
    }

    // ======= Untuk proses tunggal (kabupaten, transfer)
    // (Logika ini tidak berubah, tapi url_induk_id wajib ada)
    $url_induk_id = $input['url_induk_id'] ?? 0;
    if ($url_induk_id == 0) throw new Exception('URL Induk ID wajib untuk tipe proses ini');
    
    $stmt = $connection->prepare("
        SELECT * FROM import_log
        WHERE process_type = ? AND url_induk_id = ?
        ORDER BY id DESC
        LIMIT 1
    ");
    $stmt->bind_param('si', $process_type, $url_induk_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $status          = $row['status'];
        $total_processed = (int)$row['total_processed'];
        $total_success   = (int)$row['total_success'];
        $total_failed    = (int)$row['total_failed'];
        $error_message   = $row['error_message'];

        $percentage = 0;
        $completed  = false;
        $success    = false;

        switch ($status) {
            case 'running':
                $status_message = "Proses sedang berjalan... (ditemukan: {$total_success})";
                $percentage = 50;
                $completed = false;
                break;
            case 'completed':
                $status_message = "Proses selesai! ({$total_success} berhasil, {$total_failed} gagal)";
                $percentage = 100;
                $completed = true;
                $success = true;
                break;
            case 'failed':
                $status_message = "Proses gagal: " . ($error_message ?: 'Unknown error');
                $percentage = 100;
                $completed = true;
                break;
            case 'cancelled':
                $status_message = "Proses dibatalkan.";
                $percentage = 100;
                $completed = true;
                break;
            default:
                $status_message = "Menunggu proses dimulai...";
        }

        echo json_encode([
            'success'=>true,
            'progress'=>[
                'percentage'=>$percentage,
                'status'=>$status_message,
                'completed'=>$completed,
                'success'=>$success,
                'error'=>$error_message,
                'total_processed'=>$total_processed,
                'total_success'=>$total_success,
                'total_failed'=>$total_failed
            ]
        ]);
    } else {
        echo json_encode([
            'success'=>true,
            'progress'=>[
                'percentage'=>0,
                'status'=>"Menunggu proses dimulai...",
                'completed'=>false,
                'success'=>false,
                'error'=>null
            ]
        ]);
    }
}


/* ===============================================================
    FUNCTION: importToScrapingUrls
    (Tidak berubah)
    =============================================================== */
function importToScrapingUrls($connection, $input) {
    $import_type = $input['import_type'] ?? '';
    $selected_ids = $input['selected_ids'] ?? [];
    $data_type = $input['data_type'] ?? '';
    $url_induk_id = $input['url_induk_id'] ?? 0;

    if (empty($import_type) || empty($data_type) || empty($url_induk_id))
        throw new Exception('Parameter tidak lengkap');
    if ($data_type !== 'sekolah')
        throw new Exception('Import ke scraping_urls hanya untuk data sekolah');

    $user_id = $input['user_id'] ?? 1;
    $where = "kb.url_induk_id = ? AND s.status = 'active'";
    $params = [$url_induk_id];
    $types = 'i';

    if ($import_type === 'selected' && !empty($selected_ids)) {
        $placeholders = implode(',', array_fill(0, count($selected_ids), '?'));
        $where .= " AND s.id IN ({$placeholders})";
        $params = array_merge($params, $selected_ids);
        $types .= str_repeat('i', count($selected_ids));
    }

    $query = "
        SELECT s.id, s.npsn, s.nama_sekolah, s.url, s.jenjang,
               kc.id AS kecamatan_scrape_id, kb.id AS kabupaten_scrape_id
        FROM sekolah_scrape s
        JOIN kecamatan_scrape kc ON s.kecamatan_scrape_id = kc.id
        JOIN kabupaten_scrape kb ON kc.kabupaten_scrape_id = kb.id
        WHERE {$where}
        ORDER BY s.nama_sekolah
    ";
    $stmt = $connection->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $sekolah_data = [];
    while ($row = $result->fetch_assoc()) $sekolah_data[] = $row;

    if (empty($sekolah_data))
        throw new Exception('Tidak ada data sekolah ditemukan');

    $inserted = 0; $updated = 0; $errors = 0;
    $connection->autocommit(false);

    try {
        foreach ($sekolah_data as $s) {
            $check = $connection->prepare("SELECT id, sekolah_scrape_id FROM scraping_urls WHERE url = ?");
            $check->bind_param('s', $s['url']);
            $check->execute();
            $res = $check->get_result();

            if ($exist = $res->fetch_assoc()) {
                if ($exist['sekolah_scrape_id'] != $s['id']) {
                    $upd = $connection->prepare("
                        UPDATE scraping_urls
                        SET sekolah_scrape_id = ?, description = ?, user_id = ?, updated_at = NOW()
                        WHERE url = ?");
                    $upd->bind_param('isis', $s['id'], $s['nama_sekolah'], $user_id, $s['url']);
                    if ($upd->execute() && $upd->affected_rows > 0)
                        $updated++;
                }
            } else {
                $ins = $connection->prepare("
                    INSERT INTO scraping_urls
                    (user_id, sekolah_scrape_id, kecamatan_scrape_id, kabupaten_scrape_id,
                     url, description, status, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, 'active', NOW(), NOW())
                ");
                $ins->bind_param('iiiiss', $user_id, $s['id'], $s['kecamatan_scrape_id'],
                    $s['kabupaten_scrape_id'], $s['url'], $s['nama_sekolah']);
                if ($ins->execute()) $inserted++; else $errors++;
            }
        }
        
        // Tandai sekolah_scrape sebagai 'imported'
        if (!empty($sekolah_data)) {
            $sekolah_ids = array_column($sekolah_data, 'id');
            if (!empty($sekolah_ids)) {
                 $placeholders = implode(',', array_fill(0, count($sekolah_ids), '?'));
                 $update_status_stmt = $connection->prepare(
                    "UPDATE sekolah_scrape SET status = 'processed', updated_at = NOW() WHERE id IN ({$placeholders})"
                 );
                 $types_update = str_repeat('i', count($sekolah_ids));
                 $update_status_stmt->bind_param($types_update, ...$sekolah_ids);
                 $update_status_stmt->execute();
            }
        }

        $connection->commit();

        $log = $connection->prepare("
            INSERT INTO import_log (user_id, process_type, url_induk_id, total_processed,
                                    total_success, total_failed, status, started_at, completed_at)
            VALUES (?, 'transfer', ?, ?, ?, ?, 'completed', NOW(), NOW())
        ");
        $total_processed = count($sekolah_data);
        $total_success = $inserted + $updated;
        $total_failed = $errors;
        $log->bind_param('iiiii', $user_id, $url_induk_id, $total_processed,
            $total_success, $total_failed);
        $log->execute();

        echo json_encode([
            'success'=>true,
            'message'=>"Import berhasil: {$inserted} ditambahkan, {$updated} diperbarui",
            'inserted'=>$inserted,
            'updated'=>$updated,
            'errors'=>$errors,
            'total'=>count($sekolah_data)
        ]);
    } catch (Exception $e) {
        $connection->rollback();
        throw new Exception('Gagal import: ' . $e->getMessage());
    } finally {
        $connection->autocommit(true);
    }
}

?>