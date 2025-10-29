#!/usr/bin/env python
# -*- coding: utf-8 -*-

"""
===============================================================
MAIN SCRAPER - VERSI REFACTOR (Konsisten v6 - Full Process)
===============================================================
- Struktur konsisten dengan skrip import (db_helper, driver_helper).
- Menggunakan helper dari subfolder 'sekolah'.
- Menerima --log_id, --user_id, --driver_path dari PHP.
- ✅ FIX: Menambahkan import 'datetime' & 'random'
- ✅ FIX: Menambahkan project root ke sys.path
- Menggunakan Selenium (driver_helper) untuk navigasi.
- Menggunakan requests HANYA untuk download file Excel.
- Proses sekuensial.
- ✅ FITUR: Menambahkan konversi Excel ke JSON (menggunakan pandas).
- ✅ FITUR: Menambahkan import data dari JSON ke tabel database utama.
- ✅ FITUR: Menambahkan fungsi helper dari referensi (extract_*, get_or_create_id, dll.)
- ✅ FIX: Logging terpusat ke scraping_logs via db_helper.update_scraping_log
- Error handling & retry ditingkatkan (default 300 per URL).
- Optimasi kecepatan minor.
===============================================================
"""

import sys
import os
import logging
import argparse
import time
import re
import json
import random
import traceback
import requests # Hanya untuk download file
import pandas as pd # Untuk membaca Excel
from urllib.parse import urljoin
from datetime import datetime 

# --- FIX PATH IMPORT ---
project_root = os.path.dirname(os.path.abspath(__file__))
if project_root not in sys.path:
    sys.path.insert(0, project_root)
# --- AKHIR FIX PATH ---

# ----------------------------------------------------------
# 1. SETUP LOGGING & IMPORTS
# ----------------------------------------------------------
log_file = r'C:\laragon\www\dapokalsel\log_main_scraper.txt'
logging.basicConfig(
    filename=log_file,
    level=logging.INFO,
    format='%(asctime)s [%(levelname)s] %(name)s: %(message)s'
)

logger = logging.getLogger(__name__) 

logging.info("=" * 80)
logging.info(">>> [REFACTOR v6 - Full Process] main_scraper.pyw dimulai <<<")
logging.info(f"Arguments: {sys.argv}")
logging.info(f"Python sys.path includes: {project_root}")
logging.info(f"Log file: {log_file}")

try:
    from bs4 import BeautifulSoup
    from selenium.webdriver.common.by import By
    from selenium.webdriver.support.ui import WebDriverWait
    from selenium.webdriver.support import expected_conditions as EC
    from selenium.common.exceptions import TimeoutException, NoSuchElementException

    # Impor helper dari subfolder 'sekolah'
    from sekolah import db_helper
    from sekolah import driver_helper

    logging.info("✓ Library & Helper berhasil diimport dari subfolder 'sekolah'")

except ImportError as e:
    logger.error(f"✗ Gagal import library/helper: {e}")
    if 'db_helper' in str(e) or 'driver_helper' in str(e):
        logger.error("Pastikan file db_helper.py dan driver_helper.py ada di dalam folder 'sekolah'.")
        logger.error("Pastikan juga ada file __init__.py (kosong) di dalam folder 'sekolah'.")
    if 'pandas' in str(e):
         logger.error("Pastikan library pandas terinstal: pip install pandas")
    if 'openpyxl' in str(e):
         logger.error("Pastikan library openpyxl terinstal: pip install openpyxl")
    if 'xlrd' in str(e):
         logger.error("Pastikan library xlrd terinstal: pip install xlrd")
    sys.exit(1)
except Exception as e:
    logger.error(f"✗ Error saat import: {e}")
    traceback.print_exc()
    sys.exit(1)

# ----------------------------------------------------------
# 2. KONFIGURASI & ARGUMEN
# ----------------------------------------------------------
parser = argparse.ArgumentParser(description='Scrape detail sekolah dari Dapodik & Sekolah Kita')
parser.add_argument('--log_id', type=int, required=True, help='ID log proses dari scraping_logs')
parser.add_argument('--user_id', type=int, required=True, help='User ID')
parser.add_argument('--driver_path', type=str, required=True, help='Path absolut ke chromedriver.exe')
parser.add_argument('--max_retries_per_url', type=int, default=300, help='Max retries per URL sekolah') # Default 300

args = parser.parse_args()
log_id = args.log_id

logger.info(f"Parsed arguments:")
logger.info(f"  - log_id: {args.log_id}")
logger.info(f"  - user_id: {args.user_id}")
logger.info(f"  - driver_path: {args.driver_path}")
logger.info(f"  - max_retries_per_url: {args.max_retries_per_url}")

DAPODIK_BASE_URL = "https://dapo.kemendikdasmen.go.id"
ELEMENT_TIMEOUT = 45
REQUEST_DELAY = random.uniform(2, 5)
RETRY_DELAY = random.uniform(7, 15)
DOWNLOAD_FOLDER = "downloads_main"
should_stop = False
last_error_message = "Proses dimulai..." # Global error message

# ----------------------------------------------------------
# 3. FUNGSI HELPER (Termasuk fungsi dari referensi)
# ----------------------------------------------------------

def get_urls_to_process(log_id_from_arg):
    """Mengambil daftar URL (id, url, description) dari scraping_urls berdasarkan log_id."""
    url_list_tuples = []
    conn = db_helper.get_db_connection()
    if not conn:
        logger.error("✗ get_urls_to_process: Gagal koneksi DB.")
        return [], 0
    try:
        with conn.cursor() as cursor:
            cursor.execute("SELECT url_ids, total_urls FROM scraping_logs WHERE id = %s", (log_id_from_arg,))
            log_data = cursor.fetchone()
            if not log_data or not log_data.get('url_ids'):
                logger.warning(f"⚠ get_urls_to_process: Tidak ada url_ids di log_id {log_id_from_arg}")
                return [], 0
            
            total_urls_in_log = int(log_data.get('total_urls', 0))
            
            try:
                url_ids_list = json.loads(log_data['url_ids'])
                url_ids_list = [int(id_val) for id_val in url_ids_list]
            except (json.JSONDecodeError, TypeError, ValueError) as e:
                logger.error(f"✗ get_urls_to_process: Gagal decode url_ids JSON log {log_id_from_arg}: {e}")
                return [], total_urls_in_log
            
            if not url_ids_list:
                logger.warning(f"⚠ get_urls_to_process: Daftar url_ids kosong di log_id {log_id_from_arg}")
                return [], total_urls_in_log
                
            placeholders = ','.join(['%s'] * len(url_ids_list))
            query = f"SELECT id, url, description FROM scraping_urls WHERE id IN ({placeholders}) AND status = 'active'"
            cursor.execute(query, tuple(url_ids_list))
            results = cursor.fetchall()
            url_list_tuples = [(res['id'], res['url'], res.get('description')) for res in results]
            
            logging.info(f"✓ get_urls_to_process: Ditemukan {len(url_list_tuples)} URL aktif dari log.")
            return url_list_tuples, total_urls_in_log
    except Exception as e:
        logger.error(f"✗ get_urls_to_process: Gagal mengambil daftar URL: {e}")
        traceback.print_exc()
        return [], 0
    finally:
        if conn: conn.close()

def create_batch_folder_and_get_path():
    """Membuat folder batch unik berdasarkan tanggal dan waktu."""
    now = datetime.now()
    folder_name = now.strftime("%Y-%m-%d_%H-%M-%S")
    base_dir = os.path.dirname(os.path.abspath(__file__))
    main_download_dir = os.path.join(base_dir, DOWNLOAD_FOLDER)
    batch_dir = os.path.join(main_download_dir, folder_name)
    try:
        os.makedirs(batch_dir, exist_ok=True)
        logger.info(f"Folder batch: {batch_dir}")
        return batch_dir
    except OSError as e:
        logger.error(f"✗ Gagal membuat folder batch {batch_dir}: {e}")
        return None

def validate_and_clean_filename(filename):
    """Membersihkan nama file dari karakter ilegal."""
    if not filename: return "tanpa_nama"
    cleaned = re.sub(r'[<>:"/\\|?*\x00-\x1F]', '', filename)
    cleaned = re.sub(r'\s+', ' ', cleaned).strip()
    max_len = 200
    if len(cleaned) > max_len: cleaned = cleaned[:max_len].strip()
    return cleaned if cleaned else "tanpa_nama"

def download_file(download_url, save_path):
    """Mengunduh file menggunakan requests."""
    response_ref = {} 
    try:
        with requests.Session() as s:
            s.headers.update({'User-Agent': random.choice(driver_helper.USER_AGENTS)})
            response = s.get(download_url, stream=True, timeout=180)
            response_ref['response'] = response 
            response.raise_for_status()
            with open(save_path, 'wb') as f:
                for chunk in response.iter_content(chunk_size=65536):
                    if chunk: f.write(chunk)
            logger.info(f"      ✓ Excel diunduh: {os.path.basename(save_path)}")
            return True
    except requests.exceptions.RequestException as e:
        status_code = response_ref.get('response').status_code if response_ref.get('response') is not None else 'N/A'
        logger.error(f"      ✗ Gagal unduh file (Status: {status_code}) dari {download_url}: {e}")
        if os.path.exists(save_path): try: os.remove(save_path) except OSError: pass
        return False
    except Exception as e:
        logger.error(f"      ✗ Error unduh {download_url}: {e}")
        if os.path.exists(save_path): try: os.remove(save_path) except OSError: pass
        return False

def extract_sekolah_kita_data_robust(html_content):
    """Extract data dari halaman Sekolah Kita (HTML dari referensi)."""
    data = {'kepala_sekolah': None, 'operator': None, 'lintang': None, 'bujur': None}
    if not html_content: return data
    try:
        soup = BeautifulSoup(html_content, 'lxml')
        
        # Pola 1: Kepala Sekolah
        kepsek_item = soup.find(string=re.compile(r'Kepala Sekolah', re.I))
        if kepsek_item:
             # Coba cari di parent <li> atau <a>
             parent_li = kepsek_item.find_parent('li')
             if parent_li:
                 strong_tag = parent_li.find('strong') or parent_li.find('b')
                 if strong_tag and strong_tag.next_sibling:
                     data['kepala_sekolah'] = str(strong_tag.next_sibling).strip().strip(':').strip()
                 elif parent_li.find_next_sibling(string=True):
                     data['kepala_sekolah'] = str(parent_li.find_next_sibling(string=True)).strip().strip(':').strip()
                 elif ":" in parent_li.get_text():
                     data['kepala_sekolah'] = parent_li.get_text().split(':', 1)[-1].strip()
        
        # Pola 2: Operator
        op_item = soup.find(string=re.compile(r'Operator', re.I))
        if op_item:
            parent_li = op_item.find_parent('li')
            if parent_li:
                op_link = parent_li.find('a')
                if op_link: 
                    data['operator'] = op_link.get_text(strip=True)
                elif parent_li.find_next_sibling(string=True): 
                    data['operator'] = str(parent_li.find_next_sibling(string=True)).strip().strip(':').strip()
                elif ":" in parent_li.get_text(): 
                    data['operator'] = parent_li.get_text().split(':', 1)[-1].strip()

        # Pola 3: Koordinat
        scripts = soup.find_all('script', string=re.compile(r'L\.marker|L\.latLng|google\.maps\.LatLng'))
        for script in scripts:
            if not script.string: continue
            script_text = script.string
            patterns = [
                r'L\.marker\(\s*\[\s*(-?\d+\.?\d*)\s*,\s*(-?\d+\.?\d*)\s*\]', 
                r'L\.latLng\(\s*(-?\d+\.?\d*)\s*,\s*(-?\d+\.?\d*)\s*\)',     
                r'google\.maps\.LatLng\(\s*(-?\d+\.?\d*)\s*,\s*(-?\d+\.?\d*)\s*\)',
                r'var marker = L\.marker\(L\.latLng\(([^,]+),([^\)]+)\)' 
            ]
            for pattern in patterns:
                match = re.search(pattern, script_text)
                if match:
                    try:
                        lat_str = match.group(1).strip().replace("'", "").replace('"', '')
                        lon_str = match.group(2).strip().replace("'", "").replace('"', '')
                        lat = float(lat_str)
                        lon = float(lon_str)
                        data['lintang'] = str(lat)
                        data['bujur'] = str(lon)
                        logging.info(f"      ✓ Koordinat ditemukan: {data['lintang']}, {data['bujur']}")
                        return data 
                    except (ValueError, TypeError):
                         logging.warning(f"      ⚠ Format koordinat tidak valid: {match.group(1)}, {match.group(2)}")
                         continue 
    except Exception as e:
        logging.warning(f"      ⚠ Gagal parsing data Sekolah Kita: {e}")
    return data

def hapus_file_sementara(*file_paths):
    """Menghapus file-file sementara."""
    logging.info("      Memulai cleanup file sementara...")
    for file_path in file_paths:
         if file_path and os.path.exists(file_path):
             try:
                 os.remove(file_path)
                 logging.info(f"      ✓ File dihapus: {os.path.basename(file_path)}")
             except OSError as e:
                 logging.warning(f"      ⚠ Gagal hapus file {os.path.basename(file_path)}: {e}")
         else:
             logging.debug(f"      File tidak ditemukan untuk dihapus (mungkin gagal dibuat): {file_path}")


# --- Fungsi Helper dari Referensi (Konversi & Import DB) ---

def convert_excel_to_json(excel_path):
    """Konversi satu file Excel ke struktur JSON per sheet."""
    logging.info(f"    Memulai konversi Excel: {os.path.basename(excel_path)}")
    dict_of_sheets = None
    try:
        dict_of_sheets = pd.read_excel(excel_path, sheet_name=None, header=None, engine='openpyxl')
        logging.info(f"      ✓ Excel dibaca (openpyxl) (sheets: {list(dict_of_sheets.keys())})")
    except Exception as e_openpyxl:
        logging.warning(f"      ⚠ Gagal baca dengan openpyxl: {e_openpyxl}. Mencoba xlrd...")
        try:
             dict_of_sheets = pd.read_excel(excel_path, sheet_name=None, header=None, engine='xlrd')
             logging.info(f"      ✓ Excel dibaca (xlrd) (sheets: {list(dict_of_sheets.keys())})")
        except Exception as e_xlrd:
            logging.error(f"      ✗ Gagal membaca file Excel {os.path.basename(excel_path)} dengan kedua engine: {e_xlrd}")
            return None 
    if not dict_of_sheets: return None

    json_data_per_sheet = {}
    try:
        for sheet_name, df in dict_of_sheets.items():
            df_cleaned = df.where(pd.notnull(df), None) 
            sheet_records = []
            for _, row in df_cleaned.iterrows():
                 row_dict = {f"col_{i}": val for i, val in enumerate(row)}
                 sheet_records.append(row_dict)
            json_data_per_sheet[sheet_name] = sheet_records
        logging.info("      ✓ Konversi ke struktur JSON selesai.")
        return json_data_per_sheet
    except Exception as e_convert:
        logging.error(f"      ✗ Error saat konversi DataFrame ke JSON: {e_convert}")
        return None

def clean_text(text):
    if text is None: return None
    if isinstance(text, float) and pd.isna(text): return None
    text_str = str(text).strip().strip(':').strip()
    return text_str if text_str and text_str.lower() not in ['-', 'none', '', 'nan', 'null'] else None

def to_int(value):
    try:
        if value is None or (isinstance(value, float) and pd.isna(value)): return None
        clean_value = re.sub(r'[^\d.-]', '', str(value))
        return int(float(clean_value)) if clean_value else None
    except (ValueError, TypeError): return None

def to_date(value):
    try:
        if value is None or (isinstance(value, float) and pd.isna(value)): return None
        if isinstance(value, (int, float)):
             dt = pd.to_datetime(value, unit='D', origin='1899-12-30') 
        else:
             dt = pd.to_datetime(str(value), errors='coerce')
        return dt.strftime('%Y-%m-%d') if pd.notna(dt) else None
    except Exception: return None

# Mapping Kolom (Sama)
MAP_IDENTITAS = {
    'npsn': ('npsn', str), 'nama sekolah': ('nama_sekolah', str),
    'jenjang pendidikan': ('jenjang_pendidikan', str), 'status sekolah': ('status_sekolah', str),
    'alamat sekolah': ('alamat_jalan', str), 'kode pos': ('kode_pos', str),
    'kelurahan': ('kelurahan', str), 'kecamatan': ('kecamatan', str),
    'kabupaten/kota': ('kabupaten_kota', str), 'provinsi': ('provinsi', str)
}
MAP_PELENGKAP = {
    'sk pendirian sekolah': ('sk_pendirian', str), 'tanggal sk pendirian': ('tgl_sk_pendirian', to_date),
    'status kepemilikan': ('status_kepemilikan', str), 'sk izin operasional': ('sk_izin_operasional', str),
    'tgl sk izin operasional': ('tgl_sk_izin_operasional', to_date),
    'kebutuhan khusus dilayani': ('kebutuhan_khusus_dilayani', str),
    'nomor rekening': ('nomor_rekening', str), 'nama bank': ('nama_bank', str),
    'cabang kcp/unit': ('cabang_kcp_unit', str), 'rekening atas nama': ('rekening_atas_nama', str),
    'mbs': ('mbs', str), 'luas tanah milik (m2)': ('luas_tanah_milik_m2', to_int),
    'luas tanah bukan milik (m2)': ('luas_tanah_bukan_milik_m2', to_int),
    'nama wajib pajak': ('nama_wajib_pajak', str), 'npwp': ('npwp', str)
}
MAP_KONTAK = {
    'nomor telepon': ('nomor_telepon', str), 'nomor fax': ('nomor_fax', str),
    'email': ('email', str), 'website': ('website', str)
}
MAP_LAINNYA = {
    'akreditasi': ('akreditasi', str), 'kurikulum': ('kurikulum', str)
}

def extract_profil_data(sheet_data):
    all_data = {'identitas': {}, 'pelengkap': {}, 'kontak': {}, 'lainnya': {}}
    mappings = {'identitas': MAP_IDENTITAS, 'pelengkap': MAP_PELENGKAP, 'kontak': MAP_KONTAK, 'lainnya': MAP_LAINNYA}
    for row in sheet_data:
        label = clean_text(row.get('col_1'))
        if not label: continue
        label_lower = label.lower()
        if 'rt / rw' == label_lower:
            all_data['identitas']['rt'] = clean_text(row.get('col_3'))
            all_data['identitas']['rw'] = clean_text(row.get('col_5'))
            continue
        if 'posisi geografis' == label_lower: continue
        for category, mapping in mappings.items():
            if label_lower in mapping:
                db_column, type_converter = mapping[label_lower]
                value = clean_text(row.get('col_3')) or clean_text(row.get('col_2')) 
                if db_column in ['kecamatan', 'kabupaten_kota', 'provinsi'] and value:
                    value = re.sub(r'^(Kec\.|Kab\.|Prov\.)\s*', '', value, flags=re.IGNORECASE).strip()
                converted_value = type_converter(value) if callable(type_converter) else (value if type_converter == str else None)
                all_data[category][db_column] = converted_value
                break
    return all_data

def extract_rekap_data(sheet_data):
    rekap_data = {'ptk_pd': [], 'sarpras': [], 'rombel': []}
    def find_section_start(header_text_parts):
        for i, row in enumerate(sheet_data):
            cell_text = str(row.get('col_0', '') or row.get('col_1', '')).lower() 
            if all(part.lower() in cell_text for part in header_text_parts): return i
        return -1
    start_idx_ptk = find_section_start(['data ptk', 'pd'])
    if start_idx_ptk != -1:
        logging.debug("      Mengekstrak Rekap PTK/PD...")
        for row in sheet_data[start_idx_ptk + 2:]: 
            uraian = clean_text(row.get('col_1'))
            if not uraian or 'total' in uraian.lower() or 'keterangan' in uraian.lower(): break 
            if uraian in ['Laki-laki', 'Perempuan', 'Laki - Laki']: 
                rekap_data['ptk_pd'].append({
                    'deskripsi': uraian.replace(' - ', ''), 
                    'guru': to_int(row.get('col_2')), 'tendik': to_int(row.get('col_3')), 
                    'ptk_total': to_int(row.get('col_4')), 'pd_total': to_int(row.get('col_5')) })
    start_idx_sarpras = find_section_start(['data sarpras'])
    if start_idx_sarpras != -1:
         logging.debug("      Mengekstrak Rekap Sarpras...")
         header_row_index = start_idx_sarpras + 1
         # Cek jika baris berikutnya adalah header semester (berdasarkan HTML)
         if 'semester' in str(sheet_data[header_row_index].get('col_2','')).lower():
             header_row_index += 1 # Lewati baris header semester
         for row in sheet_data[header_row_index + 1:]:
            uraian = clean_text(row.get('col_1')) 
            if not uraian or 'total' in uraian.lower(): break
            # Ambil data dari kolom semester terbaru (asumsi col_3)
            rekap_data['sarpras'].append({ 'sarana': uraian, 'jumlah': to_int(row.get('col_3')) }) 
    start_idx_rombel = find_section_start(['rombongan belajar', 'rombel'])
    if start_idx_rombel != -1:
        logging.debug("      Mengekstrak Rekap Rombel...")
        current_kelas = None
        for i, row in enumerate(sheet_data[start_idx_rombel + 2:]): 
            kelas = clean_text(row.get('col_0')) or clean_text(row.get('col_1'))
            jenis = clean_text(row.get('col_1')) or clean_text(row.get('col_2'))
            if kelas: current_kelas = kelas 
            if not current_kelas: continue 
            if jenis == 'L' and (start_idx_rombel + 2 + i + 1) < len(sheet_data):
                next_row_index = start_idx_rombel + 2 + i + 1
                next_row = sheet_data[next_row_index]
                if clean_text(next_row.get('col_1')) == 'P' or clean_text(next_row.get('col_2')) == 'P':
                    rekap_data['rombel'].append({
                        'tingkat_kelas': current_kelas,
                        'jumlah_laki_laki': to_int(row.get('col_3')), 
                        'jumlah_perempuan': to_int(next_row.get('col_3')), 
                        'jumlah_total': to_int(row.get('col_4')) 
                    })
            elif not jenis and not kelas and not row.get('col_3'): break 
    return rekap_data

def get_indonesia_id(cursor):
    try:
        cursor.execute("SELECT id_negara FROM negara WHERE nama_negara = %s LIMIT 1", ('Indonesia',))
        result = cursor.fetchone()
        if result: return result['id_negara']
        else: 
             logging.warning("      Data 'Indonesia' tidak ada, membuat baru...")
             cursor.execute("INSERT INTO negara (nama_negara) VALUES (%s)", ('Indonesia',))
             return cursor.lastrowid
    except Exception as e:
        logging.error(f"      ✗ Error get_indonesia_id: {e}")
        raise 

def get_or_create_id(cursor, table, column, value, parent_fk_col=None, parent_fk_val=None):
    if value is None: return None
    try:
        # Sesuaikan nama ID kolom (id_provinsi, id_kabupaten, id_kecamatan)
        id_col_name = f"id_{table.replace('_kota', '')}" 
        
        query = f"SELECT `{id_col_name}` FROM `{table}` WHERE `{column}` = %s"
        params = [value]
        if parent_fk_col and parent_fk_val is not None:
            query += f" AND `{parent_fk_col}` = %s"
            params.append(parent_fk_val)
        query += " LIMIT 1"
        
        cursor.execute(query, tuple(params))
        result = cursor.fetchone()
        
        if result: return result[id_col_name] 
        
        # Insert jika tidak ada
        cols = f"(`{column}`"
        vals_placeholder = "(%s"
        insert_params = [value]
        if parent_fk_col and parent_fk_val is not None:
            cols += f", `{parent_fk_col}`)"
            vals_placeholder += ", %s)"
            insert_params.append(parent_fk_val)
        else:
            cols += ")"
            vals_placeholder += ")"
            
        insert_query = f"INSERT INTO `{table}` {cols} VALUES {vals_placeholder}"
        cursor.execute(insert_query, tuple(insert_params))
        return cursor.lastrowid 
    except Exception as e:
         logging.error(f"      ✗ Error get_or_create_id ({table}, {column}, {value}): {e}")
         raise

def insert_or_update_data(cursor, table_name, npsn, data_dict):
    valid_data = {k: v for k, v in data_dict.items() if v is not None}
    if not valid_data: return 
    valid_data['npsn_fk'] = npsn 
    try:
        cursor.execute(f"SELECT npsn_fk FROM `{table_name}` WHERE npsn_fk = %s", (npsn,))
        exists = cursor.fetchone()
        cols = list(valid_data.keys())
        placeholders = ', '.join(['%s'] * len(cols))
        values = list(valid_data.values())
        if exists:
            update_clause = ', '.join([f"`{col}` = %s" for col in cols if col != 'npsn_fk'])
            if update_clause: 
                sql = f"UPDATE `{table_name}` SET {update_clause} WHERE npsn_fk = %s"
                values_update = [v for k, v in valid_data.items() if k != 'npsn_fk']
                values_update.append(npsn) 
                cursor.execute(sql, tuple(values_update))
                logging.debug(f"      Updated {table_name} for {npsn}")
        else:
            sql = f"INSERT INTO `{table_name}` (`{'`, `'.join(cols)}`) VALUES ({placeholders})"
            cursor.execute(sql, tuple(values))
            logging.debug(f"      Inserted into {table_name} for {npsn}")
    except Exception as e:
         logging.error(f"      ✗ Error insert_or_update_data ({table_name}, {npsn}): {e}")
         raise

def insert_rekap_data(cursor, npsn, rekap_data):
    try:
        cursor.execute("DELETE FROM rekap_ptk_pd WHERE npsn_fk = %s", (npsn,))
        cursor.execute("DELETE FROM rekap_sarpras WHERE npsn_fk = %s", (npsn,))
        cursor.execute("DELETE FROM rekap_rombel WHERE npsn_fk = %s", (npsn,))
        ptk_pd_list = []
        for ptk_pd in rekap_data.get('ptk_pd', []):
            if ptk_pd.get('deskripsi'): 
                ptk_pd_list.append((npsn, ptk_pd['deskripsi'], ptk_pd.get('guru'), ptk_pd.get('tendik'), ptk_pd.get('ptk_total'), ptk_pd.get('pd_total')))
        if ptk_pd_list:
             cursor.executemany("INSERT INTO rekap_ptk_pd (npsn_fk, deskripsi, guru, tendik, ptk_total, pd_total) VALUES (%s, %s, %s, %s, %s, %s)", ptk_pd_list)
        sarpras_list = []
        for sarpras in rekap_data.get('sarpras', []):
            if sarpras.get('sarana'): 
                 sarpras_list.append((npsn, sarpras['sarana'], sarpras.get('jumlah')))
        if sarpras_list:
             cursor.executemany("INSERT INTO rekap_sarpras (npsn_fk, sarana, jumlah) VALUES (%s, %s, %s)", sarpras_list)
        rombel_list = []
        for rombel in rekap_data.get('rombel', []):
             if rombel.get('tingkat_kelas'):
                 rombel_list.append((npsn, rombel['tingkat_kelas'], rombel.get('jumlah_laki_laki'), rombel.get('jumlah_perempuan'), rombel.get('jumlah_total')))
        if rombel_list:
             cursor.executemany("INSERT INTO rekap_rombel (npsn_fk, tingkat_kelas, jumlah_laki_laki, jumlah_perempuan, jumlah_total) VALUES (%s, %s, %s, %s, %s)", rombel_list)
        logging.debug(f"      Rekap data inserted for {npsn}")
    except Exception as e:
        logging.error(f"      ✗ Error insert_rekap_data ({npsn}): {e}")
        raise

def import_all_data_for_school(npsn, info_data, profile_data_sheets):
    """Import semua data sekolah ke database."""
    conn = None
    success = False
    logging.info(f"    Memulai import database untuk NPSN: {npsn}")
    try:
        conn = db_helper.get_db_connection()
        if not conn: raise Exception("Gagal koneksi database untuk import.")
        
        with conn.cursor() as cursor:
            profil_sheet = None
            for sheet_name, sheet_data in profile_data_sheets.items():
                if 'profil' in sheet_name.lower(): 
                    profil_sheet = sheet_data
                    logging.debug(f"      Sheet profil ditemukan: '{sheet_name}'")
                    break
            if not profil_sheet: raise Exception("Sheet profil tidak ditemukan dalam data JSON.")
            
            profil_data = extract_profil_data(profil_sheet)
            
            # 1. Insert/Update sekolah_identitas
            logging.debug("      Memproses identitas...")
            id_negara = get_indonesia_id(cursor)
            identitas = profil_data['identitas']
            npsn_profil = identitas.get('npsn')
            if not npsn_profil: 
                 identitas['npsn'] = npsn 
                 logging.warning(f"      NPSN tidak ada di sheet profil, menggunakan NPSN argumen: {npsn}")
            elif npsn_profil != npsn:
                 logging.warning(f"      NPSN di profil ({npsn_profil}) berbeda dengan NPSN target ({npsn}). Menggunakan NPSN target.")
                 identitas['npsn'] = npsn

            prov = identitas.pop('provinsi', None)
            kab = identitas.pop('kabupaten_kota', None)
            kec = identitas.pop('kecamatan', None)
            id_prov = get_or_create_id(cursor, 'provinsi', 'nama_provinsi', prov, 'id_negara_fk', id_negara) if prov and id_negara else None
            id_kab = get_or_create_id(cursor, 'kabupaten_kota', 'nama_kabupaten', kab, 'id_provinsi_fk', id_prov) if kab and id_prov else None
            id_kec = get_or_create_id(cursor, 'kecamatan', 'nama_kecamatan', kec, 'id_kabupaten_fk', id_kab) if kec and id_kab else None
            identitas['id_kecamatan_fk'] = id_kec 

            if info_data.get('lintang'): identitas['lintang'] = info_data['lintang']
            if info_data.get('bujur'): identitas['bujur'] = info_data['bujur']
            
            cols = list(identitas.keys())
            placeholders = ', '.join(['%s'] * len(cols))
            update_clause = ', '.join([f"`{col}` = VALUES(`{col}`)" for col in cols if col != 'npsn']) 
            
            query_identitas = f"INSERT INTO sekolah_identitas (`{'`, `'.join(cols)}`) VALUES ({placeholders}) ON DUPLICATE KEY UPDATE {update_clause}"
            cursor.execute(query_identitas, list(identitas.values()))
            logging.info(f"      ✓ Identitas {npsn} OK.")

            # 2. Insert/Update tabel lain
            logging.debug("      Memproses pelengkap, kontak, lainnya...")
            insert_or_update_data(cursor, 'sekolah_pelengkap', npsn, profil_data['pelengkap'])
            insert_or_update_data(cursor, 'sekolah_kontak', npsn, profil_data['kontak'])
            lainnya_data = profil_data['lainnya']
            lainnya_data['kepala_sekolah'] = info_data.get('kepala_sekolah')
            lainnya_data['operator_pendataan'] = info_data.get('operator')
            insert_or_update_data(cursor, 'sekolah_lainnya', npsn, lainnya_data)
            logging.info("      ✓ Pelengkap, Kontak, Lainnya OK.")

            # 3. Proses Data Rekapitulasi
            logging.debug("      Memproses rekap...")
            rekap_sheet = None
            for sheet_name, sheet_data in profile_data_sheets.items():
                if 'rekapitulasi' in sheet_name.lower():
                    rekap_sheet = sheet_data
                    logging.debug(f"      Sheet rekapitulasi ditemukan: '{sheet_name}'")
                    break
            if rekap_sheet:
                rekap_data = extract_rekap_data(rekap_sheet)
                insert_rekap_data(cursor, npsn, rekap_data)
                logging.info("      ✓ Rekapitulasi OK.")
            else:
                 logging.warning("      ⚠ Sheet rekapitulasi tidak ditemukan.")

            conn.commit() 
            success = True
            logging.info(f"    ✓ Import DB Sukses untuk NPSN {npsn}.")

    except Exception as e:
        if conn: conn.rollback() 
        logging.error(f"    ✗ IMPORT DB GAGAL untuk NPSN {npsn}: {e}")
        traceback.print_exc()
        success = False 
    finally:
        if conn: conn.close()
        
    return success

# ----------------------------------------------------------
# 6. FUNGSI SCRAPING UTAMA PER URL (Integrasi Konversi & Import)
# ----------------------------------------------------------
def scrape_single_school(driver, url_data, batch_dir):
    global should_stop, last_error_message
    if should_stop: return False

    url_id, url_sekolah, description = url_data
    nama_sekolah_awal = description or f"URL ID {url_id}"
    logging.info(f"--> Memulai URL ID {url_id}: {nama_sekolah_awal} ({url_sekolah})")
    
    max_retries = args.max_retries_per_url
    last_step_failed = "" 
    
    excel_save_path = None
    info_json_save_path = None

    for attempt in range(max_retries):
        if should_stop: return False
        logging.info(f"    Attempt {attempt + 1}/{max_retries}...")
        
        try:
            # === TAHAP 1: SCRAPING & DOWNLOAD ===
            
            # 1. Buka halaman Dapodik
            last_step_failed = "Buka Halaman Dapodik"
            logging.info(f"      Navigasi ke: {url_sekolah}")
            driver.get(url_sekolah)
            try:
                WebDriverWait(driver, ELEMENT_TIMEOUT).until(EC.any_of(
                    EC.presence_of_element_located((By.CSS_SELECTOR, 'a[href*="/getExcel"], a[title*="Excel"], a[title*="Unduh"]')),
                    EC.presence_of_element_located((By.CSS_SELECTOR, "h2.name, h1, h2, .nama-sekolah, .school-name, .profile .info .name")) ))
                logging.info("      ✓ Halaman Dapodik termuat.")
            except TimeoutException:
                 logging.error(f"      ✗ Timeout {ELEMENT_TIMEOUT}d menunggu elemen Dapodik.")
                 raise 

            # 2. Cari Nama Sekolah
            last_step_failed = "Cari Nama Sekolah"
            nama_sekolah_element = None
            nama_selectors = ['.profile .info .name', 'h2.name', 'h1', 'h2', 'title'] 
            for selector in nama_selectors:
                try:
                    time.sleep(0.5) 
                    nama_sekolah_element = driver.find_element(By.CSS_SELECTOR, selector)
                    if nama_sekolah_element and nama_sekolah_element.text.strip(): break
                except NoSuchElementException: continue
            nama_sekolah_bersih = validate_and_clean_filename(nama_sekolah_element.text.strip() if nama_sekolah_element else nama_sekolah_awal)
            logging.info(f"      ✓ Nama Sekolah: {nama_sekolah_bersih}")

            # 3. Cari Link Download Excel
            last_step_failed = "Cari Link Excel"
            link_excel_element = None
            excel_selectors = ['a.btn[href*="/getExcel"]', 'a[title*="Excel"]', 'a[title*="Unduh"]']
            download_url = None
            for selector in excel_selectors:
                try:
                    link_excel_element = driver.find_element(By.CSS_SELECTOR, selector)
                    href = link_excel_element.get_attribute('href')
                    if href:
                        download_url = urljoin(DAPODIK_BASE_URL, href.strip()) 
                        logging.info(f"      ✓ Link Excel: {download_url}")
                        break
                except NoSuchElementException: continue
            if not download_url: raise Exception("Link download Excel tidak ditemukan") 

            # 4. Download File Excel
            last_step_failed = "Download Excel"
            timestamp = int(time.time()) 
            excel_filename = f"profil_{nama_sekolah_bersih}_{url_id}_{timestamp}.xlsx" 
            excel_save_path = os.path.join(batch_dir, excel_filename)
            logging.info("      Mulai unduh Excel...")
            if not download_file(download_url, excel_save_path):
                 raise Exception("Gagal mengunduh file Excel")

            # 5. Cari Link "Sekolah Kita"
            last_step_failed = "Cari Link Sekolah Kita"
            link_sekolah_kita_element = None
            sekolah_kita_selectors = ["a.btn[href*='sekolah.data.kemdikbud.go.id']", "a[title*='Sekolah Kita']", "//a[contains(text(),'Sekolah Kita') or contains(text(),'Data Sekolah')]"] 
            sekolah_kita_url = None
            for selector in sekolah_kita_selectors:
                try:
                    if selector.startswith("//"):
                        elements = driver.find_elements(By.XPATH, selector)
                        if elements: link_sekolah_kita_element = elements[0]
                    else: link_sekolah_kita_element = driver.find_element(By.CSS_SELECTOR, selector)
                    if link_sekolah_kita_element:
                        href_sk = link_sekolah_kita_element.get_attribute('href')
                        if href_sk:
                            sekolah_kita_url = href_sk.strip() 
                            logging.info(f"      ✓ Link Sekolah Kita: {sekolah_kita_url}")
                            break
                except NoSuchElementException: continue
            
            # 6. Scrape Data dari "Sekolah Kita"
            info_data = {'kepala_sekolah': None, 'operator': None, 'lintang': None, 'bujur': None}
            if sekolah_kita_url:
                last_step_failed = "Scrape Sekolah Kita"
                logging.info(f"      Navigasi ke Sekolah Kita: {sekolah_kita_url}")
                time.sleep(random.uniform(0.5, 1.5)) 
                try:
                    driver.execute_script("window.open(arguments[0], '_blank');", sekolah_kita_url)
                    time.sleep(1) 
                    driver.switch_to.window(driver.window_handles[-1]) 
                    WebDriverWait(driver, ELEMENT_TIMEOUT).until( EC.any_of(
                             EC.presence_of_element_located((By.XPATH, "//li[contains(., 'Kepala Sekolah')]")),
                             EC.presence_of_element_located((By.ID, "map")),
                             EC.presence_of_element_located((By.CSS_SELECTOR, ".box-profile")) ))
                    logging.info("      ✓ Halaman Sekolah Kita termuat.")
                    time.sleep(random.uniform(1, 2.5)) 
                    info_data = extract_sekolah_kita_data_robust(driver.page_source)
                    driver.close()
                    driver.switch_to.window(driver.window_handles[0]) 
                except TimeoutException: logging.warning("      ⚠ Timeout menunggu elemen di Sekolah Kita.")
                except Exception as e_sk: logging.warning(f"      ⚠ Gagal akses/scrape Sekolah Kita: {e_sk}")
                finally:
                     if len(driver.window_handles) > 1:
                         try: 
                             driver.close() 
                             driver.switch_to.window(driver.window_handles[0]) 
                         except: pass 
            else: logging.warning("      ⚠ Link 'Sekolah Kita' tidak ditemukan.")

            # 7. Simpan Info Data ke JSON
            last_step_failed = "Simpan Info JSON"
            info_json_filename = f"info_{nama_sekolah_bersih}_{url_id}_{timestamp}.json" 
            info_json_save_path = os.path.join(batch_dir, info_json_filename)
            try:
                with open(info_json_save_path, 'w', encoding='utf-8') as f:
                    json.dump(info_data, f, ensure_ascii=False, indent=2)
                logging.info(f"      ✓ Info JSON disimpan: {info_json_filename}")
            except IOError as e_json: logging.error(f"      ✗ Gagal simpan JSON: {e_json}")

            # === TAHAP 2: KONVERSI & IMPORT ===
            
            # 8. Konversi Excel ke JSON Struktur
            last_step_failed = "Konversi Excel ke JSON"
            profile_data_sheets = convert_excel_to_json(excel_save_path)
            if not profile_data_sheets:
                raise Exception("Gagal mengkonversi file Excel ke JSON")
                
            # 9. Ekstrak NPSN
            last_step_failed = "Ekstrak NPSN dari Profil"
            npsn_from_profile = None
            profil_sheet_data = None
            for sheet_name, sheet_content in profile_data_sheets.items():
                 if 'profil' in sheet_name.lower():
                     profil_sheet_data = sheet_content
                     break
            if profil_sheet_data:
                 temp_profil_data = extract_profil_data(profil_sheet_data)
                 npsn_from_profile = temp_profil_data.get('identitas', {}).get('npsn')
                 logging.info(f"      ✓ NPSN dari profil: {npsn_from_profile}")
            if not npsn_from_profile:
                 npsn_match = re.search(r'(\d{8,10})', excel_filename) 
                 if npsn_match: 
                      npsn_from_profile = npsn_match.group(1)
                      logging.warning(f"      Menggunakan NPSN fallback dari nama file: {npsn_from_profile}")
                 else:
                      raise Exception("NPSN tidak dapat diekstrak dari data profil Excel.")

            # 10. Import data ke Database
            last_step_failed = "Import Database"
            import_success = import_all_data_for_school(
                npsn=npsn_from_profile, 
                info_data=info_data, 
                profile_data_sheets=profile_data_sheets
            )
            if not import_success:
                 raise Exception("Gagal mengimpor data ke database.")

            # 11. Hapus file sementara JIKA import sukses
            hapus_file_sementara(excel_save_path, info_json_save_path)
                 
            logging.info(f"===> BERHASIL FULL PROCESS URL ID {url_id}: {nama_sekolah_bersih} <===")
            return True # Sukses

        except Exception as e_main:
            logging.error(f"    ✗ Gagal Attempt {attempt + 1} ({last_step_failed}): {e_main}")
            last_error_message = f"Gagal {nama_sekolah_awal} (step: {last_step_failed}): {str(e_main)[:150]}..." 
            if attempt < max_retries - 1 and not should_stop:
                logging.info(f"      Mencoba lagi dalam {RETRY_DELAY:.1f} detik...")
                wait_start = time.time()
                while time.time() - wait_start < RETRY_DELAY:
                    if should_stop: return False 
                    time.sleep(0.5) 
            else:
                logging.error(f"===> GAGAL TOTAL URL ID {url_id}: {nama_sekolah_awal} <===")
                logging.error("Traceback error terakhir:")
                logging.error(traceback.format_exc())
                hapus_file_sementara(excel_save_path, info_json_save_path)
                return False 
                
    return False 

# ----------------------------------------------------------
# 7. PROSES UTAMA (SEKUENSIAL)
# ----------------------------------------------------------
driver = None
total_urls_from_log = 0
processed_count = 0
success_count = 0
failed_count = 0
last_error_message = "Proses dimulai..." 

try:
    url_list, total_urls_from_log = get_urls_to_process(args.log_id)
    if not url_list:
        db_helper.update_scraping_log(log_id, 
            status='completed', total_urls=0, processed_urls=0, 
            success_count=0, failed_count=0, 
            error_message="Tidak ada URL aktif yang ditemukan untuk log ini.")
        logging.warning(f"Tidak ada URL aktif ditemukan untuk log_id {log_id}. Selesai.")
        sys.exit(0) 

    batch_dir_path = create_batch_folder_and_get_path()
    if not batch_dir_path:
        raise Exception("Gagal membuat folder batch penyimpanan.")

    db_helper.update_scraping_log(log_id, 
        status='running', 
        total_urls=total_urls_from_log, 
        processed_urls=0, 
        success_count=0, 
        failed_count=0, 
        error_message="Proses scraping dimulai...")
    
    logging.info("Mempersiapkan Selenium Driver...")
    driver = driver_helper.setup_driver(driver_path=args.driver_path)
    if not driver:
        raise Exception("Gagal menginisialisasi Selenium Driver")

    logging.info(f"\n--- Memulai Proses Scraping & Import untuk {len(url_list)} URL ---")
    for url_data_tuple in url_list:
        if should_stop:
             logging.warning("Proses dihentikan oleh sinyal.")
             last_error_message = "Proses dibatalkan oleh sinyal."
             break 

        is_success = scrape_single_school(driver, url_data_tuple, batch_dir_path)
        
        processed_count += 1
        if is_success: success_count += 1
        else: failed_count += 1
            
        db_helper.update_scraping_log(log_id=log_id,
             status='running',
             processed_urls=processed_count, 
             success_count=success_count, 
             failed_count=failed_count,   
             error_message=last_error_message if failed_count > 0 else f"Memproses {processed_count}/{total_urls_from_log}..."
        )
        
        logging.info(f"----- Jeda {REQUEST_DELAY:.1f} detik -----")
        wait_start = time.time()
        while time.time() - wait_start < REQUEST_DELAY:
            if should_stop: break
            time.sleep(0.5)

    # Finalisasi Log
    final_status = 'completed'
    final_error_message = None
    if should_stop:
        final_status = 'cancelled'
        final_error_message = last_error_message or "Proses dibatalkan."
    elif failed_count > 0:
        final_status = 'failed' 
        final_error_message = f"{failed_count}/{processed_count} URL gagal. Error terakhir: {last_error_message}"
    else: final_error_message = f"Semua {processed_count} URL berhasil diproses." 

    db_helper.update_scraping_log(log_id=log_id,
         status=final_status, 
         total_urls=total_urls_from_log, 
         success_count=success_count, 
         failed_count=failed_count,
         processed_urls=processed_count, 
         error_message=final_error_message 
    )
    
    logging.info("=" * 80)
    logging.info(f"PROSES SCRAPING & IMPORT '{DOWNLOAD_FOLDER}' SELESAI")
    logging.info(f"Status Final: {final_status.upper()}")
    logging.info(f"Total URL di Log Awal: {total_urls_from_log}")
    logging.info(f"URL Diproses: {processed_count}")
    logging.info(f"Sukses (Scrape & Import): {success_count}")
    logging.info(f"Gagal: {failed_count}")
    logging.info(f"Pesan Akhir: {final_error_message}")
    logging.info("=" * 80)
    
except Exception as e:
    logging.error(f"✗✗✗ ERROR FATAL (Level Utama): {e}")
    error_details = traceback.format_exc()
    logging.error(error_details)
    db_helper.update_scraping_log(log_id=log_id,
         status='failed',
         total_urls=total_urls_from_log,
         success_count=success_count,
         total_failed=total_urls_from_log - success_count, 
         processed_urls=processed_count,
         error_message=f"FATAL ERROR: {str(e)}\n{error_details}"
    )
    sys.exit(1) 

finally:
    if driver:
        try:
            driver.quit()
            logging.info("✓ Driver Selenium ditutup")
        except Exception as quit_err:
             logging.error(f"Error saat menutup driver: {quit_err}")
    logging.info(f">>> Script '{DOWNLOAD_FOLDER}' selesai <<<")

# --- MAIN EXECUTION ---
if __name__ == "__main__":
    main()