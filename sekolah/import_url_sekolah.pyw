import sys
import time
import logging
import argparse
import json
import subprocess
from bs4 import BeautifulSoup
import undetected_chromedriver as uc
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait, Select
from selenium.webdriver.support import expected_conditions as EC
from urllib.parse import urljoin
from selenium.common.exceptions import TimeoutException, NoSuchElementException, WebDriverException, StaleElementReferenceException
import random
import os

# Setup logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

# Configuration
BASE_URL = "https://dapo.kemendikdasmen.go.id"
PAGE_LOAD_TIMEOUT = 600
RETRY_DELAY_SECONDS = 13
SEKOLAH_RETRY_DELAY = 13

# Jenjang sekolah yang akan di-scrape
JENJANG_SEKOLAH = ['sd', 'smp', 'sma', 'smk']

def execute_php_query(query, params=None):
    """Execute PHP query through subprocess for database operations"""
    try:
        if params is None:
            params = []
        
        # Escape parameters for PHP
        escaped_params = []
        for param in params:
            if isinstance(param, str):
                escaped_param = param.replace("'", "\\'").replace('"', '\\"')
                escaped_params.append(f"'{escaped_param}'")
            else:
                escaped_params.append(str(param))
        
        # Create PHP command
        if escaped_params:
            php_query = query.replace('?', '{}').format(*escaped_params)
        else:
            php_query = query
        
        php_code = f"""
require_once '../helper/connection.php';
try {{
    $result = mysqli_query($connection, "{php_query}");
    if ($result === false) {{
        echo json_encode(['error' => mysqli_error($connection)]);
    }} else {{
        if (is_bool($result)) {{
            echo json_encode(['success' => $result, 'affected_rows' => mysqli_affected_rows($connection)]);
        }} else {{
            $data = [];
            while ($row = mysqli_fetch_assoc($result)) {{
                $data[] = $row;
            }}
            echo json_encode(['success' => true, 'data' => $data]);
        }}
    }}
}} catch (Exception $e) {{
    echo json_encode(['error' => $e->getMessage()]);
}}
mysqli_close($connection);
        """
        
        result = subprocess.run(['php', '-r', php_code], capture_output=True, text=True, cwd=os.path.dirname(__file__))
        
        if result.returncode != 0:
            logger.error(f"PHP execution failed: {result.stderr}")
            return None
        
        try:
            return json.loads(result.stdout)
        except json.JSONDecodeError as e:
            logger.error(f"JSON decode error: {e}, Output: {result.stdout}")
            return None
            
    except Exception as e:
        logger.error(f"Error executing PHP query: {e}")
        return None

def get_kecamatan_data(kecamatan_ids):
    """Get kecamatan data from database by multiple IDs"""
    try:
        # Buat placeholder untuk query IN
        placeholders = ','.join([str(id) for id in kecamatan_ids])
        
        query = f"""
        SELECT id, kode_kecamatan, nama_kecamatan, url, kabupaten_scrape_id
        FROM kecamatan_scrape
        WHERE id IN ({placeholders}) AND status = 'active'
        """
        
        result = execute_php_query(query)
        
        if result and result.get('success') and result.get('data'):
            logger.info(f"Found {len(result['data'])} kecamatan records")
            return result['data']
        else:
            logger.error(f"No kecamatan data found or query failed: {result}")
            return None
            
    except Exception as e:
        logger.error(f"Error getting kecamatan data: {e}")
        return None

def save_sekolah_to_db(sekolah_data_batch):
    """Save sekolah data to database with verification"""
    if not sekolah_data_batch:
        return 0
    
    try:
        logger.info(f"Attempting to save {len(sekolah_data_batch)} sekolah records to database")
        
        # First, check which records already exist
        existing_count = 0
        new_records = []
        
        for npsn, nama, url, kecamatan_id, jenjang in sekolah_data_batch:
            # Check if record exists
            check_query = f"SELECT id FROM sekolah_scrape WHERE npsn = '{npsn}' AND kecamatan_scrape_id = {kecamatan_id}"
            result = execute_php_query(check_query)
            
            if result and result.get('success') and result.get('data'):
                existing_count += 1
                logger.debug(f"Sekolah {npsn} already exists, skipping")
            else:
                new_records.append((npsn, nama, url, kecamatan_id, jenjang))
        
        if existing_count > 0:
            logger.info(f"Skipping {existing_count} existing records")
        
        if not new_records:
            logger.info("All sekolah records already exist in database")
            return 0
        
        # Insert new records one by one for better error handling
        inserted_count = 0
        for npsn, nama, url, kecamatan_id, jenjang in new_records:
            try:
                # Escape special characters
                nama_escaped = nama.replace("'", "\\'").replace('"', '\\"')
                url_escaped = url.replace("'", "\\'")
                
                insert_query = f"""
                INSERT INTO sekolah_scrape (npsn, nama_sekolah, url, kecamatan_scrape_id, jenjang, status, created_at, updated_at) 
                VALUES ('{npsn}', '{nama_escaped}', '{url_escaped}', {kecamatan_id}, '{jenjang}', 'active', NOW(), NOW())
                """
                
                result = execute_php_query(insert_query)
                if result and result.get('success'):
                    inserted_count += 1
                    logger.debug(f"Successfully inserted sekolah {npsn}: {nama}")
                else:
                    logger.error(f"Failed to insert sekolah {npsn}: {result}")
                    
            except Exception as e:
                logger.error(f"Error inserting individual sekolah record {npsn}: {e}")
                continue
        
        logger.info(f"Successfully inserted {inserted_count} new sekolah records")
        return inserted_count
        
    except Exception as e:
        logger.error(f"Error saving sekolah data: {e}")
        return 0

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
        # Coba dengan version_main=140 untuk Chrome 140.x
        driver = uc.Chrome(options=options, version_main=140)
        driver.set_page_load_timeout(PAGE_LOAD_TIMEOUT)
        return driver
    except Exception as e:
        logger.error(f"Error setting up driver with version 140: {e}")
        try:
            # Fallback: biarkan auto-detect
            driver = uc.Chrome(options=options, use_subprocess=True)
            driver.set_page_load_timeout(PAGE_LOAD_TIMEOUT)
            return driver
        except Exception as e2:
            logger.error(f"Error setting up driver with auto-detect: {e2}")
            return None

def human_like_delay():
    """Add human-like random delay"""
    delay = random.uniform(1.5, 3.5)
    time.sleep(delay)

def safe_click(driver, element):
    """Safely click an element with human-like behavior"""
    try:
        # Scroll to element
        driver.execute_script("arguments[0].scrollIntoView(true);", element)
        human_like_delay()
        
        # Try regular click first
        try:
            element.click()
        except:
            # If regular click fails, try JavaScript click
            driver.execute_script("arguments[0].click();", element)
        
        human_like_delay()
        return True
        
    except Exception as e:
        logger.error(f"Error clicking element: {e}")
        return False

def wait_for_ajax_complete(driver, timeout=30):
    """Wait for AJAX requests to complete"""
    try:
        wait = WebDriverWait(driver, timeout)
        wait.until(lambda driver: driver.execute_script("return jQuery.active == 0") if driver.execute_script("return typeof jQuery !== 'undefined'") else True)
        return True
    except:
        # If jQuery is not available, wait for document ready state
        try:
            wait.until(lambda driver: driver.execute_script("return document.readyState") == "complete")
            return True
        except:
            logger.warning("Could not confirm AJAX completion, continuing anyway")
            return False

def extract_sekolah_data_from_page(driver, jenjang, max_retries=50):
    """Extract sekolah data from current page with retry mechanism"""
    
    for attempt in range(max_retries):
        try:
            logger.info(f"Extracting data attempt {attempt + 1}/{max_retries} for jenjang {jenjang}")
            time.sleep(3)
            
            # Get page source
            page_source = driver.page_source
            soup = BeautifulSoup(page_source, 'html.parser')
            
            # Find the table - try multiple selectors
            table = None
            table_selectors = [
                '#dataTables',
                '#DataTables_Table_0',
                'table.dataTable',
                '.table-responsive table',
                'table.table'
            ]
            
            for selector in table_selectors:
                try:
                    if selector.startswith('#'):
                        table = soup.find('table', id=selector[1:])
                    elif selector.startswith('.'):
                        classes = selector[1:].split('.')
                        table = soup.find('table', class_=classes)
                    else:
                        table = soup.select_one(selector)
                    
                    if table:
                        logger.info(f"Table found using selector: {selector}")
                        break
                except Exception as e:
                    logger.debug(f"Selector {selector} failed: {e}")
                    continue
            
            if not table:
                logger.error(f"No table found for jenjang {jenjang}")
                if attempt < max_retries - 1:
                    time.sleep(SEKOLAH_RETRY_DELAY)
                    continue
                return []
            
            # Find tbody or get rows directly from table
            tbody = table.find('tbody')
            if tbody:
                rows = tbody.find_all('tr')
            else:
                rows = table.find_all('tr')[1:]  # Skip header row
            
            # Jika ada tulisan "No data available in table" → langsung skip
            if len(rows) == 1:
                row_text = rows[0].get_text(strip=True)
                if "No data available" in row_text or "Tidak ada data" in row_text:
                    logger.warning(f"Tabel kosong untuk jenjang {jenjang}, skip jenjang ini")
                    return None   # Flag untuk skip
            
            if not rows:
                logger.warning(f"No data rows found in table for jenjang {jenjang}")
                return []
            
            sekolah_data = []
            processed_npsn = set()  # To avoid duplicates
            
            for row_idx, row in enumerate(rows):
                try:
                    cells = row.find_all('td')
                    if len(cells) < 3:
                        logger.debug(f"Row {row_idx} has insufficient columns: {len(cells)}")
                        continue
                    
                    # Extract data based on the HTML structure observed
                    # Assuming: Column 1 = Name+Link, Column 2 = NPSN, Column 3 = BP/Type
                    
                    # Get school name and URL from first column with link
                    nama_cell = cells[1] if len(cells) > 1 else cells[0]
                    anchor = nama_cell.find('a')
                    
                    if not anchor:
                        logger.debug(f"No anchor tag found in row {row_idx}")
                        continue
                    
                    nama_sekolah = anchor.get_text(strip=True)
                    href = anchor.get('href', '').strip()
                    
                    if not nama_sekolah or not href:
                        logger.debug(f"Missing name or href in row {row_idx}")
                        continue
                    
                    # Build full URL
                    full_url = urljoin(BASE_URL, href)
                    
                    # Get NPSN from second column (index 2)
                    npsn_cell = cells[2] if len(cells) > 2 else None
                    if not npsn_cell:
                        logger.debug(f"No NPSN cell found in row {row_idx}")
                        continue
                    
                    npsn = npsn_cell.get_text(strip=True)
                    
                    if not npsn or npsn in processed_npsn:
                        logger.debug(f"Invalid or duplicate NPSN in row {row_idx}: {npsn}")
                        continue
                    
                    # Validate NPSN format (should be numeric)
                    try:
                        int(npsn)
                    except ValueError:
                        logger.debug(f"Invalid NPSN format in row {row_idx}: {npsn}")
                        continue
                    
                    processed_npsn.add(npsn)
                    sekolah_data.append((npsn, nama_sekolah, full_url))
                    
                    logger.debug(f"Extracted: NPSN={npsn}, Name={nama_sekolah[:50]}...")
                    
                except Exception as e:
                    logger.error(f"Error processing row {row_idx}: {e}")
                    continue
            
            if sekolah_data:
                logger.info(f"Successfully extracted {len(sekolah_data)} sekolah records for jenjang {jenjang}")
                return sekolah_data
            else:
                logger.warning(f"No valid data extracted for jenjang {jenjang} on attempt {attempt + 1}")
                if attempt < max_retries - 1:
                    time.sleep(SEKOLAH_RETRY_DELAY)
                
        except Exception as e:
            logger.error(f"Error extracting data for jenjang {jenjang} on attempt {attempt + 1}: {e}")
            if attempt < max_retries - 1:
                time.sleep(SEKOLAH_RETRY_DELAY)
    
    return []

def scrape_sekolah_from_kecamatan(driver, kecamatan_data, max_retries):
    """Scrape sekolah data from a single kecamatan with unlimited retry"""
    
    kecamatan_id = kecamatan_data['id']
    nama_kecamatan = kecamatan_data['nama_kecamatan']
    url_kecamatan = kecamatan_data['url']
    
    logger.info(f"Processing Kecamatan: {nama_kecamatan}")
    logger.info(f"URL: {url_kecamatan}")
    
    retry_count = 0
    total_sekolah_saved = 0
    
    while retry_count < max_retries:
        try:
            if retry_count > 0:
                logger.info(f"Retry attempt {retry_count + 1} for {nama_kecamatan}")
                time.sleep(SEKOLAH_RETRY_DELAY)
            
            # Navigate to kecamatan page
            logger.info("Loading kecamatan page...")
            driver.get(url_kecamatan)
            
            # Wait for page to load
            time.sleep(5)
            
            # Check if page loaded successfully
            try:
                WebDriverWait(driver, 30).until(
                    lambda driver: driver.execute_script("return document.readyState") == "complete"
                )
            except TimeoutException:
                logger.warning("Page load timeout, but continuing...")
            
            # Process each jenjang
            for jenjang in JENJANG_SEKOLAH:
                jenjang_retry = 0
                jenjang_success = False
                
                while jenjang_retry < 10 and not jenjang_success:
                    try:
                        if jenjang_retry > 0:
                            logger.info(f"Jenjang retry {jenjang_retry + 1} for {jenjang} in {nama_kecamatan}")
                            time.sleep(SEKOLAH_RETRY_DELAY)
                        
                        logger.info(f"Processing jenjang: {jenjang}")
                        
                        # Find and interact with jenjang dropdown
                        dropdown_selectors = [
                            '#selectJenjang',
                            'select[name="jenjang"]',
                            'select.form-control',
                            '.form-group select'
                        ]
                        
                        dropdown_element = None
                        for selector in dropdown_selectors:
                            try:
                                dropdown_element = WebDriverWait(driver, 10).until(
                                    EC.presence_of_element_located((By.CSS_SELECTOR, selector))
                                )
                                logger.info(f"Dropdown found with selector: {selector}")
                                break
                            except TimeoutException:
                                continue
                        
                        if not dropdown_element:
                            raise Exception("Jenjang dropdown not found")
                        
                        # Get current table reference before changing dropdown
                        try:
                            old_table = driver.find_element(By.CSS_SELECTOR, "table")
                        except:
                            old_table = None
                        
                        # Select jenjang
                        select_obj = Select(dropdown_element)
                        select_obj.select_by_value(jenjang)
                        logger.info(f"Selected jenjang: {jenjang}")
                        
                        # Wait for table to refresh after dropdown selection
                        if old_table:
                            try:
                                WebDriverWait(driver, 20).until(EC.staleness_of(old_table))
                                logger.info("Table has been refreshed")
                            except TimeoutException:
                                logger.warning("Could not confirm table refresh, continuing...")
                        
                        # Wait for new table to appear
                        try:
                            WebDriverWait(driver, 20).until(
                                EC.presence_of_element_located((By.CSS_SELECTOR, "table tbody tr"))
                            )
                        except TimeoutException:
                            logger.warning("No table rows found, might be empty")
                        
                        # Wait for AJAX to complete
                        wait_for_ajax_complete(driver)
                        
                        # Additional wait to ensure data is loaded
                        time.sleep(5)
                        
                        # Extract data
                        sekolah_data = extract_sekolah_data_from_page(driver, jenjang)
                        
                        # Flag skip jenjang karena tabel kosong
                        if sekolah_data is None:
                            logger.info(f"Skip jenjang {jenjang} karena tabel kosong")
                            jenjang_success = True
                            continue
                        
                        if not sekolah_data:
                            logger.warning(f"No sekolah data found untuk jenjang {jenjang}")
                            jenjang_success = True  # tetap anggap sukses biar lanjut ke jenjang lain
                            continue
                        
                        # Add kecamatan_id and jenjang to each record
                        sekolah_with_meta = [
                            (npsn, nama, url, kecamatan_id, jenjang) 
                            for npsn, nama, url in sekolah_data
                        ]
                        
                        # Save to database
                        saved_count = save_sekolah_to_db(sekolah_with_meta)
                        total_sekolah_saved += saved_count
                        
                        logger.info(f"Saved {saved_count} sekolah records for jenjang {jenjang}")
                        jenjang_success = True
                        
                    except Exception as e:
                        logger.error(f"Error processing jenjang {jenjang}: {e}")
                        jenjang_retry += 1
                        
                        # If too many jenjang retries, refresh page
                        if jenjang_retry % 3 == 0 and jenjang_retry < 10:
                            logger.info("Refreshing page due to repeated jenjang failures...")
                            driver.get(url_kecamatan)
                            time.sleep(5)
                
                if not jenjang_success:
                    logger.error(f"Failed to process jenjang {jenjang} after retries")
            
            # If we reach here, consider kecamatan processing successful
            logger.info(f"Successfully processed kecamatan {nama_kecamatan} with {total_sekolah_saved} sekolah saved")
            return True, total_sekolah_saved, None
            
        except Exception as e:
            error_message = str(e)
            logger.error(f"Error processing kecamatan {nama_kecamatan}: {error_message}")
            retry_count += 1
            
            if retry_count >= max_retries:
                logger.error(f"Max retries reached for kecamatan {nama_kecamatan}")
                return False, total_sekolah_saved, error_message
    
    return False, total_sekolah_saved, "Unknown error"

def main():
    # PERBAIKAN: Argument parser sesuai dengan perintah handler
    parser = argparse.ArgumentParser(description='Import Sekolah dari Dapodik')
    parser.add_argument('--kecamatan_id', type=str, required=True, help='Comma-separated kecamatan IDs yang akan di-scrape')
    parser.add_argument('--max_retries', type=int, default=3, help='Jumlah maksimal retry')
    args = parser.parse_args()
    
    # Parse kecamatan_id (bukan kecamatan_ids, sesuai handler)
    try:
        kecamatan_ids = [int(id.strip()) for id in args.kecamatan_id.split(',')]
    except ValueError:
        logger.error("Invalid kecamatan_id format. Please provide comma-separated integers.")
        sys.exit(1)
    
    logger.info("="*80)
    logger.info("STARTING SEKOLAH IMPORT")
    logger.info("="*80)
    logger.info(f"Kecamatan IDs: {kecamatan_ids}")

    # Ambil data untuk multiple kecamatan
    kecamatan_list = get_kecamatan_data(kecamatan_ids)
    if not kecamatan_list:
        logger.error("Kecamatan tidak ditemukan di database")
        sys.exit(1)

    driver = setup_driver()
    if not driver:
        logger.error("Gagal setup driver Chrome")
        sys.exit(1)

    # Inisialisasi counter untuk total sekolah
    total_sekolah_saved = 0
    success_count = 0
    failed_count = 0
    
    try:
        # Proses setiap kecamatan
        for kecamatan_data in kecamatan_list:
            logger.info(f"\n{'='*60}")
            logger.info(f"Processing: {kecamatan_data['nama_kecamatan']}")
            logger.info(f"{'='*60}")
            
            success, sekolah_count, error_msg = scrape_sekolah_from_kecamatan(
                driver, kecamatan_data, args.max_retries
            )
            
            total_sekolah_saved += sekolah_count
            
            if success:
                success_count += 1
                logger.info(f"Selesai import {sekolah_count} sekolah untuk {kecamatan_data['nama_kecamatan']}")
            else:
                failed_count += 1
                logger.error(f"Gagal import sekolah untuk {kecamatan_data['nama_kecamatan']}: {error_msg}")
            
            # Jeda antar kecamatan
            if kecamatan_data != kecamatan_list[-1]:
                time.sleep(3)
        
        # Log summary
        logger.info("\n" + "="*80)
        logger.info("SEKOLAH IMPORT SUMMARY")
        logger.info("="*80)
        logger.info(f"Total Kecamatan Processed: {len(kecamatan_list)}")
        logger.info(f"Successful: {success_count}")
        logger.info(f"Failed: {failed_count}")
        logger.info(f"Total Sekolah Saved: {total_sekolah_saved}")
        logger.info("="*80)
        
        if failed_count == 0:
            logger.info("Sekolah import completed successfully!")
            sys.exit(0)
        else:
            logger.error(f"Sekolah import completed with {failed_count} failures.")
            sys.exit(1)
            
    except Exception as e:
        logger.error(f"Fatal error in sekolah import: {e}", exc_info=True)
        sys.exit(1)
    finally:
        try:
            if driver:
                driver.quit()
                logger.info("Browser closed successfully")
        except Exception as e:
            logger.error(f"Error closing browser: {e}")

if __name__ == "__main__":
    main()