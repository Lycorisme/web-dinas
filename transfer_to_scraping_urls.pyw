import sys
import logging
import argparse
import subprocess
import json
import os

# Setup logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

def get_sekolah_scrape_data(url_induk_id=None):
    """Get sekolah scrape data from database via PHP"""
    try:
        # BARU: Dapatkan path absolut ke helper/connection.php
        connection_path = os.path.abspath(os.path.join(os.path.dirname(__file__), 'helper', 'connection.php'))
        
        if url_induk_id:
            cmd = ['php', '-r', f'''
            require_once "{connection_path}";
            if ($conn->connect_error) {{
                die("Connection failed: " . $conn->connect_error);
            }}
            $query = "SELECT s.id, s.npsn, s.nama_sekolah, s.url, s.jenjang, k.url_induk_id 
                      FROM sekolah_scrape s 
                      JOIN kecamatan_scrape kc ON s.kecamatan_scrape_id = kc.id 
                      JOIN kabupaten_scrape k ON kc.kabupaten_scrape_id = k.id 
                      WHERE k.url_induk_id = ? AND s.status = 'active'";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", {url_induk_id});
            $stmt->execute();
            $result = $stmt->get_result();
            $sekolah = [];
            while ($row = $result->fetch_assoc()) {{
                $sekolah[] = $row;
            }}
            echo json_encode($sekolah);
            $conn->close();
            ''']
        else:
            cmd = ['php', '-r', f'''
            require_once "{connection_path}";
            if ($conn->connect_error) {{
                die("Connection failed: " . $conn->connect_error);
            }}
            $query = "SELECT s.id, s.npsn, s.nama_sekolah, s.url, s.jenjang, k.url_induk_id 
                      FROM sekolah_scrape s 
                      JOIN kecamatan_scrape kc ON s.kecamatan_scrape_id = kc.id 
                      JOIN kabupaten_scrape k ON kc.kabupaten_scrape_id = k.id 
                      WHERE s.status = 'active'";
            $result = $conn->query($query);
            $sekolah = [];
            while ($row = $result->fetch_assoc()) {{
                $sekolah[] = $row;
            }}
            echo json_encode($sekolah);
            $conn->close();
            ''']
        
        result = subprocess.run(cmd, capture_output=True, text=True, check=True)
        sekolah_list = json.loads(result.stdout)
        logger.info(f"Found {len(sekolah_list)} sekolah records to transfer")
        return sekolah_list
        
    except Exception as e:
        logger.error(f"Error getting sekolah scrape data: {e}")
        return []

def log_import_process(process_type, url_induk_id, status='running', total_processed=0, total_success=0, total_failed=0, error_message=None):
    """Log import process to database via PHP"""
    try:
        # BARU: Dapatkan path absolut ke helper/connection.php
        connection_path = os.path.abspath(os.path.join(os.path.dirname(__file__), 'helper', 'connection.php'))
        
        if status == 'running':
            cmd = ['php', '-r', f'''
            require_once "{connection_path}";
            if ($conn->connect_error) {{
                die("Connection failed: " . $conn->connect_error);
            }}
            $stmt = $conn->prepare("INSERT INTO import_log (process_type, url_induk_id, status) VALUES (?, ?, ?)");
            $stmt->bind_param("sis", "{process_type}", {url_induk_id if url_induk_id else 'NULL'}, "{status}");
            $stmt->execute();
            echo $conn->insert_id;
            $conn->close();
            ''']
        else:
            error_msg = error_message.replace('"', '\\"') if error_message else 'NULL'
            cmd = ['php', '-r', f'''
            require_once "{connection_path}";
            if ($conn->connect_error) {{
                die("Connection failed: " . $conn->connect_error);
            }}
            $stmt = $conn->prepare("UPDATE import_log SET total_processed = ?, total_success = ?, total_failed = ?, status = ?, completed_at = NOW(), error_message = ? WHERE process_type = ? AND url_induk_id = ? AND status = 'running' ORDER BY id DESC LIMIT 1");
            $stmt->bind_param("iiisssi", {total_processed}, {total_success}, {total_failed}, "{status}", {"\"" + error_msg + "\"" if error_message else 'NULL'}, "{process_type}", {url_induk_id if url_induk_id else 'NULL'});
            $stmt->execute();
            $conn->close();
            ''']
        
        result = subprocess.run(cmd, capture_output=True, text=True, check=True)
        if status == 'running':
            return int(result.stdout.strip())
        return True
        
    except Exception as e:
        logger.error(f"Error logging import process: {e}")
        return None

def transfer_to_scraping_urls(sekolah_data_list):
    """Transfer sekolah data to scraping_urls table via PHP"""
    if not sekolah_data_list:
        return 0, 0
    
    try:
        # Prepare data for PHP
        data_json = json.dumps(sekolah_data_list).replace('"', '\\"')
        
        # BARU: Dapatkan path absolut ke helper/connection.php
        connection_path = os.path.abspath(os.path.join(os.path.dirname(__file__), 'helper', 'connection.php'))
        
        cmd = ['php', '-r', f'''
        require_once "{connection_path}";
        if ($conn->connect_error) {{
            die("Connection failed: " . $conn->connect_error);
        }}
        
        $data = json_decode("{data_json}", true);
        $inserted = 0;
        $updated = 0;
        
        foreach ($data as $item) {{
            $sekolah_scrape_id = $item["id"];
            $url = $item["url"];
            $nama_sekolah = $item["nama_sekolah"];
            
            // Check if URL already exists in scraping_urls
            $check_stmt = $conn->prepare("SELECT id, sekolah_scrape_id FROM scraping_urls WHERE url = ?");
            $check_stmt->bind_param("s", $url);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {{
                // URL exists, update sekolah_scrape_id and description if needed
                $existing = $check_result->fetch_assoc();
                if ($existing["sekolah_scrape_id"] != $sekolah_scrape_id) {{
                    $update_stmt = $conn->prepare("UPDATE scraping_urls SET sekolah_scrape_id = ?, description = ?, updated_at = NOW() WHERE url = ?");
                    $update_stmt->bind_param("iss", $sekolah_scrape_id, $nama_sekolah, $url);
                    if ($update_stmt->execute() && $update_stmt->affected_rows > 0) {{
                        $updated++;
                    }}
                }}
            }} else {{
                // URL doesn't exist, insert new record
                $insert_stmt = $conn->prepare("INSERT INTO scraping_urls (sekolah_scrape_id, url, description, status, created_at, updated_at) VALUES (?, ?, ?, 'active', NOW(), NOW())");
                $insert_stmt->bind_param("iss", $sekolah_scrape_id, $url, $nama_sekolah);
                if ($insert_stmt->execute()) {{
                    $inserted++;
                }}
            }}
        }}
        
        echo json_encode(["inserted" => $inserted, "updated" => $updated]);
        $conn->close();
        ''']
        
        result = subprocess.run(cmd, capture_output=True, text=True, check=True)
        result_data = json.loads(result.stdout.strip())
        
        inserted_count = result_data.get('inserted', 0)
        updated_count = result_data.get('updated', 0)
        
        logger.info(f"Transfer completed: {inserted_count} inserted, {updated_count} updated")
        return inserted_count, updated_count
        
    except Exception as e:
        logger.error(f"Error transferring to scraping_urls: {e}")
        return 0, 0

def mark_sekolah_as_processed(sekolah_ids):
    """Mark sekolah as processed in sekolah_scrape table"""
    if not sekolah_ids:
        return True
    
    try:
        ids_str = ','.join(map(str, sekolah_ids))
        
        # BARU: Dapatkan path absolut ke helper/connection.php
        connection_path = os.path.abspath(os.path.join(os.path.dirname(__file__), 'helper', 'connection.php'))
        
        cmd = ['php', '-r', f'''
        require_once "{connection_path}";
        if ($conn->connect_error) {{
            die("Connection failed: " . $conn->connect_error);
        }}
        
        $ids = [{ids_str}];
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $query = "UPDATE sekolah_scrape SET status = 'active', updated_at = NOW() WHERE id IN ($placeholders)";
        $stmt = $conn->prepare($query);
        $types = str_repeat('i', count($ids));
        $stmt->bind_param($types, ...array_values($ids));
        
        $success = $stmt->execute();
        echo $success ? "1" : "0";
        $conn->close();
        ''']
        
        result = subprocess.run(cmd, capture_output=True, text=True, check=True)
        success = result.stdout.strip() == "1"
        
        if success:
            logger.info(f"Marked {len(sekolah_ids)} sekolah records as active")
        else:
            logger.error("Failed to mark sekolah records as processed")
        
        return success
        
    except Exception as e:
        logger.error(f"Error marking sekolah as processed: {e}")
        return False

def process_transfer_for_url_induk(url_induk_id, sekolah_group):
    """Process transfer for single URL induk group"""
    logger.info(f"Processing transfer for URL Induk ID: {url_induk_id}")
    logger.info(f"Sekolah records to transfer: {len(sekolah_group)}")
    
    # Log process start
    log_id = log_import_process('transfer', url_induk_id, 'running')
    
    try:
        # Transfer data to scraping_urls
        inserted_count, updated_count = transfer_to_scraping_urls(sekolah_group)
        total_transferred = inserted_count + updated_count
        
        if total_transferred > 0:
            # Mark sekolah as processed
            sekolah_ids = [item['id'] for item in sekolah_group]
            if mark_sekolah_as_processed(sekolah_ids):
                # Log success
                log_import_process('transfer', url_induk_id, 'completed',
                                  total_processed=len(sekolah_group),
                                  total_success=total_transferred,
                                  total_failed=len(sekolah_group) - total_transferred)
                
                logger.info(f"✅ Successfully transferred {total_transferred} records for URL Induk ID {url_induk_id}")
                logger.info(f"   - Inserted: {inserted_count}")
                logger.info(f"   - Updated: {updated_count}")
                return True, total_transferred
            else:
                error_msg = "Failed to mark sekolah as processed"
                log_import_process('transfer', url_induk_id, 'failed',
                                  total_processed=len(sekolah_group),
                                  total_success=0,
                                  total_failed=len(sekolah_group),
                                  error_message=error_msg)
                logger.error(f"❌ {error_msg} for URL Induk ID {url_induk_id}")
                return False, 0
        else:
            error_msg = "No records were transferred"
            log_import_process('transfer', url_induk_id, 'failed',
                              total_processed=len(sekolah_group),
                              total_success=0,
                              total_failed=len(sekolah_group),
                              error_message=error_msg)
            logger.warning(f"⚠️ {error_msg} for URL Induk ID {url_induk_id}")
            return False, 0
            
    except Exception as e:
        error_msg = str(e)
        log_import_process('transfer', url_induk_id, 'failed',
                          total_processed=len(sekolah_group),
                          total_success=0,
                          total_failed=len(sekolah_group),
                          error_message=error_msg)
        logger.error(f"❌ Transfer failed for URL Induk ID {url_induk_id}: {error_msg}")
        return False, 0

def main():
    parser = argparse.ArgumentParser(description='Transfer Sekolah data to scraping_urls')
    parser.add_argument('--url_induk_id', type=int, help='Specific URL Induk ID to process')
    
    args = parser.parse_args()
    
    logger.info("=== STARTING TRANSFER TO SCRAPING_URLS ===")
    logger.info(f"URL Induk ID: {args.url_induk_id}")
    
    try:
        # Get sekolah scrape data
        sekolah_list = get_sekolah_scrape_data(args.url_induk_id)
        
        if not sekolah_list:
            logger.warning("No sekolah records found for transfer")
            sys.exit(0)
        
        # Group sekolah by url_induk_id
        url_induk_groups = {}
        for sekolah in sekolah_list:
            url_induk_id = sekolah['url_induk_id']
            if url_induk_id not in url_induk_groups:
                url_induk_groups[url_induk_id] = []
            url_induk_groups[url_induk_id].append(sekolah)
        
        total_success_count = 0
        total_failed_count = 0
        total_transferred = 0
        
        for url_induk_id, sekolah_group in url_induk_groups.items():
            success, transferred_count = process_transfer_for_url_induk(url_induk_id, sekolah_group)
            
            if success:
                total_success_count += 1
                total_transferred += transferred_count
            else:
                total_failed_count += 1
        
        logger.info(f"\n=== TRANSFER SUMMARY ===")
        logger.info(f"Total URL Induk Groups: {len(url_induk_groups)}")
        logger.info(f"Successful Groups: {total_success_count}")
        logger.info(f"Failed Groups: {total_failed_count}")
        logger.info(f"Total Records Transferred: {total_transferred}")
        
        if total_success_count > 0:
            logger.info("🎉 Transfer completed with some success!")
            sys.exit(0)
        else:
            logger.error("❌ All transfers failed")
            sys.exit(1)
            
    except Exception as e:
        logger.error(f"Fatal error in transfer process: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()