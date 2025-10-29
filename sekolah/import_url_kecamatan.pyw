#!/usr/bin/env python
# -*- coding: utf-8 -*-

"""
===============================================================
IMPORT URL KECAMATAN - VERSI REFACTOR (ROMBAK TOTAL)
===============================================================
- Diadaptasi penuh dari logika import_url_kabupaten.pyw
- Menggunakan db_helper.py dan driver_helper.py
- Menerima --kabupaten_id (banyak) dari import_handler.php
- Logika scraping diperbaiki sesuai HTML (kode diambil dari URL)
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
# (Pastikan file log ini bisa ditulis oleh server)
log_file = r'C:\laragon\www\dapokalsel\log_kecamatan.txt'
logging.basicConfig(
    filename=log_file,
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s'
)

logging.info("=" * 80)
logging.info(">>> [ROMBAK TOTAL] import_url_kecamatan.pyw dimulai <<<")
logging.info(f"Arguments: {sys.argv}")

try:
    from bs4 import BeautifulSoup
    from selenium.webdriver.common.by import By
    from selenium.webdriver.support.ui import WebDriverWait
    from selenium.webdriver.support import expected_conditions as EC
    
    # Impor helper terpusat
    import db_helper
    import driver_helper
    
    logging.info("✓ Library & Helper berhasil diimport")

except ImportError as e:
    logging.error(f"✗ Gagal import library: {e}")
    sys.exit(1)

# ----------------------------------------------------------
# 2. PARSE ARGUMENTS
# ----------------------------------------------------------
parser = argparse.ArgumentParser(description='Import URL Kecamatan dari Dapodik')
# Menerima --kabupaten_id sebagai string (contoh: "1,2,3")
parser.add_argument('--kabupaten_id', type=str, required=True, help='Comma-separated kabupaten_scrape_ids')
parser.add_argument('--user_id', type=int, default=1, help='User ID')
parser.add_argument('--max_retries', type=int, default=3, help='Max retries per kabupaten')
parser.add_argument('--driver_path', type=str, required=True, help='Path absolut ke chromedriver.exe')
parser.add_argument('--log_id', type=int, required=True, help='ID dari import_log untuk diupdate')

args = parser.parse_args()
log_id = args.log_id # Simpan log_id untuk dipakai di 'except'

logging.info(f"Parsed arguments:")
logging.info(f"  - kabupaten_id: {args.kabupaten_id}")
logging.info(f"  - driver_path: {args.driver_path}")
logging.info(f"  - log_id: {args.log_id}")

BASE_URL = "https://dapo.kemendikdasmen.go.id"

# ----------------------------------------------------------
# 3. FUNGSI HELPER DATABASE
# ----------------------------------------------------------

def get_kabupaten_to_process(kabupaten_ids_str):
    """
    Mengambil daftar URL kabupaten yang akan diproses dari DB.
    """
    conn = db_helper.get_db_connection()
    if not conn:
        return []
    try:
        with conn.cursor() as cursor:
            # Konversi string "1,2,3" menjadi daftar integer [1, 2, 3]
            id_list = [int(id_str) for id_str in kabupaten_ids_str.split(',')]
            if not id_list:
                return []
                
            placeholders = ','.join(['%s'] * len(id_list))
            query = f"SELECT id, nama_kabupaten, url FROM kabupaten_scrape WHERE id IN ({placeholders}) AND status = 'active'"
            
            cursor.execute(query, tuple(id_list))
            kabupaten_list = cursor.fetchall()
            logging.info(f"✓ Ditemukan {len(kabupaten_list)} kabupaten untuk diproses")
            return kabupaten_list
    except Exception as e:
        logging.error(f"✗ Gagal mengambil data kabupaten: {e}")
        return []
    finally:
        if conn:
            conn.close()

def save_kecamatan_to_db(kecamatan_data_batch, kabupaten_scrape_id):
    """
    Simpan data kecamatan ke database.
    Logika sama dengan 'save_kabupaten_to_db'.
    """
    success_count = 0
    failed_count = 0
    conn = db_helper.get_db_connection()
    if not conn:
        return 0, len(kecamatan_data_batch)

    try:
        with conn.cursor() as cursor:
            for kec in kecamatan_data_batch:
                try:
                    # Cek berdasarkan ID kabupaten dan KODE kecamatan
                    cursor.execute(
                        "SELECT id FROM kecamatan_scrape WHERE kabupaten_scrape_id = %s AND kode_kecamatan = %s",
                        (kabupaten_scrape_id, kec['kode'])
                    )
                    existing = cursor.fetchone()
                    
                    if existing:
                        cursor.execute("""
                            UPDATE kecamatan_scrape 
                            SET nama_kecamatan = %s, url = %s, status = 'active', updated_at = NOW()
                            WHERE id = %s
                        """, (kec['nama'], kec['url'], existing['id']))
                        logging.info(f"  ↻ Updated: {kec['nama']}")
                    else:
                        cursor.execute("""
                            INSERT INTO kecamatan_scrape 
                            (kabupaten_scrape_id, kode_kecamatan, nama_kecamatan, url, status, created_at, updated_at)
                            VALUES (%s, %s, %s, %s, 'active', NOW(), NOW())
                        """, (kabupaten_scrape_id, kec['kode'], kec['nama'], kec['url']))
                        logging.info(f"  ✓ Inserted: {kec['nama']}")
                    
                    conn.commit()
                    success_count += 1
                
                except Exception as e:
                    logging.error(f"  ✗ Gagal menyimpan {kec['nama']}: {e}")
                    failed_count += 1
                    conn.rollback()
    
    except Exception as e:
        logging.error(f"✗ Error besar saat menyimpan kecamatan ke DB: {e}")
    finally:
        if conn:
            conn.close()
            
    return success_count, failed_count

# ----------------------------------------------------------
# 4. FUNGSI SCRAPING KECAMATAN (LOGIKA ADAPTASI)
# ----------------------------------------------------------
def scrape_kecamatan_from_kabupaten(driver, kab_data, max_retries=3):
    """
    Scrape daftar kecamatan dari satu halaman kabupaten.
    Logika ini diadaptasi penuh dari scrape_kabupaten().
    """
    url = kab_data['url']
    nama_kabupaten = kab_data['nama_kabupaten']
    logging.info(f"--- Memulai scraping untuk {nama_kabupaten} ---")
    
    for attempt in range(max_retries):
        try:
            logging.info(f"Attempt {attempt + 1}/{max_retries} untuk {nama_kabupaten}...")
            driver.get(url)
            
            # Menunggu tabel kecamatan muncul
            # Selector ini sesuai dengan HTML yang Anda berikan
            WebDriverWait(driver, 60).until(
                EC.presence_of_element_located((By.CSS_SELECTOR, "table#DataTables_Table_0 tr.data"))
            )
            
            soup = BeautifulSoup(driver.page_source, 'html.parser')
            table = soup.find('table', id='DataTables_Table_0')
            if not table: 
                raise Exception("Tabel DataTables_Table_0 tidak ditemukan")
            
            rows = table.find('tbody').find_all('tr', class_='data')
            if not rows: 
                raise Exception("Tidak ada 'tr.data' di dalam tabel")

            logging.info(f"Ditemukan {len(rows)} baris data (calon kecamatan)")
            
            kecamatan_list = []
            for row in rows:
                cells = row.find_all('td')
                if len(cells) < 2: 
                    continue # Baris tidak valid
                
                # Kolom 1: Nama Kecamatan & Link (Sama seperti skrip kabupaten)
                link_cell = cells[1] 
                link = link_cell.find('a', href=True)
                
                if link:
                    href_kotor = link.get('href', '')
                    nama = link.get_text(strip=True)
                    
                    # Bersihkan href dari spasi (sesuai HTML Anda: "/sp/3/151006  ")
                    href = href_kotor.strip() 
                    
                    if href and nama:
                        full_url = urljoin(BASE_URL, href)
                        
                        # Ekstrak kode 6 digit dari URL (Sama seperti skrip kabupaten)
                        # Ini adalah logika kunci yang hilang sebelumnya
                        kode_match = re.search(r'/(\d{6})', href)
                        kode = kode_match.group(1) if kode_match else ''
                        
                        if kode:
                            kecamatan_list.append({
                                'kode': kode,
                                'nama': nama,
                                'url': full_url
                            })
                            logging.info(f"  ✓ Found: {nama} (Kode: {kode})")
            
            if not kecamatan_list:
                raise Exception("tr.data ditemukan, tapi tidak ada link kecamatan yang valid")

            logging.info(f"✓ Berhasil scraping {len(kecamatan_list)} kecamatan dari {nama_kabupaten}")
            return kecamatan_list

        except Exception as e:
            logging.warning(f"⚠ Attempt {attempt + 1} gagal untuk {nama_kabupaten}: {e}")
            if attempt < max_retries - 1:
                time.sleep(5) # Jeda sebelum retry
            else:
                logging.error(f"✗ Gagal total scraping {nama_kabupaten} setelah {max_retries} attempts")
                return None # Return None jika gagal total
    return None

# ----------------------------------------------------------
# 5. PROSES UTAMA (BATCH LOOP)
# ----------------------------------------------------------
driver = None
total_kab_to_process = 0
total_kab_success = 0
total_kab_failed = 0
total_kec_saved = 0
error_message = None

try:
    # 1. Ambil daftar kabupaten untuk diproses
    kabupaten_list = get_kabupaten_to_process(args.kabupaten_id)
    total_kab_to_process = len(kabupaten_list)
    if total_kab_to_process == 0:
        raise Exception(f"Tidak ada kabupaten aktif ditemukan untuk IDs: {args.kabupaten_id}")
    
    # 2. Update log_id dengan total yg akan diproses
    # total_processed = jumlah kabupaten
    db_helper.finish_log_entry(log_id, 'running', total_kab_to_process, 0, 0, None)
    
    # 3. Setup Driver (Hanya 1x di awal)
    driver = driver_helper.setup_driver(driver_path=args.driver_path)
    if not driver:
        raise Exception("Gagal menginisialisasi Selenium Driver")

    # 4. Looping setiap kabupaten
    for kab_data in kabupaten_list:
        kecamatan_list = None
        try:
            # 5. Scrape
            kecamatan_list = scrape_kecamatan_from_kabupaten(driver, kab_data, args.max_retries)
            
            if kecamatan_list is None:
                raise Exception("Gagal scrape kecamatan, data tidak ditemukan (None)")
            
            # 6. Simpan
            logging.info(f"Menyimpan {len(kecamatan_list)} kecamatan untuk {kab_data['nama_kabupaten']}...")
            success_count, fail_count = save_kecamatan_to_db(
                kecamatan_list, 
                kab_data['id']
            )
            
            total_kec_saved += success_count
            total_kab_success += 1
            
        except Exception as e:
            logging.error(f"✗ Gagal memproses kabupaten {kab_data['nama_kabupaten']}: {e}")
            total_kab_failed += 1
            error_message = f"Gagal pada {kab_data['nama_kabupaten']}: {e}"
        
        # 7. Update log progres SETELAH SETIAP kabupaten (real-time)
        db_helper.finish_log_entry(
            log_id=log_id,
            status='running',
            total_processed=total_kab_to_process,
            total_success=total_kab_success, # total_success = jumlah kab yg sukses
            total_failed=total_kab_failed,   # total_failed = jumlah kab yg gagal
            error_message=error_message
        )

    # 8. Selesai (Setelah loop)
    final_status = 'completed' if total_kab_failed == 0 else 'failed'
    if total_kab_failed > 0:
        error_message = f"{total_kab_failed} dari {total_kab_to_process} kabupaten gagal diproses."

    db_helper.finish_log_entry(
        log_id=log_id,
        status=final_status,
        total_processed=total_kab_to_process,
        total_success=total_kab_success,
        total_failed=total_kab_failed,
        error_message=error_message
    )
    
    logging.info("=" * 80)
    logging.info("PROSES KECAMATAN SELESAI")
    logging.info(f"Total Kabupaten Diproses: {total_kab_to_process}")
    logging.info(f"Kabupaten Sukses: {total_kab_success}")
    logging.info(f"Kabupaten Gagal: {total_kab_failed}")
    logging.info(f"Total Kecamatan Disimpan: {total_kec_saved}") # Ini adalah total kecamatan
    logging.info("=" * 80)
    
except Exception as e:
    logging.error(f"✗✗✗ ERROR FATAL: {e}")
    # Catat error fatal di log
    db_helper.finish_log_entry(
        log_id=log_id,
        status='failed',
        total_processed=total_kab_to_process,
        total_success=total_kab_success,
        total_failed=total_kab_to_process - total_kab_success,
        error_message=str(e)
    )
    sys.exit(1)

finally:
    # Selalu tutup driver
    if driver:
        driver.quit()
        logging.info("✓ Driver Selenium ditutup")
    
    logging.info(">>> Script kecamatan selesai <<<")