#!/usr/bin/env python
# -*- coding: utf-8 -*-

"""
===============================================================
IMPORT URL SEKOLAH - VERSI REFACTOR (ROMBAK TOTAL v3 - Robust)
===============================================================
- Menggunakan db_helper.py dan driver_helper.py
- Menerima --kecamatan_id (banyak) dari import_handler.php
- Iterasi jenjang SPESIFIK (SD, SMP, SMA, SMK)
- Strategi WebDriverWait yang lebih fleksibel untuk tabel refresh
- Mencoba ID tabel #dataTables dan #DataTables_Table_0
- Menambahkan refresh halaman otomatis jika jenjang gagal total
- Fix: Import 'random'
===============================================================
"""

import sys
import os
import logging
import argparse
import time
import re
import random # <-- FIX: Import random
from urllib.parse import urljoin

# ----------------------------------------------------------
# 1. SETUP LOGGING & IMPORTS
# ----------------------------------------------------------
log_file = r'C:\laragon\www\dapokalsel\log_sekolah.txt'
logging.basicConfig(
    filename=log_file,
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s'
)

logging.info("=" * 80)
logging.info(">>> [ROMBAK TOTAL v3 - Robust] import_url_sekolah.pyw dimulai <<<")
logging.info(f"Arguments: {sys.argv}")

try:
    from bs4 import BeautifulSoup
    from selenium.webdriver.common.by import By
    from selenium.webdriver.support.ui import WebDriverWait, Select
    from selenium.webdriver.support import expected_conditions as EC
    from selenium.common.exceptions import TimeoutException, StaleElementReferenceException, NoSuchElementException
    
    # Impor helper terpusat
    import db_helper
    import driver_helper
    
    logging.info("✓ Library & Helper berhasil diimport")

except ImportError as e:
    logging.error(f"✗ Gagal import library: {e}")
    sys.exit(1)

# ----------------------------------------------------------
# 2. KONFIGURASI & ARGUMEN
# ----------------------------------------------------------
parser = argparse.ArgumentParser(description='Import URL Sekolah dari Dapodik per Kecamatan')
parser.add_argument('--kecamatan_id', type=str, required=True, help='Comma-separated kecamatan_scrape_ids')
parser.add_argument('--user_id', type=int, default=1, help='User ID')
parser.add_argument('--max_retries', type=int, default=3, help='Max retries per kecamatan')
parser.add_argument('--driver_path', type=str, required=True, help='Path absolut ke chromedriver.exe')
parser.add_argument('--log_id', type=int, required=True, help='ID dari import_log untuk diupdate')

args = parser.parse_args()
log_id = args.log_id 

logging.info(f"Parsed arguments:")
logging.info(f"  - kecamatan_id: {args.kecamatan_id}")
logging.info(f"  - driver_path: {args.driver_path}")
logging.info(f"  - log_id: {args.log_id}")

BASE_URL = "https://dapo.kemendikdasmen.go.id"
JENJANG_SEKOLAH = ['sd', 'smp', 'sma', 'smk'] 
ELEMENT_TIMEOUT = 45 # Naikkan timeout tunggu elemen
TABLE_REFRESH_TIMEOUT = 60 # Naikkan timeout tunggu refresh tabel

# ----------------------------------------------------------
# 3. FUNGSI HELPER DATABASE (Sama seperti sebelumnya)
# ----------------------------------------------------------
def get_kecamatan_to_process(kecamatan_ids_str):
    conn = db_helper.get_db_connection()
    # ... (kode fungsi ini sama seperti di versi sebelumnya) ...
    if not conn: return []
    try:
        with conn.cursor() as cursor:
            id_list = [int(id_str) for id_str in kecamatan_ids_str.split(',')]
            if not id_list: return []
            placeholders = ','.join(['%s'] * len(id_list))
            query = f"SELECT id, nama_kecamatan, url FROM kecamatan_scrape WHERE id IN ({placeholders}) AND status = 'active'" 
            cursor.execute(query, tuple(id_list))
            kecamatan_list = cursor.fetchall()
            logging.info(f"✓ Ditemukan {len(kecamatan_list)} kecamatan untuk diproses")
            return kecamatan_list
    except Exception as e:
        logging.error(f"✗ Gagal mengambil data kecamatan: {e}")
        return []
    finally:
        if conn: conn.close()

def save_sekolah_to_db(sekolah_data_batch, kecamatan_scrape_id):
    success_count = 0
    failed_count = 0
    conn = db_helper.get_db_connection()
    # ... (kode fungsi ini sama seperti di versi sebelumnya) ...
    if not conn:
        logging.error("✗ Gagal koneksi DB saat menyimpan sekolah.")
        return 0, len(sekolah_data_batch) 
    try:
        with conn.cursor() as cursor:
            for sek in sekolah_data_batch:
                try:
                    cursor.execute("SELECT id FROM sekolah_scrape WHERE kecamatan_scrape_id = %s AND npsn = %s", (kecamatan_scrape_id, sek['npsn']))
                    existing = cursor.fetchone()
                    if existing:
                        cursor.execute("UPDATE sekolah_scrape SET nama_sekolah = %s, url = %s, jenjang = %s, status = 'active', updated_at = NOW() WHERE id = %s", (sek['nama'], sek['url'], sek['jenjang'], existing['id']))
                        logging.info(f"    ↻ Updated: {sek['npsn']} - {sek['nama']}")
                    else:
                        cursor.execute("INSERT INTO sekolah_scrape (kecamatan_scrape_id, npsn, nama_sekolah, url, jenjang, status, created_at, updated_at) VALUES (%s, %s, %s, %s, %s, 'active', NOW(), NOW())", (kecamatan_scrape_id, sek['npsn'], sek['nama'], sek['url'], sek['jenjang']))
                        logging.info(f"    ✓ Inserted: {sek['npsn']} - {sek['nama']}")
                    conn.commit()
                    success_count += 1
                except Exception as e:
                    logging.error(f"    ✗ Gagal menyimpan {sek['npsn']}: {e}")
                    failed_count += 1
                    conn.rollback() 
    except Exception as e:
        logging.error(f"✗ Error besar saat menyimpan sekolah ke DB: {e}")
        failed_count = len(sekolah_data_batch) - success_count 
    finally:
        if conn: conn.close()
    return success_count, failed_count

# ----------------------------------------------------------
# 4. FUNGSI SCRAPING SEKOLAH (PER JENJANG - Lebih Robust)
# ----------------------------------------------------------
def extract_sekolah_from_table(driver, jenjang):
    """
    Mengekstrak data sekolah dari tabel yang sedang ditampilkan.
    Mencoba ID tabel #dataTables dan #DataTables_Table_0.
    """
    try:
        logging.info(f"    Mengekstrak data untuk jenjang: {jenjang}")
        
        # Tunggu salah satu elemen tabel atau pesan 'empty' muncul
        # Ini adalah bagian krusial untuk menunggu AJAX selesai
        logging.info(f"    Menunggu tabel {jenjang} muncul atau pesan 'empty'...")
        try:
            WebDriverWait(driver, TABLE_REFRESH_TIMEOUT).until(
                EC.any_of(
                    EC.presence_of_element_located((By.CSS_SELECTOR, "table#dataTables tbody tr")), 
                    EC.presence_of_element_located((By.CSS_SELECTOR, "table#DataTables_Table_0 tbody tr")), 
                    EC.presence_of_element_located((By.CSS_SELECTOR, "table#dataTables td.dataTables_empty")),
                    EC.presence_of_element_located((By.CSS_SELECTOR, "table#DataTables_Table_0 td.dataTables_empty")) 
                )
            )
            logging.info(f"    ✓ Elemen tabel/empty untuk {jenjang} ditemukan.")
        except TimeoutException:
             # Jika timeout di sini, kemungkinan besar halaman tidak merespon/error
             logging.error(f"    ✗ Timeout menunggu tabel/pesan empty untuk jenjang {jenjang} setelah {TABLE_REFRESH_TIMEOUT} detik.")
             return None # Kembalikan None untuk menandakan kegagalan ekstrak

        # Beri sedikit jeda agar JS selesai merender data sepenuhnya
        time.sleep(random.uniform(1.5, 3)) 

        soup = BeautifulSoup(driver.page_source, 'html.parser')
        
        # Coba cari tabel dengan kedua ID
        table = soup.find('table', id='dataTables')
        if not table:
            table = soup.find('table', id='DataTables_Table_0')
            
        if not table: 
            logging.warning(f"    Tabel (dataTables/DataTables_Table_0) tidak ditemukan setelah menunggu (Jenjang: {jenjang})")
            return [] # Kembalikan list kosong jika tabel tidak ada
        
        # Cek jika tabel eksplisit menyatakan kosong
        empty_cell = table.find('td', class_='dataTables_empty')
        if empty_cell:
            logging.info(f"    Tabel kosong untuk jenjang {jenjang}.")
            return []

        # Cari semua baris di tbody
        rows = table.find('tbody').find_all('tr')
        if not rows:
            logging.warning(f"    Tabel {jenjang} ditemukan tapi tidak ada baris <tr> di tbody.")
            return []
        
        logging.info(f"    Ditemukan {len(rows)} baris data sekolah untuk jenjang {jenjang}")
        
        sekolah_list = []
        processed_npsn = set() 

        for row in rows:
            cells = row.find_all('td')
            # Sesuai HTML baru: No(0), Nama+Link(1), NPSN(2), BP(3), Status(4) ...
            if len(cells) < 3: 
                logging.debug("    Baris tidak punya cukup kolom (minimal Nama, NPSN), dilewati.")
                continue 
            
            # Kolom 1: Nama & Link
            link_cell = cells[1]
            link = link_cell.find('a', href=True)
            
            # Kolom 2: NPSN
            npsn_cell = cells[2]
            npsn = npsn_cell.get_text(strip=True) if npsn_cell else None
            
            # Validasi dasar
            if link and npsn and npsn.isdigit() and npsn not in processed_npsn:
                nama = link.get_text(strip=True)
                href = link.get('href', '').strip()
                
                if href and nama:
                    full_url = urljoin(BASE_URL, href)
                    sekolah_list.append({
                        'npsn': npsn,
                        'nama': nama,
                        'url': full_url,
                        'jenjang': jenjang # Tambahkan info jenjang
                    })
                    processed_npsn.add(npsn) 
            else:
                 logging.debug(f"    Data baris tidak valid atau duplikat NPSN: {npsn}")
        
        logging.info(f"    ✓ Berhasil ekstrak {len(sekolah_list)} sekolah unik untuk jenjang {jenjang}")
        return sekolah_list # Kembalikan list (bisa kosong) jika ekstraksi berhasil
        
    except Exception as e:
        logging.error(f"    ✗ Gagal ekstrak data jenjang {jenjang}: {e}")
        import traceback
        logging.error(traceback.format_exc()) # Log traceback untuk debug
        return None # Kembalikan None jika ada error tak terduga saat ekstraksi


def scrape_sekolah_from_kecamatan(driver, kec_data, max_retries_per_kec=3):
    """
    Scrape semua jenjang sekolah (SD, SMP, SMA, SMK) dari satu halaman kecamatan.
    Lebih tahan banting terhadap error AJAX dan server down.
    """
    url = kec_data['url']
    nama_kecamatan = kec_data['nama_kecamatan']
    kecamatan_id = kec_data['id']
    logging.info(f"---> Memulai scraping untuk Kecamatan: {nama_kecamatan} (ID: {kecamatan_id}) <---")
    
    sekolah_ditemukan_di_kecamatan = [] 
    
    # Retry di level kecamatan jika terjadi error tak terduga
    for attempt in range(max_retries_per_kec):
        kecamatan_proses_berhasil_flag = True # Flag penanda sukses per attempt kecamatan
        try:
            logging.info(f"  Attempt {attempt + 1}/{max_retries_per_kec} untuk {nama_kecamatan}...")
            # Selalu buka URL di awal setiap attempt kecamatan
            driver.get(url) 
            
            # 1. Tunggu dropdown jenjang muncul dan bisa diklik
            logging.info("    Menunggu dropdown jenjang...")
            try:
                select_element = WebDriverWait(driver, ELEMENT_TIMEOUT).until(
                    EC.element_to_be_clickable((By.ID, "selectJenjang"))
                )
                logging.info("    ✓ Dropdown jenjang ditemukan.")
            except TimeoutException:
                 logging.error(f"    ✗ Timeout menunggu dropdown jenjang di {nama_kecamatan}. Attempt {attempt + 1} gagal.")
                 # Jika dropdown saja tidak muncul, anggap gagal untuk attempt ini
                 kecamatan_proses_berhasil_flag = False
                 continue # Lanjut ke attempt kecamatan berikutnya
            
            # Buat objek Select
            select_obj = Select(select_element)
            
            # 2. Iterasi melalui setiap jenjang SPESIFIK
            for jenjang in JENJANG_SEKOLAH:
                max_retries_per_jenjang = 3 # Coba 3x per jenjang
                jenjang_berhasil_diekstrak = False # Flag sukses per jenjang
                
                for jenjang_attempt in range(max_retries_per_jenjang):
                    try:
                        logging.info(f"    Memproses jenjang: {jenjang} (Attempt {jenjang_attempt + 1}/{max_retries_per_jenjang})")
                        
                        # --- Logika Pilih Jenjang dan Tunggu Refresh ---
                        
                        # Pastikan dropdown masih ada (jika halaman refresh/error)
                        try:
                           current_select_element = WebDriverWait(driver, 5).until(EC.element_to_be_clickable((By.ID, "selectJenjang")))
                           current_select_obj = Select(current_select_element)
                        except:
                             logging.warning(f"    Dropdown hilang saat retry jenjang {jenjang}, coba refresh...")
                             driver.refresh()
                             current_select_element = WebDriverWait(driver, ELEMENT_TIMEOUT).until(EC.element_to_be_clickable((By.ID, "selectJenjang")))
                             current_select_obj = Select(current_select_element)
                             logging.info("    Dropdown ditemukan setelah refresh.")
                        
                        # Pilih jenjang
                        current_select_obj.select_by_value(jenjang)
                        logging.info(f"    Jenjang '{jenjang}' dipilih.")
                        # Beri jeda singkat setelah klik
                        time.sleep(random.uniform(1, 2)) 
                             
                        # --- Ekstraksi Data ---
                        # Fungsi extract_sekolah_from_table sudah termasuk WebDriverWait di dalamnya
                        sekolah_data_jenjang = extract_sekolah_from_table(driver, jenjang)
                        
                        # Periksa hasil ekstraksi
                        if sekolah_data_jenjang is None:
                            # Jika None, berarti ada error saat ekstraksi/menunggu tabel
                            logging.warning(f"    Ekstraksi gagal (return None) untuk jenjang {jenjang}, attempt {jenjang_attempt + 1}.")
                            # Jangan break, coba lagi jenjang ini jika masih ada retry
                            if jenjang_attempt < max_retries_per_jenjang - 1:
                                logging.info(f"    Mencoba refresh sebelum retry jenjang {jenjang}...")
                                driver.refresh()
                                # Tunggu dropdown lagi setelah refresh
                                WebDriverWait(driver, ELEMENT_TIMEOUT).until(EC.element_to_be_clickable((By.ID, "selectJenjang")))
                                time.sleep(random.uniform(2, 4)) # Jeda setelah refresh
                            continue # Lanjut ke attempt jenjang berikutnya
                        else:
                            # Jika list (meski kosong), ekstraksi berhasil
                            sekolah_ditemukan_di_kecamatan.extend(sekolah_data_jenjang)
                            jenjang_berhasil_diekstrak = True
                            break # Berhasil untuk jenjang ini, keluar dari loop retry jenjang
                            
                    except StaleElementReferenceException:
                        logging.warning(f"    StaleElement saat memproses jenjang {jenjang}. Retry jenjang...")
                        # Tidak perlu refresh manual, loop retry akan re-find element
                        if jenjang_attempt < max_retries_per_jenjang - 1:
                           time.sleep(3) 
                        continue 
                           
                    except TimeoutException as te:
                         logging.warning(f"    Timeout ({type(te).__name__}) saat proses jenjang {jenjang}. Retry jenjang...")
                         # Timeout bisa terjadi saat re-find dropdown atau saat memilih
                         if jenjang_attempt < max_retries_per_jenjang - 1:
                             logging.info(f"    Mencoba refresh sebelum retry jenjang {jenjang}...")
                             driver.refresh()
                             WebDriverWait(driver, ELEMENT_TIMEOUT).until(EC.element_to_be_clickable((By.ID, "selectJenjang")))
                             time.sleep(random.uniform(2, 4))
                         continue # Lanjut ke attempt jenjang berikutnya
                         
                    except Exception as e_jenjang:
                        logging.error(f"    ✗ Error tak terduga pada jenjang {jenjang} (Attempt {jenjang_attempt + 1}): {e_jenjang}")
                        import traceback
                        logging.error(traceback.format_exc())
                        if jenjang_attempt < max_retries_per_jenjang - 1:
                           logging.info(f"    Mencoba refresh sebelum retry jenjang {jenjang}...")
                           try:
                               driver.refresh()
                               WebDriverWait(driver, ELEMENT_TIMEOUT).until(EC.element_to_be_clickable((By.ID, "selectJenjang")))
                               time.sleep(random.uniform(2, 4))
                           except Exception as refresh_err:
                                logging.error(f"    Gagal refresh/menemukan dropdown setelah error: {refresh_err}. Gagal total untuk kecamatan ini.")
                                kecamatan_proses_berhasil_flag = False
                                break # Keluar dari loop jenjang_attempt
                           continue 
                        else:
                            logging.error(f"    Gagal total untuk jenjang {jenjang} setelah {max_retries_per_jenjang} attempts.")
                            # Tidak set flag gagal kecamatan, hanya gagal jenjang ini
                            break # Keluar dari loop jenjang_attempt

                # Jika keluar dari loop retry jenjang dan flag 'kecamatan_proses_berhasil_flag' jadi False
                if not kecamatan_proses_berhasil_flag:
                    break # Keluar juga dari loop JENJANG_SEKOLAH utama
                
                # Jika jenjang tidak berhasil diekstrak setelah semua retry
                if not jenjang_berhasil_diekstrak:
                     logging.error(f"    Gagal memproses jenjang {jenjang} setelah {max_retries_per_jenjang} attempts. Lanjut ke jenjang berikutnya.")
                     # Tidak set flag gagal kecamatan, hanya catat error

            # Setelah loop semua jenjang selesai...
            # Jika flag 'kecamatan_proses_berhasil_flag' masih True, berarti attempt kecamatan ini sukses
            if kecamatan_proses_berhasil_flag:
                logging.info(f"---> Selesai scraping {nama_kecamatan} (Attempt {attempt+1}), total ditemukan {len(sekolah_ditemukan_di_kecamatan)} sekolah <---")
                return sekolah_ditemukan_di_kecamatan # SUKSES untuk kecamatan ini
            else:
                 # Jika ada error fatal di tengah loop jenjang
                 raise Exception(f"Proses gagal di tengah jalan (flag=False) untuk kecamatan {nama_kecamatan}.")

        except Exception as e_kec:
            # Tangkap error level kecamatan (misal: gagal load halaman awal, gagal total temukan dropdown)
            logging.warning(f"⚠ Error pada attempt {attempt + 1} kecamatan {nama_kecamatan}: {e_kec}")
            kecamatan_proses_berhasil_flag = False # Tandai attempt ini gagal
            
        # Jika loop attempt kecamatan sampai sini DAN flag masih false, retry kecamatan
        if not kecamatan_proses_berhasil_flag and attempt < max_retries_per_kec - 1:
            logging.info(f"    Retry kecamatan {nama_kecamatan}...")
            time.sleep(random.uniform(7, 12)) # Jeda lebih lama sebelum retry kecamatan
        elif not kecamatan_proses_berhasil_flag:
             logging.error(f"✗ Gagal total scraping {nama_kecamatan} setelah {max_retries_per_kec} attempts")
             return None # GAGAL TOTAL untuk kecamatan ini

    return None # Fallback jika loop attempt selesai tanpa return sukses

# ----------------------------------------------------------
# 5. PROSES UTAMA (BATCH LOOP KECAMATAN)
# ----------------------------------------------------------
driver = None
total_kec_to_process = 0
total_kec_success = 0
total_kec_failed = 0
total_sek_saved_overall = 0 
error_message_summary = "" 

try:
    # 1. Ambil daftar kecamatan untuk diproses
    kecamatan_list = get_kecamatan_to_process(args.kecamatan_id)
    total_kec_to_process = len(kecamatan_list)
    if total_kec_to_process == 0:
        raise Exception(f"Tidak ada kecamatan aktif ditemukan untuk IDs: {args.kecamatan_id}")
    
    # 2. Update log_id dengan total yg akan diproses
    db_helper.finish_log_entry(log_id, 'running', total_kec_to_process, 0, 0, None)
    
    # 3. Setup Driver (Hanya 1x di awal)
    driver = driver_helper.setup_driver(driver_path=args.driver_path)
    if not driver:
        raise Exception("Gagal menginisialisasi Selenium Driver")

    # 4. Looping setiap kecamatan
    for index, kec_data in enumerate(kecamatan_list):
        sekolah_list_all_jenjang = None 
        kec_success_flag = False 
        
        logging.info(f"\n===== Memproses Kecamatan {index + 1}/{total_kec_to_process}: {kec_data['nama_kecamatan']} =====")
        
        try:
            # 5. Scrape semua jenjang untuk kecamatan ini
            sekolah_list_all_jenjang = scrape_sekolah_from_kecamatan(driver, kec_data, args.max_retries)
            
            # Periksa hasil scraping
            if sekolah_list_all_jenjang is None:
                raise Exception(f"Gagal total scrape sekolah") # Error sudah dilog di dalam fungsi
                
            elif not sekolah_list_all_jenjang:
                 logging.info(f"==> Tidak ada sekolah ditemukan (scrape sukses).")
                 kec_success_flag = True 
                 
            else:
                logging.info(f"==> Ditemukan {len(sekolah_list_all_jenjang)} sekolah. Menyimpan ke DB...")
                # 6. Simpan batch sekolah ke DB
                success_count, fail_count = save_sekolah_to_db(
                    sekolah_list_all_jenjang, 
                    kec_data['id']
                )
                
                total_sek_saved_overall += success_count
                
                if fail_count > 0:
                     logging.warning(f"    Gagal menyimpan {fail_count} sekolah.")
                     kec_success_flag = True # Anggap sukses scrape, tapi catat error
                     error_message_summary += f"Gagal simpan {fail_count} di {kec_data['nama_kecamatan'][:20]}.. . " # Potong nama
                else:
                    kec_success_flag = True # Sukses penuh
            
        except Exception as e:
            logging.error(f"✗ Gagal total memproses kecamatan {kec_data['nama_kecamatan']}: {e}")
            kec_success_flag = False 
            error_message_summary += f"Gagal proses {kec_data['nama_kecamatan'][:20]}..: {str(e)[:50]}... . " # Potong pesan error juga
            
        # Update counter sukses/gagal berdasarkan flag
        if kec_success_flag:
            total_kec_success += 1
        else:
            total_kec_failed += 1
            
        # 7. Update log progres SETELAH SETIAP kecamatan
        db_helper.finish_log_entry(
            log_id=log_id,
            status='running', # Status tetap running selama loop
            total_processed=total_kec_to_process,
            total_success=total_kec_success, 
            total_failed=total_kec_failed,   
            error_message=error_message_summary.strip() if error_message_summary else None 
        )
        
        # Jeda antar kecamatan
        logging.info(f"----- Jeda sebelum kecamatan berikutnya -----")
        time.sleep(random.uniform(3, 6)) 

    # 8. Selesai (Setelah loop semua kecamatan)
    final_status = 'completed' if total_kec_failed == 0 else 'failed'
    final_error_message = None
    if total_kec_failed > 0:
        final_error_message = f"{total_kec_failed}/{total_kec_to_process} kecamatan gagal."
        if error_message_summary: final_error_message += f" Detail: {error_message_summary.strip()}"
    elif error_message_summary: 
         final_error_message = f"Peringatan Simpan: {error_message_summary.strip()}"
         final_status = 'completed' 

    db_helper.finish_log_entry(
        log_id=log_id,
        status=final_status,
        total_processed=total_kec_to_process,
        total_success=total_kec_success,
        total_failed=total_kec_failed,
        error_message=final_error_message 
    )
    
    logging.info("=" * 80)
    logging.info("PROSES SEKOLAH SELESAI")
    logging.info(f"Total Kecamatan Diproses: {total_kec_to_process}")
    logging.info(f"Kecamatan Sukses: {total_kec_success}")
    logging.info(f"Kecamatan Gagal: {total_kec_failed}")
    logging.info(f"Total Sekolah Disimpan/Update: {total_sek_saved_overall}") 
    logging.info("=" * 80)
    
except Exception as e:
    logging.error(f"✗✗✗ ERROR FATAL (Level Utama): {e}")
    import traceback
    logging.error(traceback.format_exc())
    # Catat error fatal di log
    db_helper.finish_log_entry(
        log_id=log_id,
        status='failed',
        total_processed=total_kec_to_process, 
        total_success=total_kec_success,
        total_failed=total_kec_to_process - total_kec_success, 
        error_message=f"FATAL ERROR: {str(e)}"
    )
    sys.exit(1) # Keluar dengan status error

finally:
    # Selalu tutup driver jika sudah dibuat
    if driver:
        try:
            driver.quit()
            logging.info("✓ Driver Selenium ditutup")
        except Exception as quit_err:
             logging.error(f"Error saat menutup driver: {quit_err}")
    
    logging.info(">>> Script sekolah selesai <<<")