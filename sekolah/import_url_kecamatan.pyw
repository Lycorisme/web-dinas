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

# Tambahkan path ke folder helper dan import koneksi
project_root = os.path.abspath(os.path.join(os.path.dirname(__file__), '..'))
sys.path.append(project_root)
from helper.db_connector import get_db_connection

# Setup logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

# Configuration
BASE_URL = "https://dapo.kemendikdasmen.go.id"
PAGE_LOAD_TIMEOUT = 300
RETRY_DELAY_SECONDS = 15
KECAMATAN_RETRY_DELAY = 3

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
        driver = uc.Chrome(options=options, version_main=140)
        driver.set_page_load_timeout(PAGE_LOAD_TIMEOUT)
        return driver
    except Exception as e:
        logger.error(f"Error setting up driver with version 140: {e}")
        try:
            driver = uc.Chrome(options=options, use_subprocess=True)
            driver.set_page_load_timeout(PAGE_LOAD_TIMEOUT)
            return driver
        except Exception as e2:
            logger.error(f"Error setting up driver with auto-detect: {e2}")
            return None

def get_kabupaten_data(kabupaten_ids=None):
    """Get kabupaten data from database"""
    conn = get_db_connection()
    if not conn:
        return []
    
    cursor = None
    try:
        cursor = conn.cursor(dictionary=True)
        query = "SELECT id, kode_kabupaten, nama_kabupaten, url, url_induk_id FROM kabupaten_scrape WHERE status='active'"
        params = []
        
        if kabupaten_ids and len(kabupaten_ids) > 0:
            placeholders = ','.join(['%s'] * len(kabupaten_ids))
            query += f" AND id IN ({placeholders})"
            params = kabupaten_ids

        cursor.execute(query, tuple(params))
        kabupaten_list = cursor.fetchall()
        logger.info(f"Found {len(kabupaten_list)} kabupaten records to process")
        return kabupaten_list
        
    except Error as e:
        logger.error(f"Error getting kabupaten data: {e}")
        return []
    finally:
        if cursor:
            cursor.close()
        if conn and conn.is_connected():
            conn.close()

def log_import_process(process_type, url_induk_id=None, status='running', total_processed=0, total_success=0, total_failed=0, error_message=None, user_id=1):
    """Log import process to database"""
    conn = get_db_connection()
    if not conn:
        logger.error("Failed to get database connection for logging")
        return None
    
    cursor = None
    try:
        cursor = conn.cursor()
        
        if status == 'running':
            # Insert new log entry
            if url_induk_id:
                cursor.execute("""
                    INSERT INTO import_log (user_id, process_type, url_induk_id, status, total_processed, started_at, created_at) 
                    VALUES (%s, %s, %s, %s, %s, NOW(), NOW())
                """, (user_id, process_type, url_induk_id, status, total_processed))
            else:
                cursor.execute("""
                    INSERT INTO import_log (user_id, process_type, status, total_processed, started_at, created_at) 
                    VALUES (%s, %s, %s, %s, NOW(), NOW())
                """, (user_id, process_type, status, total_processed))
            log_id = cursor.lastrowid
            conn.commit()
            logger.info(f"Created log entry with ID: {log_id}")
            return log_id
        else:
            # Update existing log entry
            if url_induk_id:
                cursor.execute("""
                    UPDATE import_log 
                    SET total_processed = %s, total_success = %s, total_failed = %s, 
                        status = %s, completed_at = NOW(), error_message = %s, updated_at = NOW()
                    WHERE process_type = %s AND url_induk_id = %s AND status = 'running' 
                    ORDER BY id DESC LIMIT 1
                """, (total_processed, total_success, total_failed, status, error_message, process_type, url_induk_id))
            else:
                cursor.execute("""
                    UPDATE import_log 
                    SET total_processed = %s, total_success = %s, total_failed = %s, 
                        status = %s, completed_at = NOW(), error_message = %s, updated_at = NOW()
                    WHERE process_type = %s AND status = 'running' 
                    ORDER BY id DESC LIMIT 1
                """, (total_processed, total_success, total_failed, status, error_message, process_type))
            
            affected_rows = cursor.rowcount
            conn.commit()
            logger.info(f"Updated log entry, affected rows: {affected_rows}")
            return affected_rows > 0
        
    except Error as e:
        logger.error(f"Error logging import process: {e}")
        if conn:
            conn.rollback()
        return None
    finally:
        if cursor:
            cursor.close()
        if conn and conn.is_connected():
            conn.close()

def save_kecamatan_to_db(kecamatan_data):
    """Save kecamatan data to database with verification"""
    if not kecamatan_data:
        logger.warning("No kecamatan data to save")
        return 0
    
    conn = get_db_connection()
    if not conn:
        logger.error("Failed to get database connection for saving")
        return 0
    
    cursor = None
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
                logger.info(f"Kecamatan {kode} already exists, skipping...")
        
        # Filter data baru
        new_data = [(kode, nama, url, kabupaten_scrape_id) 
                    for kode, nama, url, kabupaten_scrape_id in kecamatan_data 
                    if kode not in existing_codes]
        
        if not new_data:
            logger.info("All kecamatan data for this batch already exists in database")
            return 0
        
        logger.info(f"Inserting {len(new_data)} new kecamatan records...")
        
        # Insert data baru
        insert_query = """
            INSERT INTO kecamatan_scrape (kode_kecamatan, nama_kecamatan, url, kabupaten_scrape_id, status, created_at, updated_at) 
            VALUES (%s, %s, %s, %s, 'active', NOW(), NOW())
        """
        cursor.executemany(insert_query, new_data)
        conn.commit()
        
        # Verifikasi data tersimpan
        saved_count = 0
        for kode, nama, _, kabupaten_scrape_id in new_data:
            cursor.execute("""
                SELECT id FROM kecamatan_scrape 
                WHERE kode_kecamatan = %s AND kabupaten_scrape_id = %s
            """, (kode, kabupaten_scrape_id))
            if cursor.fetchone():
                saved_count += 1
                logger.debug(f"Verified: {nama} ({kode}) saved successfully")
        
        if saved_count == len(new_data):
            logger.info(f"Successfully inserted and verified {saved_count} new kecamatan records")
            return saved_count
        else:
            logger.warning(f"Only {saved_count}/{len(new_data)} kecamatan records were verified")
            return saved_count
            
    except Error as e:
        logger.error(f"Error saving kecamatan data: {e}")
        if conn:
            conn.rollback()
        return 0
    finally:
        if cursor:
            cursor.close()
        if conn and conn.is_connected():
            conn.close()

def scrape_kecamatan(url, kabupaten_name):
    """Scraping data kecamatan dari URL kabupaten"""
    logger.info(f"Starting kecamatan scraping for {kabupaten_name} from: {url}")
    
    driver = None
    try:
        driver = setup_driver()
        if not driver:
            raise Exception("Failed to setup driver")
        
        logger.info("Loading kabupaten page...")
        driver.get(url)
        
        # Wait for table to appear
        wait = WebDriverWait(driver, 60)
        wait.until(EC.presence_of_element_located((By.ID, "DataTables_Table_0")))
        logger.info("Kecamatan table found.")
        
        # Wait for data to load
        wait.until(EC.visibility_of_element_located((By.CSS_SELECTOR, "#DataTables_Table_0 tbody tr.data")))
        logger.info("Kecamatan data loaded successfully.")
        
        time.sleep(2)  # Wait to ensure all data is loaded
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
        
        logger.info(f"Found {len(rows)} kecamatan rows to process")
        
        kecamatan_data = []
        for idx, row in enumerate(rows, 1):
            cells = row.find_all('td')
            if len(cells) < 2:
                logger.warning(f"Row {idx} has insufficient cells, skipping...")
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
                    logger.debug(f"Extracted: {kode} - {nama}")
                else:
                    logger.warning(f"Row {idx} missing data: kode={kode}, nama={nama}, url={full_url}")
        
        if not kecamatan_data:
            raise Exception(f"No valid kecamatan data extracted from {kabupaten_name}.")
            
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
    url_induk_id = kabupaten_data['url_induk_id']
    
    logger.info(f"\n{'='*60}")
    logger.info(f"Processing Kabupaten: {nama_kabupaten} ({kode_kabupaten})")
    logger.info(f"URL: {url_kabupaten}")
    logger.info(f"{'='*60}")
    
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
            kecamatan_with_id = [(kode, nama, url, kabupaten_id) 
                                for kode, nama, url in kecamatan_data]
            
            # Save to database
            inserted_count = save_kecamatan_to_db(kecamatan_with_id)
            
            # Consider success even if 0 new records (duplicates)
            total_kecamatan = inserted_count
            success = True
            
            if inserted_count > 0:
                logger.info(f"Successfully saved {total_kecamatan} new kecamatan records from {nama_kabupaten}")
            else:
                logger.info(f"No new kecamatan records for {nama_kabupaten} (already exist).")

        except Exception as e:
            error_message = str(e)
            logger.error(f"Error processing kabupaten {nama_kabupaten}: {error_message}")
            retry_count += 1
            
            if retry_count >= max_retries:
                logger.error(f"Max retries reached for {nama_kabupaten}")
    
    return success, total_kecamatan, error_message

def main():
    parser = argparse.ArgumentParser(description='Import Kecamatan from Dapodik')
    parser.add_argument('--kabupaten_id', type=str, required=True, 
                       help='Comma-separated kabupaten_scrape_ids to process')
    args = parser.parse_args()
    
    # Parse kabupaten_id
    kabupaten_ids = None
    if args.kabupaten_id:
        try:
            kabupaten_ids = [int(id.strip()) for id in args.kabupaten_id.split(',')]
        except ValueError:
            logger.error("Invalid kabupaten_id format. Please provide comma-separated integers.")
            sys.exit(1)
    
    logger.info("="*80)
    logger.info("STARTING KECAMATAN IMPORT")
    logger.info("="*80)
    logger.info(f"Kabupaten IDs: {kabupaten_ids}")
    
    try:
        # Get kabupaten data
        kabupaten_list = get_kabupaten_data(kabupaten_ids)
        
        if not kabupaten_list:
            logger.error("No kabupaten records found for kecamatan processing")
            sys.exit(1)
        
        # Group kabupaten by url_induk_id
        url_induk_groups = {}
        for kab in kabupaten_list:
            url_induk_id = kab['url_induk_id']
            if url_induk_id not in url_induk_groups:
                url_induk_groups[url_induk_id] = []
            url_induk_groups[url_induk_id].append(kab)
        
        # Process each group
        for url_induk_id, kabupaten_group in url_induk_groups.items():
            logger.info(f"\nProcessing kabupaten group for URL Induk ID: {url_induk_id}")
            
            # Log process start
            total_kabupaten = len(kabupaten_group)
            log_id = log_import_process('kecamatan', url_induk_id, 'running', 
                                       total_processed=total_kabupaten, user_id=1)
            
            if not log_id:
                logger.warning("Failed to create log entry, continuing anyway...")
            
            # Initialize counters
            processed_count = 0
            success_count = 0
            failed_count = 0
            total_kecamatan_saved = 0
            
            # Process each kabupaten
            for kabupaten_data in kabupaten_group:
                success, kecamatan_count, error_msg = process_kabupaten(kabupaten_data, 3)
                
                # Update counters
                processed_count += 1
                if success:
                    success_count += 1
                    total_kecamatan_saved += kecamatan_count
                    logger.info(f"Successfully processed {kabupaten_data['nama_kabupaten']}")
                else:
                    failed_count += 1
                    logger.error(f"Failed to process {kabupaten_data['nama_kabupaten']}: {error_msg}")
                
                # Update progress
                log_import_process(
                    'kecamatan', 
                    url_induk_id, 
                    status='running',
                    total_processed=total_kabupaten,
                    total_success=success_count,
                    total_failed=failed_count,
                    user_id=1
                )
                
                # Brief delay between kabupaten
                if processed_count < total_kabupaten:
                    time.sleep(1)
            
            # Log group completion
            final_status = 'completed' if failed_count == 0 else 'failed'
            error_msg = f"{failed_count} kabupaten failed to process." if failed_count > 0 else None
            
            log_import_process('kecamatan', url_induk_id, final_status,
                              total_processed=total_kabupaten, 
                              total_success=success_count, 
                              total_failed=failed_count,
                              error_message=error_msg,
                              user_id=1)
            
            if failed_count == 0:
                logger.info(f"Completed kecamatan import for URL Induk ID {url_induk_id}")
            else:
                logger.error(f"Failed some kecamatan imports for URL Induk ID {url_induk_id}")
        
        # Final summary
        logger.info("\n" + "="*80)
        logger.info("KECAMATAN IMPORT SUMMARY")
        logger.info("="*80)
        logger.info(f"Total Kabupaten Processed: {len(kabupaten_list)}")
        logger.info(f"Successful: {success_count}")
        logger.info(f"Failed: {failed_count}")
        logger.info(f"Total Kecamatan Saved: {total_kecamatan_saved}")
        logger.info("="*80)
        
        if failed_count == 0:
            logger.info("Kecamatan import completed successfully!")
            sys.exit(0)
        else:
            logger.error(f"Kecamatan import completed with {failed_count} failures.")
            sys.exit(1)
            
    except Exception as e:
        logger.error(f"Fatal error in kecamatan import: {e}", exc_info=True)
        sys.exit(1)

if __name__ == "__main__":
    main()