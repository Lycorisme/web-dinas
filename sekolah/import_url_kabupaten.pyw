#!/usr/bin/env python
# -*- coding: utf-8 -*-

"""
===============================================================
IMPORT URL KABUPATEN - VERSI REFACTOR
===============================================================
- Menggunakan db_helper.py untuk koneksi & logging
- Menggunakan driver_helper.py untuk setup Selenium
- Menerima path driver & log_id dari import_handler.php
===============================================================
"""

import sys
import os
import logging
import argparse
import time
import re
from urllib.parse import urljoin

# ----------------------------------------------------------
# 1. SETUP LOGGING & IMPORTS
# ----------------------------------------------------------
log_file = r'C:\laragon\www\dapokalsel\log_kabupaten.txt'
logging.basicConfig(
    filename=log_file,
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s'
)

logging.info("=" * 80)
logging.info(">>> [REFACTOR] import_url_kabupaten.pyw dimulai <<<")
logging.info(f"Arguments: {sys.argv}")

try:
    from bs4 import BeautifulSoup
    from selenium.webdriver.common.by import By
    from selenium.webdriver.support.ui import WebDriverWait
    from selenium.webdriver.support import expected_conditions as EC
    
    # IMPOR HELPER BARU
    import db_helper
    import driver_helper
    
    logging.info("✓ Library & Helper berhasil diimport")

except ImportError as e:
    logging.error(f"✗ Gagal import library: {e}")
    sys.exit(1)

# ----------------------------------------------------------
# 2. PARSE ARGUMENTS (DENGAN TAMBAHAN)
# ----------------------------------------------------------
parser = argparse.ArgumentParser(description='Import URL Kabupaten dari Dapodik')
parser.add_argument('--url_induk_id', type=int, required=True, help='ID URL Induk')
parser.add_argument('--user_id', type=int, default=1, help='User ID')
parser.add_argument('--max_retries', type=int, default=300
    , help='Max retries untuk scraping')
parser.add_argument('--driver_path', type=str, required=True, help='Path absolut ke chromedriver.exe')
parser.add_argument('--log_id', type=int, required=True, help='ID dari import_log untuk diupdate')

args = parser.parse_args()
log_id = args.log_id # Simpan log_id untuk dipakai di 'except'

logging.info(f"Parsed arguments:")
logging.info(f"  - url_induk_id: {args.url_induk_id}")
logging.info(f"  - driver_path: {args.driver_path}")
logging.info(f"  - log_id: {args.log_id}")

# ----------------------------------------------------------
# 3. FUNGSI SCRAPING KABUPATEN
# ----------------------------------------------------------
def scrape_kabupaten(driver, url, max_retries=3):
    """Scrape daftar kabupaten dari halaman Dapodik"""
    for attempt in range(max_retries):
        try:
            logging.info(f"Attempt {attempt + 1}/{max_retries}: Fetching {url}")
            driver.get(url)
            
            # Tunggu hingga data tabel (tr.data) muncul
            WebDriverWait(driver, 60).until(
                EC.presence_of_element_located((By.CSS_SELECTOR, "tr.data"))
            )
            
            soup = BeautifulSoup(driver.page_source, 'html.parser')
            logging.info(f"Page loaded. Content Length: {len(driver.page_source)}")
            
            kabupaten_list = []
            rows = soup.find_all('tr', class_='data')
            logging.info(f"Total rows found: {len(rows)}")
            
            if not rows:
                raise Exception("Tidak ada 'tr.data' ditemukan di halaman")

            for row in rows:
                cells = row.find_all('td')
                if len(cells) >= 2:
                    link_cell = cells[1]  # Kolom kedua adalah "Wilayah"
                    link = link_cell.find('a', href=True)
                    
                    if link:
                        href = link.get('href', '').strip()
                        text = link.get_text(strip=True)
                        
                        if href and text:
                            full_url = urljoin('https://dapo.kemendikdasmen.go.id', href)
                            kode_match = re.search(r'/(\d{6})', href)
                            kode = kode_match.group(1) if kode_match else ''
                            
                            kabupaten_list.append({
                                'kode': kode,
                                'nama': text,
                                'url': full_url
                            })
                            logging.info(f"  ✓ Found: {text} (Kode: {kode})")
            
            if not kabupaten_list:
                raise Exception("tr.data ditemukan, tapi tidak ada link kabupaten yang valid")

            logging.info(f"✓ Berhasil scraping {len(kabupaten_list)} kabupaten")
            return kabupaten_list
        
        except Exception as e:
            logging.warning(f"⚠ Attempt {attempt + 1} gagal: {e}")
            if attempt < max_retries - 1:
                time.sleep(5) # Jeda sebelum retry
            else:
                logging.error(f"✗ Gagal scraping setelah {max_retries} attempts")
                return []
    return []

# ----------------------------------------------------------
# 4. FUNGSI SIMPAN DATA KABUPATEN
# ----------------------------------------------------------
def save_kabupaten_to_db(kabupaten_data, url_induk_id):
    """Simpan data kabupaten ke database"""
    success_count = 0
    failed_count = 0
    conn = db_helper.get_db_connection()
    if not conn:
        return 0, len(kabupaten_data)
        
    try:
        with conn.cursor() as cursor:
            for kab in kabupaten_data:
                try:
                    # Cek apakah sudah ada
                    cursor.execute(
                        "SELECT id FROM kabupaten_scrape WHERE url_induk_id = %s AND kode_kabupaten = %s",
                        (url_induk_id, kab['kode'])
                    )
                    existing = cursor.fetchone()
                    
                    if existing:
                        # Update jika sudah ada
                        cursor.execute("""
                            UPDATE kabupaten_scrape 
                            SET nama_kabupaten = %s, url = %s, status = 'active', updated_at = NOW()
                            WHERE id = %s
                        """, (kab['nama'], kab['url'], existing['id']))
                        logging.info(f"  ↻ Updated: {kab['nama']}")
                    else:
                        # Insert baru
                        cursor.execute("""
                            INSERT INTO kabupaten_scrape 
                            (url_induk_id, kode_kabupaten, nama_kabupaten, url, status, created_at, updated_at)
                            VALUES (%s, %s, %s, %s, 'active', NOW(), NOW())
                        """, (url_induk_id, kab['kode'], kab['nama'], kab['url']))
                        logging.info(f"  ✓ Inserted: {kab['nama']}")
                    
                    conn.commit()
                    success_count += 1
                    
                except Exception as e:
                    logging.error(f"  ✗ Gagal menyimpan {kab['nama']}: {e}")
                    failed_count += 1
                    conn.rollback()
    
    except Exception as e:
        logging.error(f"✗ Error besar saat menyimpan ke DB: {e}")
    finally:
        if conn:
            conn.close()
            
    return success_count, failed_count

# ----------------------------------------------------------
# 5. PROSES UTAMA
# ----------------------------------------------------------
driver = None
try:
    # 1. Ambil URL Induk
    base_url = db_helper.get_url_induk(args.url_induk_id)
    if not base_url:
        raise Exception(f"URL Induk dengan ID {args.url_induk_id} tidak ditemukan")
    logging.info(f"✓ URL Induk ditemukan: {base_url}")
    
    # 2. Setup Driver (Cara Baru)
    driver = driver_helper.setup_driver(driver_path=args.driver_path)
    if not driver:
        raise Exception("Gagal menginisialisasi Selenium Driver")
    
    # 3. Scraping
    kabupaten_data = scrape_kabupaten(driver, base_url, args.max_retries)
    total_processed = len(kabupaten_data)
    
    if not kabupaten_data:
        raise Exception("Tidak ada data kabupaten yang berhasil di-scrape")
    
    # 4. Simpan ke DB
    logging.info(f"Menyimpan {total_processed} data ke database...")
    success_count, failed_count = save_kabupaten_to_db(
        kabupaten_data, 
        args.url_induk_id
    )
    
    # 5. Update Log Sukses
    db_helper.finish_log_entry(
        log_id=log_id,
        status='completed',
        total_processed=total_processed,
        total_success=success_count,
        total_failed=failed_count
    )
    
    logging.info("=" * 80)
    logging.info("PROSES KABUPATEN SELESAI")
    logging.info(f"Total Ditemukan: {total_processed}")
    logging.info(f"Sukses Simpan: {success_count}")
    logging.info(f"Gagal Simpan: {failed_count}")
    logging.info("=" * 80)
    
except Exception as e:
    logging.error(f"✗✗✗ ERROR FATAL: {e}")
    # Update Log Gagal
    db_helper.finish_log_entry(
        log_id=log_id,
        status='failed',
        total_processed=0,
        total_success=0,
        total_failed=0,
        error_message=str(e)
    )
    sys.exit(1)

finally:
    if driver:
        driver.quit()
        logging.info("✓ Driver Selenium ditutup")
    
    logging.info(">>> Script kabupaten selesai <<<")