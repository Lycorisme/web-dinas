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

def get_url_induk_data(url_induk_id=None):
    """Get URL induk data from database"""
    conn = get_db_connection()
    if not conn:
        return []
    
    try:
        cursor = conn.cursor(dictionary=True)
        
        if url_induk_id:
            cursor.execute("""
                SELECT id, url, description 
                FROM url_induk_scrape 
                WHERE id = %s AND status = 'active'
            """, (url_induk_id,))
        else:
            cursor.execute("""
                SELECT id, url, description 
                FROM url_induk_scrape 
                WHERE status = 'active'
            """)
        
        urls = cursor.fetchall()
        logger.info(f"Found {len(urls)} active URL induk records")
        return urls
        
    except Error as e:
        logger.error(f"Error getting URL induk data: {e}")
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
                INSERT INTO import_log (process_type, url_induk_id, status) 
                VALUES (%s, %s, %s)
            """, (process_type, url_induk_id, status))
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

def save_kabupaten_to_db(kabupaten_data, url_induk_id):
    """Save kabupaten data to database"""
    if not kabupaten_data:
        return 0
    
    conn = get_db_connection()
    if not conn:
        return 0
    
    try:
        cursor = conn.cursor()
        
        # Cek data yang sudah ada
        existing_codes = set()
        for kode, _, _ in kabupaten_data:
            cursor.execute("""
                SELECT kode_kabupaten FROM kabupaten_scrape 
                WHERE kode_kabupaten = %s AND url_induk_id = %s
            """, (kode, url_induk_id))
            if cursor.fetchone():
                existing_codes.add(kode)
        
        # Filter data baru
        new_data = [(kode, nama, url, url_induk_id) for kode, nama, url in kabupaten_data if kode not in existing_codes]
        
        if not new_data:
            logger.info("All kabupaten data already exists in database")
            return 0
            
        # Insert data baru
        insert_query = """
            INSERT INTO kabupaten_scrape (kode_kabupaten, nama_kabupaten, url, url_induk_id) 
            VALUES (%s, %s, %s, %s)
        """
        cursor.executemany(insert_query, new_data)
        conn.commit()
        
        # Verifikasi data tersimpan
        saved_count = 0
        for kode, _, _, _ in new_data:
            cursor.execute("""
                SELECT 1 FROM kabupaten_scrape 
                WHERE kode_kabupaten = %s AND url_induk_id = %s
            """, (kode, url_induk_id))
            if cursor.fetchone():
                saved_count += 1
        
        if saved_count == len(new_data):
            logger.info(f"Successfully inserted {saved_count} kabupaten records")
            return saved_count
        else:
            logger.error(f"Only {saved_count}/{len(new_data)} kabupaten records were saved")
            return saved_count
            
    except Error as e:
        logger.error(f"Error saving kabupaten data: {e}")
        conn.rollback()
        return 0
    finally:
        if conn.is_connected():
            cursor.close()
            conn.close()

def scrape_kabupaten(url):
    """Scraping data kabupaten dari URL"""
    logger.info(f"Starting kabupaten scraping from: {url}")
    
    driver = None
    try:
        driver = setup_driver()
        if not driver:
            raise Exception("Failed to setup driver")
        
        driver.get(url)
        logger.info("Waiting for page to load...")
        
        # Wait for table to appear
        wait = WebDriverWait(driver, 60)
        wait.until(EC.presence_of_element_located((By.ID, "DataTables_Table_0")))
        logger.info("Table found.")
        
        # Wait for data to load
        wait.until(EC.visibility_of_element_located((By.CSS_SELECTOR, "#DataTables_Table_0 tbody tr.data")))
        logger.info("Table data loaded successfully.")
        
        time.sleep(3)  # Additional wait to ensure all data is loaded
        page_html = driver.page_source
        soup = BeautifulSoup(page_html, 'html.parser')
        
        table = soup.find('table', id='DataTables_Table_0')
        if not table:
            raise Exception("Table not found on page.")
            
        tbody = table.find('tbody')
        if not tbody:
            raise Exception("Tbody not found in table.")
            
        rows = tbody.find_all('tr', class_='data')
        if not rows:
            raise Exception("No data rows found in table.")
            
        data_to_insert = []
        for row in rows:
            cells = row.find_all('td')
            if len(cells) < 2:
                continue
                
            # Kolom pertama adalah kode kabupaten
            kode_cell = cells[0]
            kode = kode_cell.get_text(strip=True)
            
            # Kolom kedua berisi nama kabupaten dan link
            kabupaten_cell = cells[1]
            anchor_tag = kabupaten_cell.find('a')
            if anchor_tag:
                nama = anchor_tag.get_text(strip=True)
                href = anchor_tag.get('href', '').strip()
                
                # Build full URL
                full_url = urljoin(BASE_URL, href) if href else ''
                
                if kode and nama and full_url:
                    data_to_insert.append((kode, nama, full_url))
        
        if not data_to_insert:
            raise Exception("No kabupaten data extracted.")
            
        logger.info(f"Successfully extracted {len(data_to_insert)} kabupaten records.")
        return data_to_insert
        
    except Exception as e:
        logger.error(f"Error scraping kabupaten: {e}")
        raise
    finally:
        if driver:
            driver.quit()
            logger.info("Browser closed.")

def process_url_induk(url_induk_data, max_retries):
    """Process single URL induk for kabupaten scraping"""
    url_induk_id = url_induk_data['id']
    url = url_induk_data['url']
    description = url_induk_data.get('description', '')
    
    logger.info(f"Processing URL Induk ID: {url_induk_id}")
    logger.info(f"URL: {url}")
    logger.info(f"Description: {description}")
    
    # Log process start
    log_id = log_import_process('kabupaten', url_induk_id, 'running')
    
    retry_count = 0
    success = False
    total_kabupaten = 0
    error_message = None
    
    while retry_count < max_retries and not success:
        try:
            if retry_count > 0:
                logger.info(f"Retry attempt {retry_count + 1}/{max_retries}")
                time.sleep(RETRY_DELAY_SECONDS)
            
            # Scrape kabupaten data
            kabupaten_data = scrape_kabupaten(url)
            
            # Save to database
            inserted_count = save_kabupaten_to_db(kabupaten_data, url_induk_id)
            
            if inserted_count > 0:
                total_kabupaten = inserted_count
                success = True
                logger.info(f"Successfully processed {total_kabupaten} kabupaten records")
            else:
                logger.warning("No new kabupaten records were inserted (might be duplicates)")
                success = True  # Consider this as success
                
        except Exception as e:
            error_message = str(e)
            logger.error(f"Error processing URL Induk ID {url_induk_id}: {error_message}")
            retry_count += 1
    
    # Log process completion
    if success:
        log_import_process('kabupaten', url_induk_id, 'completed', 
                          total_processed=1, total_success=1, total_failed=0)
        logger.info(f"✅ Successfully completed kabupaten import for URL Induk ID {url_induk_id}")
        return True
    else:
        log_import_process('kabupaten', url_induk_id, 'failed',
                          total_processed=1, total_success=0, total_failed=1, 
                          error_message=error_message)
        logger.error(f"❌ Failed kabupaten import for URL Induk ID {url_induk_id}")
        return False

def main():
    parser = argparse.ArgumentParser(description='Import Kabupaten from Dapodik')
    parser.add_argument('--url_induk_id', type=int, help='Specific URL Induk ID to process')
    parser.add_argument('--max_retries', type=int, default=300, help='Maximum retry attempts')
    
    args = parser.parse_args()
    
    logger.info("=== STARTING KABUPATEN IMPORT ===")
    logger.info(f"URL Induk ID: {args.url_induk_id}")
    logger.info(f"Max Retries: {args.max_retries}")
    
    try:
        # Get URL induk data
        url_induk_list = get_url_induk_data(args.url_induk_id)
        
        if not url_induk_list:
            logger.error("No active URL induk records found")
            sys.exit(1)
        
        total_success = 0
        total_failed = 0
        
        for url_induk_data in url_induk_list:
            if process_url_induk(url_induk_data, args.max_retries):
                total_success += 1
            else:
                total_failed += 1
        
        logger.info(f"\n=== KABUPATEN IMPORT SUMMARY ===")
        logger.info(f"Total URL Induk Processed: {len(url_induk_list)}")
        logger.info(f"Successful: {total_success}")
        logger.info(f"Failed: {total_failed}")
        
        if total_success > 0:
            logger.info("🎉 Kabupaten import completed with some success!")
            sys.exit(0)
        else:
            logger.error("❌ All kabupaten imports failed")
            sys.exit(1)
            
    except Exception as e:
        logger.error(f"Fatal error in kabupaten import: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()