import os
import sys
import time
import logging
import argparse
import json
from bs4 import BeautifulSoup
import undetected_chromedriver as uc
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from urllib.parse import urljoin
from selenium.common.exceptions import TimeoutException, NoSuchElementException, WebDriverException
import mysql.connector
from mysql.connector import Error

# --- PERUBAHAN: Tambahkan path ke folder helper dan import koneksi ---
project_root = os.path.abspath(os.path.join(os.path.dirname(__file__), '..'))
sys.path.append(project_root)
from helper.db_connector import get_db_connection
# --- AKHIR PERUBAHAN ---

# Setup logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

# Configuration
BASE_URL = "https://dapo.kemendikdasmen.go.id"
PAGE_LOAD_TIMEOUT = 90
RETRY_DELAY_SECONDS = 15
KECAMATAN_RETRY_DELAY = 10  # Jeda antar retry untuk server down

def setup_driver():
    """Setup Chrome driver dengan opsi yang diperlukan"""
    options = uc.ChromeOptions()
    
    # Opsi dasar
    options.add_argument("--headless")
    options.add_argument("--no-sandbox")
    options.add_argument("--disable-dev-shm-usage")
    options.add_argument("--disable-gpu")
    options.add_argument("--window-size=1920,1080")
    
    # Opsi untuk menghindari deteksi
    options.add_argument("--disable-extensions")
    options.add_argument("--disable-infobars")
    options.add_argument("--disable-notifications")
    options.add_argument("--disable-popup-blocking")
    options.add_argument("--disable-blink-features=AutomationControlled")
    options.add_argument("--disable-web-security")
    options.add_argument("--allow-running-insecure-content")
    options.add_argument("--disable-features=VizDisplayCompositor")
    
    try:
        driver = uc.Chrome(options=options, version_main=None)
        driver.set_page_load_timeout(PAGE_LOAD_TIMEOUT)
        return driver
    except Exception as e:
        logger.error(f"Error setting up driver: {e}")
        return None

# MODIFIED FUNCTION
def get_kabupaten_data(url_induk_id=None, ids=None):
    """Get kabupaten data from database, bisa berdasarkan ID spesifik"""
    conn = get_db_connection()
    if not conn:
        return []
    
    try:
        cursor = conn.cursor(dictionary=True)
        query = "SELECT id, kode_kabupaten, nama_kabupaten, url, url_induk_id FROM kabupaten_scrape"
        params = []
        
        # MODIFIED: Logika untuk memfilter berdasarkan ID
        if ids:
            # Jika ada --ids, proses berdasarkan ID tersebut
            id_list = [int(id_str) for id_str in ids.split(',')]
            placeholders = ','.join(['%s'] * len(id_list))
            query += f" WHERE id IN ({placeholders}) AND status = 'active'"
            params.extend(id_list)
        elif url_induk_id:
            # Jika tidak ada --ids, gunakan logika lama (ambil semua dari url_induk_id)
            query += " WHERE url_induk_id = %s AND status = 'active'"
            params.append(url_induk_id)
        else:
            # Fallback jika tidak ada parameter sama sekali
            query += " WHERE status = 'active'"

        cursor.execute(query, tuple(params))
        kabupaten_list = cursor.fetchall()

        if ids:
            logger.info(f"Found {len(kabupaten_list)} selected kabupaten records to process")
        else:
            logger.info(f"Found {len(kabupaten_list)} kabupaten records to process")
        return kabupaten_list
        
    except Error as e:
        logger.error(f"Error getting kabupaten data: {e}")
        return []
    finally:
        if conn.is_connected():
            cursor.close()
            conn.close()

def log_import_process(process_type, url_induk_id, status='running', total_processed=0, total_success=0, total_failed=0, error_message=None):
    """Log import process to database"""
    conn = get_db_connection()
    if not conn:
        return None
    
    try:
        cursor = conn.cursor()
        
        if status == 'running':
            cursor.execute("""
                INSERT INTO import_log (process_type, url_induk_id, status, total_processed) 
                VALUES (%s, %s, %s, %s)
            """, (process_type, url_induk_id, status, total_processed))
            log_id = cursor.lastrowid
        else:
            cursor.execute("""
                UPDATE import_log 
                SET total_processed = %s, total_success = %s, total_failed = %s, 
                    status = %s, completed_at = NOW(), error_message = %s 
                WHERE process_type = %s AND url_induk_id = %s AND status = 'running' 
                ORDER BY id DESC LIMIT 1
            """, (total_processed, total_success, total_failed, status, error_message, process_type, url_induk_id))
            log_id = True
        
        conn.commit()
        return log_id
        
    except Error as e:
        logger.error(f"Error logging import process: {e}")
        conn.rollback()
        return None
    finally:
        if conn.is_connected():
            cursor.close()
            conn.close()

def save_kecamatan_to_db(kecamatan_data):
    """Save kecamatan data to database with verification"""
    if not kecamatan_data:
        return 0
    
    conn = get_db_connection()
    if not conn:
        return 0
    
    try:
        cursor = conn.cursor()
        
        # Cek data yang sudah ada
        existing_codes = set()
        for kode, _, _, kabupaten_scrape_id in kecamatan_data:
            cursor.execute("""
                SELECT kode_kecamatan FROM kecamatan_scrape 
                WHERE kode_kecamatan = %s AND kabupaten_scrape_id = %s
            """, (kode, kabupaten_scrape_id))
            if cursor.fetchone():
                existing_codes.add(kode)
        
        # Filter data baru
        new_data = [(kode, nama, url, kabupaten_scrape_id) for kode, nama, url, kabupaten_scrape_id in kecamatan_data if kode not in existing_codes]
        
        if not new_data:
            logger.info("All kecamatan data for this batch already exists in database")
            return 0
            
        # Insert data baru
        insert_query = """
            INSERT INTO kecamatan_scrape (kode_kecamatan, nama_kecamatan, url, kabupaten_scrape_id) 
            VALUES (%s, %s, %s, %s)
        """
        cursor.executemany(insert_query, new_data)
        conn.commit()
        
        # Verifikasi data tersimpan
        saved_count = 0
        for kode, _, _, kabupaten_scrape_id in new_data:
            cursor.execute("""
                SELECT 1 FROM kecamatan_scrape 
                WHERE kode_kecamatan = %s AND kabupaten_scrape_id = %s
            """, (kode, kabupaten_scrape_id))
            if cursor.fetchone():
                saved_count += 1
        
        if saved_count == len(new_data):
            logger.info(f"Successfully inserted {saved_count} new kecamatan records")
            return saved_count
        else:
            logger.error(f"Only {saved_count}/{len(new_data)} kecamatan records were saved")
            return saved_count
            
    except Error as e:
        logger.error(f"Error saving kecamatan data: {e}")
        conn.rollback()
        return 0
    finally:
        if conn.is_connected():
            cursor.close()
            conn.close()

def scrape_kecamatan(url, kabupaten_name):
    """Scraping data kecamatan dari URL kabupaten"""
    logger.info(f"Starting kecamatan scraping for {kabupaten_name} from: {url}")
    
    driver = None
    try:
        driver = setup_driver()
        if not driver:
            raise Exception("Failed to setup driver")
        
        driver.get(url)
        logger.info("Waiting for kabupaten page to load...")
        
        # Wait for table to appear
        wait = WebDriverWait(driver, 60)
        wait.until(EC.presence_of_element_located((By.ID, "DataTables_Table_0")))
        logger.info("Kecamatan table found.")
        
        # Wait for data to load
        wait.until(EC.visibility_of_element_located((By.CSS_SELECTOR, "#DataTables_Table_0 tbody tr.data")))
        logger.info("Kecamatan data loaded successfully.")
        
        time.sleep(3)  # Additional wait to ensure all data is loaded
        page_html = driver.page_source
        soup = BeautifulSoup(page_html, 'html.parser')
        
        table = soup.find('table', id='DataTables_Table_0')
        if not table:
            raise Exception(f"Kecamatan table not found for {kabupaten_name}.")
            
        tbody = table.find('tbody')
        if not tbody:
            raise Exception(f"Tbody not found in kecamatan table for {kabupaten_name}.")
            
        rows = tbody.find_all('tr', class_='data')
        if not rows:
            raise Exception(f"No kecamatan data rows found for {kabupaten_name}.")
            
        kecamatan_data = []
        for row in rows:
            cells = row.find_all('td')
            if len(cells) < 2:
                continue
                
            # Kolom pertama adalah kode kecamatan
            kode_cell = cells[0]
            kode = kode_cell.get_text(strip=True)
            
            # Kolom kedua berisi nama kecamatan dan link
            kecamatan_cell = cells[1]
            anchor_tag = kecamatan_cell.find('a')
            if anchor_tag:
                nama = anchor_tag.get_text(strip=True)
                href = anchor_tag.get('href', '').strip()
                
                # Build full URL
                full_url = urljoin(BASE_URL, href) if href else ''
                
                if kode and nama and full_url:
                    kecamatan_data.append((kode, nama, full_url))
        
        if not kecamatan_data:
            raise Exception(f"No kecamatan data extracted from {kabupaten_name}.")
            
        logger.info(f"Successfully extracted {len(kecamatan_data)} kecamatan records from {kabupaten_name}.")
        return kecamatan_data
        
    except Exception as e:
        logger.error(f"Error scraping kecamatan from {kabupaten_name}: {e}")
        raise
    finally:
        if driver:
            driver.quit()
            logger.info("Browser closed.")

def process_kabupaten(kabupaten_data, max_retries):
    """Process single kabupaten for kecamatan scraping"""
    kabupaten_id = kabupaten_data['id']
    kode_kabupaten = kabupaten_data['kode_kabupaten']
    nama_kabupaten = kabupaten_data['nama_kabupaten']
    url_kabupaten = kabupaten_data['url']
    
    logger.info(f"Processing Kabupaten: {nama_kabupaten} ({kode_kabupaten})")
    logger.info(f"URL: {url_kabupaten}")
    
    retry_count = 0
    success = False
    total_kecamatan = 0
    error_message = None
    
    while retry_count < max_retries and not success:
        try:
            if retry_count > 0:
                logger.info(f"Retry attempt {retry_count + 1}/{max_retries} for {nama_kabupaten}")
                time.sleep(KECAMATAN_RETRY_DELAY)
            
            # Scrape kecamatan data
            kecamatan_data = scrape_kecamatan(url_kabupaten, nama_kabupaten)
            
            # Tambahkan kabupaten_scrape_id ke setiap data kecamatan
            kecamatan_with_id = [(kode, nama, url, kabupaten_id) for kode, nama, url in kecamatan_data]
            
            # Save to database
            inserted_count = save_kecamatan_to_db(kecamatan_with_id)
            
            # We consider it a success even if 0 new records are inserted (duplicates)
            total_kecamatan = inserted_count
            success = True
            if inserted_count > 0:
                logger.info(f"Successfully processed {total_kecamatan} new kecamatan records from {nama_kabupaten}")
            else:
                logger.info(f"No new kecamatan records were inserted for {nama_kabupaten} (already exist).")

        except Exception as e:
            error_message = str(e)
            logger.error(f"Error processing kabupaten {nama_kabupaten}: {error_message}")
            retry_count += 1
    
    return success, total_kecamatan, error_message

def main():
    parser = argparse.ArgumentParser(description='Import Kecamatan from Dapodik')
    parser.add_argument('--url_induk_id', type=int, help='Specific URL Induk ID to process')
    # MODIFIED: Tambahkan argumen --ids
    parser.add_argument('--ids', type=str, help='Comma-separated list of specific kabupaten IDs to process')
    parser.add_argument('--max_retries', type=int, default=300, help='Maximum retry attempts')
    
    args = parser.parse_args()
    
    logger.info("=== STARTING KECAMATAN IMPORT ===")
    logger.info(f"URL Induk ID: {args.url_induk_id}")
    # MODIFIED: Log argumen ids jika ada
    logger.info(f"Specific Kabupaten IDs: {args.ids if args.ids else 'All'}")
    logger.info(f"Max Retries: {args.max_retries}")
    
    try:
        # MODIFIED: Kirim args.ids ke get_kabupaten_data
        kabupaten_list = get_kabupaten_data(args.url_induk_id, args.ids)
        
        if not kabupaten_list:
            logger.error("No kabupaten records found for kecamatan processing")
            sys.exit(1)
        
        url_induk_groups = {}
        for kab in kabupaten_list:
            url_induk_id = kab['url_induk_id']
            if url_induk_id not in url_induk_groups:
                url_induk_groups[url_induk_id] = []
            url_induk_groups[url_induk_id].append(kab)
        
        total_success = 0
        total_failed = 0
        
        for url_induk_id, kabupaten_group in url_induk_groups.items():
            logger.info(f"\nProcessing kabupaten group for URL Induk ID: {url_induk_id}")
            
            # Log process start
            total_to_process = len(kabupaten_group)
            log_id = log_import_process('kecamatan', url_induk_id, 'running', total_processed=total_to_process)
            
            group_success = 0
            group_failed = 0
            
            for kabupaten_data in kabupaten_group:
                success, kecamatan_count, error_msg = process_kabupaten(kabupaten_data, args.max_retries)
                
                if success:
                    group_success += 1
                    total_success += 1
                    logger.info(f"✅ Successfully processed {kabupaten_data['nama_kabupaten']}")
                else:
                    group_failed += 1
                    total_failed += 1
                    logger.error(f"❌ Failed to process {kabupaten_data['nama_kabupaten']}: {error_msg}")
                
                time.sleep(KECAMATAN_RETRY_DELAY / 2) # Jeda antar kabupaten
            
            # Log group completion
            if group_failed == 0:
                log_import_process('kecamatan', url_induk_id, 'completed',
                                  total_processed=len(kabupaten_group), 
                                  total_success=group_success, 
                                  total_failed=group_failed)
                logger.info(f"✅ Completed kecamatan import for URL Induk ID {url_induk_id}")
            else:
                log_import_process('kecamatan', url_induk_id, 'failed',
                                  total_processed=len(kabupaten_group),
                                  total_success=group_success,
                                  total_failed=group_failed,
                                  error_message=f"{group_failed} kabupaten failed to process.")
                logger.error(f"❌ Failed some kecamatan imports for URL Induk ID {url_induk_id}")
        
        logger.info(f"\n=== KECAMATAN IMPORT SUMMARY ===")
        logger.info(f"Total Kabupaten Processed: {len(kabupaten_list)}")
        logger.info(f"Successful: {total_success}")
        logger.info(f"Failed: {total_failed}")
        
        if total_failed == 0:
            logger.info("🎉 Kecamatan import completed successfully!")
            sys.exit(0)
        else:
            logger.error(f"❌ Kecamatan import completed with {total_failed} failures.")
            sys.exit(1)
            
    except Exception as e:
        logger.error(f"Fatal error in kecamatan import: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()