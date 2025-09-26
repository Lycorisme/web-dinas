# C:\laragon\www\dapodik3\main_scraper.pyw
import requests
from bs4 import BeautifulSoup
import time
import os
import threading
import mysql.connector
from datetime import datetime
import sys
import pandas as pd
import re
import json
from mysql.connector import Error
import signal
import atexit
import argparse
import traceback
import random
from urllib3.util.retry import Retry
from requests.adapters import HTTPAdapter

# --- Konfigurasi Awal ---
project_root = os.path.abspath(os.path.dirname(__file__))
sys.path.append(project_root)
from helper.db_connector import get_db_connection

# Konfigurasi yang ditingkatkan
JEDA_SAAT_GAGAL = 10  # Dikurangi dari 15 detik
JEDA_ANTAR_REQUEST = 2  # Jeda antara request untuk menghindari rate limiting
MAX_RETRIES = 10  # Dikurangi dari 300 untuk menghindari loop tak terbatas
REQUEST_TIMEOUT = 60  # Timeout ditingkatkan
NAMA_HARI = {'Monday': 'Senin', 'Tuesday': 'Selasa', 'Wednesday': 'Rabu', 'Thursday': 'Kamis', 'Friday': 'Jumat', 'Saturday': 'Sabtu', 'Sunday': 'Minggu'}
NAMA_BULAN = {1: 'Januari', 2: 'Februari', 3: 'Maret', 4: 'April', 5: 'Mei', 6: 'Juni', 7: 'Juli', 8: 'Agustus', 9: 'September', 10: 'Oktober', 11: 'November', 12: 'Desember'}

# --- Variabel Global & Fungsi Cleanup ---
should_stop = False
log_id = None
db_connection_lock = threading.Lock()
connection = None

def signal_handler(signum, frame):
    global should_stop
    should_stop = True
    print(f"\nSinyal {signum} diterima, proses akan berhenti setelah tugas saat ini selesai...")

signal.signal(signal.SIGTERM, signal_handler)
signal.signal(signal.SIGINT, signal_handler)

@atexit.register
def cleanup():
    global connection
    with db_connection_lock:
        if connection and connection.is_connected():
            connection.close()
            print("\nKoneksi database global ditutup.")

# ================================================================
# SECTION 1 & 2: FUNGSI DATABASE & SCRAPING - DIPERBAIKI
# ================================================================

def create_session_with_retry():
    """Membuat session requests dengan retry strategy"""
    session = requests.Session()
    
    # Retry strategy
    retry_strategy = Retry(
        total=3,
        backoff_factor=1,
        status_forcelist=[429, 500, 502, 503, 504],
    )
    
    adapter = HTTPAdapter(max_retries=retry_strategy)
    session.mount("http://", adapter)
    session.mount("https://", adapter)
    
    # Headers yang lebih realistis
    session.headers.update({
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'Accept-Language': 'id-ID,id;q=0.9,en;q=0.8',
        'Accept-Encoding': 'gzip, deflate, br',
        'Connection': 'keep-alive',
        'Upgrade-Insecure-Requests': '1',
    })
    
    return session

def get_task_details_from_log(log_id):
    """Mengambil detail tugas dari log database"""
    try:
        conn = get_db_connection()
        if not conn:
            return []
        cursor = conn.cursor(dictionary=True)
        cursor.execute("SELECT url_ids FROM scraping_logs WHERE id = %s", (log_id,))
        result = cursor.fetchone()
        cursor.close()
        conn.close()
        
        if result and result['url_ids']:
            try:
                return json.loads(result['url_ids'])
            except (json.JSONDecodeError, TypeError):
                # Handle string format like "[\"2\",\"3\"]"
                url_ids = result['url_ids'].strip('[]').replace('"', '').split(',')
                return [int(id.strip()) for id in url_ids if id.strip()]
        return []
    except Exception as e:
        print(f"Error mengambil detail tugas: {e}")
        return []

def get_urls_from_database(selected_ids):
    """Mengambil URL dari database berdasarkan ID yang dipilih"""
    try:
        conn = get_db_connection()
        if not conn:
            return []
        cursor = conn.cursor()
        
        if not selected_ids:
            return []
            
        placeholders = ','.join(['%s'] * len(selected_ids))
        query = f"SELECT id, url, description FROM scraping_urls WHERE status = 'active' AND id IN ({placeholders})"
        cursor.execute(query, tuple(selected_ids))
        urls = cursor.fetchall()
        cursor.close()
        conn.close()
        return urls
    except Exception as e:
        print(f"Error mengambil URL: {e}")
        return []

def update_scraping_log(log_id, **kwargs):
    """Update scraping log dengan thread-safe"""
    global connection
    if not log_id:
        return
        
    with db_connection_lock:
        try:
            if not connection or not connection.is_connected():
                connection = get_db_connection()
            
            if not connection:
                return
                
            cursor = connection.cursor()
            set_clauses = [f"{key} = %s" for key in kwargs]
            values = list(kwargs.values())
            
            if set_clauses:
                query = f"UPDATE scraping_logs SET {', '.join(set_clauses)} WHERE id = %s"
                values.append(log_id)
                cursor.execute(query, tuple(values))
                connection.commit()
            cursor.close()
        except Exception as e:
            print(f"Error update log: {e}")
            if connection:
                connection.rollback()

def check_log_status(log_id):
    """Cek status log dari database"""
    try:
        conn = get_db_connection()
        if not conn:
            return None
        cursor = conn.cursor()
        cursor.execute("SELECT status FROM scraping_logs WHERE id = %s", (log_id,))
        result = cursor.fetchone()
        cursor.close()
        conn.close()
        return result[0] if result else None
    except Exception as e:
        print(f"Error cek status log: {e}")
        return None

def create_batch_folder():
    """Membuat folder batch untuk menyimpan file hasil scraping"""
    now = datetime.now()
    nama_hari = NAMA_HARI.get(now.strftime('%A'), now.strftime('%A'))
    nama_bulan = NAMA_BULAN.get(now.month, str(now.month))
    folder_name = f"update {nama_hari} {now.day}-{nama_bulan}-{now.year}"
    base_dir = os.path.dirname(os.path.abspath(__file__))
    downloads_dir = os.path.join(base_dir, 'downloads')
    batch_dir = os.path.join(downloads_dir, folder_name)
    os.makedirs(batch_dir, exist_ok=True)
    return batch_dir

def extract_sekolah_kita_data(html_content):
    """Extract data dari halaman Sekolah Kita"""
    data = {'kepala_sekolah': None, 'operator': None, 'lintang': None, 'bujur': None}
    try:
        soup = BeautifulSoup(html_content, 'html.parser')
        
        # Extract kepala sekolah dengan pattern yang lebih robust
        kepsek_elements = soup.find_all('li', class_='list-group-item')
        for element in kepsek_elements:
            text = element.get_text(strip=True)
            if 'Kepala Sekolah' in text:
                # Mencari teks setelah "Kepala Sekolah"
                match = re.search(r'Kepala Sekolah\s*:?\s*(.+)', text, re.IGNORECASE)
                if match:
                    data['kepala_sekolah'] = match.group(1).strip()
                break

        # Extract operator dengan pattern yang lebih robust
        for element in kepsek_elements:
            text = element.get_text(strip=True)
            if 'Operator' in text:
                operator_link = element.find('a')
                if operator_link:
                    data['operator'] = operator_link.get_text(strip=True)
                else:
                    match = re.search(r'Operator\s*:?\s*(.+)', text, re.IGNORECASE)
                    if match:
                        data['operator'] = match.group(1).strip()
                break

        # Extract koordinat dari script dengan pattern yang lebih luas
        scripts = soup.find_all('script')
        for script in scripts:
            if script.string:
                # Pattern untuk marker Leaflet yang lebih fleksibel
                patterns = [
                    r'var marker = L\.marker\(L\.latLng\(([^,]+),([^\)]+)\)',
                    r'L\.marker\(\[([^,]+),([^\]]+)\]',
                    r'latLng\(([^,]+),([^\)]+)\)'
                ]
                
                for pattern in patterns:
                    match = re.search(pattern, script.string)
                    if match:
                        data['lintang'] = match.group(1).strip().replace("'", "").replace('"', '').strip()
                        data['bujur'] = match.group(2).strip().replace("'", "").replace('"', '').strip()
                        if data['lintang'] and data['bujur']:
                            return data
                        break
    except Exception as e:
        print(f"  [ERROR PARSING] Gagal mem-parsing data Sekolah Kita: {e}")
    return data

def validate_and_clean_filename(filename):
    """Validasi dan bersihkan nama file dari karakter tidak valid"""
    # Hapus karakter tidak valid untuk nama file
    invalid_chars = '<>:"/\\|?*'
    for char in invalid_chars:
        filename = filename.replace(char, '')
    
    # Hapus spasi berlebih dan trim
    filename = re.sub(r'\s+', ' ', filename).strip()
    
    # Batasi panjang nama file
    if len(filename) > 200:
        filename = filename[:200]
    
    return filename

def unduh_dan_scrape_sekolah(url_data, batch_dir, log_id, lock, counters):
    """Fungsi untuk mengunduh dan scraping data sekolah - VERSI DIPERBAIKI"""
    global should_stop
    url_id, url_sekolah, description = url_data
    thread_name = threading.current_thread().name
    
    print(f"[{thread_name}] Memulai: {description or url_sekolah}")
    
    # Buat session untuk thread ini
    session = create_session_with_retry()
    max_retries = MAX_RETRIES
    retry_count = 0
    
    while not should_stop and retry_count < max_retries:
        # Cek status log secara berkala
        if retry_count % 3 == 0:  # Cek setiap 3 percobaan
            log_status = check_log_status(log_id)
            if log_status == 'cancelled':
                should_stop = True
                print(f"[{thread_name}] Proses dibatalkan melalui database")
                return
        
        try:
            print(f"[{thread_name}] Mengunduh profil dari {url_sekolah}...")
            
            # Request halaman dengan timeout dan retry
            halaman_response = session.get(url_sekolah, timeout=REQUEST_TIMEOUT)
            halaman_response.raise_for_status()
            
            # Cek apakah halaman valid
            if len(halaman_response.text) < 1000:
                raise ValueError("Halaman terlalu pendek, mungkin terjadi error")
                
            soup = BeautifulSoup(halaman_response.text, 'html.parser')

            # Cari link download Excel dengan berbagai pattern
            link_element = None
            selectors = [
                'a[href*="/getExcel/getProfilSekolah"]',
                'a[href*="getExcel"]',
                'a[href*="profil"]',
                'a:contains("Excel")',
                'a:contains("Unduh")'
            ]
            
            for selector in selectors:
                try:
                    if 'contains' in selector:
                        # Manual text search
                        text_to_find = selector.split('"')[1]
                        for link in soup.find_all('a'):
                            if text_to_find in link.get_text():
                                link_element = link
                                break
                    else:
                        link_element = soup.select_one(selector)
                    if link_element:
                        break
                except Exception:
                    continue

            if not link_element:
                raise ValueError("Link unduh profil tidak ditemukan.")

            # Cari nama sekolah dengan berbagai pattern
            nama_sekolah_element = None
            nama_selectors = [
                'h2.name',
                'h1',
                'h2',
                '.nama-sekolah',
                '.school-name',
                'title'
            ]
            
            for selector in nama_selectors:
                nama_sekolah_element = soup.select_one(selector)
                if nama_sekolah_element and nama_sekolah_element.text.strip():
                    break

            if not nama_sekolah_element:
                raise ValueError("Nama sekolah tidak ditemukan.")

            # Build download URL
            href = link_element.get('href', '').strip()
            if href.startswith('/'):
                download_url = "https://dapo.kemendikdasmen.go.id" + href
            elif href.startswith('http'):
                download_url = href
            else:
                download_url = url_sekolah.rsplit('/', 1)[0] + '/' + href.lstrip('/')
            
            nama_sekolah = validate_and_clean_filename(nama_sekolah_element.text.strip())
            
            # Buat nama file yang unik
            timestamp = int(time.time())
            excel_filename = f"profil_{nama_sekolah}_{timestamp}.xlsx"
            excel_save_path = os.path.join(batch_dir, excel_filename)

            # Download file Excel dengan retry
            print(f"[{thread_name}] Mengunduh file Excel...")
            time.sleep(JEDA_ANTAR_REQUEST)  # Jeda sebelum download
            
            file_response = session.get(download_url, timeout=90, stream=True)
            file_response.raise_for_status()
            
            # Simpan file dengan chunk untuk menghindari memory issue
            with open(excel_save_path, 'wb') as file:
                for chunk in file_response.iter_content(chunk_size=8192):
                    if chunk:
                        file.write(chunk)
            print(f"[{thread_name}] Berhasil mengunduh: {excel_filename}")

            # Cari link 'Data Sekolah Kita' dengan berbagai pattern
            print(f"[{thread_name}] Mencari link 'Data Sekolah Kita'...")
            sekolah_kita_link_element = None
            sekolah_kita_selectors = [
                "a[title*='Sekolah Kita']",
                "a[href*='sekolah.kemdikbud.go.id']",
                "a:contains('Sekolah Kita')",
                "a:contains('Data Sekolah')"
            ]
            
            for selector in sekolah_kita_selectors:
                try:
                    if 'contains' in selector:
                        text_to_find = selector.split('"')[1]
                        for link in soup.find_all('a'):
                            if text_to_find in link.get_text():
                                sekolah_kita_link_element = link
                                break
                    else:
                        sekolah_kita_link_element = soup.select_one(selector)
                    if sekolah_kita_link_element:
                        break
                except Exception:
                    continue
            
            info_data = {'kepala_sekolah': None, 'operator': None, 'lintang': None, 'bujur': None}
            
            if sekolah_kita_link_element:
                sekolah_kita_url = sekolah_kita_link_element.get('href', '')
                if sekolah_kita_url:
                    if not sekolah_kita_url.startswith('http'):
                        # Build absolute URL
                        if sekolah_kita_url.startswith('/'):
                            sekolah_kita_url = "https://dapo.kemendikdasmen.go.id" + sekolah_kita_url
                        else:
                            sekolah_kita_url = url_sekolah.rsplit('/', 1)[0] + '/' + sekolah_kita_url.lstrip('/')
                    
                    print(f"[{thread_name}] Mengakses: {sekolah_kita_url}")
                    time.sleep(JEDA_ANTAR_REQUEST)  # Jeda sebelum request berikutnya
                    
                    try:
                        sekolah_kita_response = session.get(sekolah_kita_url, timeout=45)
                        sekolah_kita_response.raise_for_status()
                        info_data = extract_sekolah_kita_data(sekolah_kita_response.text)
                    except Exception as e:
                        print(f"[{thread_name}] Warning: Gagal akses Sekolah Kita: {e}")
            else:
                print(f"[{thread_name}] Warning: Link 'Data Sekolah Kita' tidak ditemukan")

            # Simpan info data ke JSON
            info_json_filename = f"info_{nama_sekolah}_{timestamp}.json"
            info_json_save_path = os.path.join(batch_dir, info_json_filename)
            with open(info_json_save_path, 'w', encoding='utf-8') as f:
                json.dump(info_data, f, ensure_ascii=False, indent=2)
            print(f"[{thread_name}] Berhasil menyimpan data tambahan: {info_json_filename}")

            # Update counters dengan thread safety
            with lock:
                counters['success'] += 1
                counters['processed'] += 1
                counters.setdefault('downloaded_files', []).append((url_id, nama_sekolah, excel_save_path, info_json_save_path))
                update_scraping_log(log_id, processed_urls=counters['processed'], success_count=counters['success'])
            
            print(f"[{thread_name}] BERHASIL LENGKAP untuk: {nama_sekolah}")
            break  # Keluar dari retry loop jika berhasil

        except requests.exceptions.RequestException as e:
            retry_count += 1
            error_msg = f"Network error: {e}"
            print(f"[{thread_name}] GAGAL (percobaan ke-{retry_count}): {error_msg}")
            
        except Exception as e:
            retry_count += 1
            error_msg = f"{type(e).__name__}: {e}"
            print(f"[{thread_name}] GAGAL (percobaan ke-{retry_count}): {error_msg}")
        
        # Update failed counter
        with lock:
            counters['failed'] += 1
            update_scraping_log(log_id, failed_count=counters['failed'])
        
        # Jika belum mencapai max retries, tunggu sebelum retry
        if retry_count < max_retries and not should_stop:
            wait_time = JEDA_SAAT_GAGAL + random.uniform(0, 5)  # Random delay untuk menghindari pattern
            print(f"[{thread_name}] Mencoba lagi dalam {wait_time:.1f} detik...")
            
            # Wait dengan periodic check untuk cancellation
            for i in range(int(wait_time * 10)):
                if should_stop or check_log_status(log_id) == 'cancelled':
                    should_stop = True
                    return
                time.sleep(0.1)
        else:
            print(f"[{thread_name}] Gagal setelah {max_retries} percobaan untuk: {description or url_sekolah}")
            break

def scrape_all_files(url_list, batch_dir, log_id):
    """Scrape semua file dengan threading - VERSI DIPERBAIKI"""
    lock = threading.Lock()
    counters = {'processed': 0, 'success': 0, 'failed': 0, 'downloaded_files': []}
    threads = []
    
    # Batasi jumlah thread bersamaan untuk menghindari overload
    max_concurrent_threads = 3
    current_threads = 0
    
    for index, url_data in enumerate(url_list):
        if should_stop:
            break
            
        # Tunggu jika terlalu banyak thread aktif
        while current_threads >= max_concurrent_threads and not should_stop:
            time.sleep(1)
            # Hitung thread yang masih aktif
            current_threads = sum(1 for t in threads if t.is_alive())
        
        if should_stop:
            break
            
        thread = threading.Thread(
            target=unduh_dan_scrape_sekolah, 
            args=(url_data, batch_dir, log_id, lock, counters), 
            name=f"Pekerja-{index+1}"
        )
        threads.append(thread)
        thread.start()
        current_threads += 1
        
        # Jeda kecil antara thread startup
        time.sleep(0.5)
    
    # Wait for all threads to complete
    for thread in threads:
        thread.join()
    
    return counters.get('downloaded_files', []), not should_stop

# ================================================================
# SECTION 3: FUNGSI KONVERSI - TETAP SAMA
# ================================================================

def convert_excel_to_json(downloaded_files):
    """Konversi file Excel ke JSON"""
    converted_files = []
    if not downloaded_files:
        return [], True
    
    for url_id, nama_sekolah, excel_path, info_json_path in downloaded_files:
        if should_stop:
            break
        try:
            if not os.path.exists(excel_path):
                print(f"  [KONVERSI] File Excel tidak ditemukan: {excel_path}")
                continue
            
            # Read all sheets dari Excel
            dict_of_sheets = pd.read_excel(excel_path, sheet_name=None, header=None)
            json_data = {}
            
            for sheet_name, df in dict_of_sheets.items():
                json_data[sheet_name] = []
                for _, row in df.iterrows():
                    row_data = {}
                    for i, value in enumerate(row):
                        row_data[f"col_{i}"] = str(value) if pd.notna(value) else None
                    json_data[sheet_name].append(row_data)
            
            profile_json_path = os.path.splitext(excel_path)[0] + ".json"
            with open(profile_json_path, 'w', encoding='utf-8') as f:
                json.dump(json_data, f, ensure_ascii=False, indent=2)
            
            print(f"  [KONVERSI] Berhasil konversi: {os.path.basename(excel_path)}")
            converted_files.append((url_id, nama_sekolah, excel_path, info_json_path, profile_json_path))
            
        except Exception as e:
            print(f"  [KONVERSI GAGAL] '{nama_sekolah}': {e}")
            
    return converted_files, not should_stop

# ================================================================
# SECTION 4: FUNGSI IMPORT DATABASE - TETAP SAMA
# ================================================================

def clean_text(text):
    """Membersihkan teks dari karakter yang tidak diinginkan"""
    if text is None or pd.isna(text):
        return None
    text_str = str(text).strip().strip(':').strip()
    return text_str if text_str and text_str.lower() not in ['-', 'none', '', 'nan'] else None

def to_int(value):
    """Konversi nilai ke integer"""
    try:
        if value is None or pd.isna(value):
            return None
        # Clean string dari karakter non-numeric
        clean_value = re.sub(r'[^\d.]', '', str(value))
        return int(float(clean_value)) if clean_value else None
    except (ValueError, TypeError):
        return None

def to_date(value):
    """Konversi nilai ke format tanggal"""
    try:
        if value is None or pd.isna(value):
            return None
        dt = pd.to_datetime(value, errors='coerce')
        return dt.strftime('%Y-%m-%d') if pd.notna(dt) else None
    except Exception:
        return None

# Mapping untuk kolom database
MAP_IDENTITAS = {
    'npsn': ('npsn', str), 
    'nama sekolah': ('nama_sekolah', str), 
    'jenjang pendidikan': ('jenjang_pendidikan', str), 
    'status sekolah': ('status_sekolah', str), 
    'alamat sekolah': ('alamat_jalan', str), 
    'kode pos': ('kode_pos', str), 
    'kelurahan': ('kelurahan', str), 
    'kecamatan': ('kecamatan', str), 
    'kabupaten/kota': ('kabupaten_kota', str), 
    'provinsi': ('provinsi', str)
}

MAP_PELENGKAP = {
    'sk pendirian sekolah': ('sk_pendirian', str), 
    'tanggal sk pendirian': ('tgl_sk_pendirian', to_date), 
    'status kepemilikan': ('status_kepemilikan', str), 
    'sk izin operasional': ('sk_izin_operasional', str), 
    'tgl sk izin operasional': ('tgl_sk_izin_operasional', to_date), 
    'kebutuhan khusus dilayani': ('kebutuhan_khusus_dilayani', str), 
    'nomor rekening': ('nomor_rekening', str), 
    'nama bank': ('nama_bank', str), 
    'cabang kcp/unit': ('cabang_kcp_unit', str), 
    'rekening atas nama': ('rekening_atas_nama', str), 
    'mbs': ('mbs', str), 
    'luas tanah milik (m2)': ('luas_tanah_milik_m2', to_int), 
    'luas tanah bukan milik (m2)': ('luas_tanah_bukan_milik_m2', to_int), 
    'nama wajib pajak': ('nama_wajib_pajak', str), 
    'npwp': ('npwp', str)
}

MAP_KONTAK = {
    'nomor telepon': ('nomor_telepon', str), 
    'nomor fax': ('nomor_fax', str), 
    'email': ('email', str), 
    'website': ('website', str)
}

MAP_LAINNYA = {
    'kepala sekolah': ('kepala_sekolah', str), 
    'operator pendataan': ('operator_pendataan', str), 
    'akreditasi': ('akreditasi', str), 
    'kurikulum': ('kurikulum', str)
}

def extract_profil_data(sheet_data):
    """Extract data profil dari sheet data"""
    all_data = {
        'identitas': {}, 
        'pelengkap': {}, 
        'kontak': {}, 
        'lainnya': {}
    }
    
    mappings = {
        'identitas': MAP_IDENTITAS, 
        'pelengkap': MAP_PELENGKAP, 
        'kontak': MAP_KONTAK, 
        'lainnya': MAP_LAINNYA
    }
    
    for row in sheet_data:
        label = clean_text(row.get('col_1'))
        if not label:
            continue
            
        # Handle RT/RW khusus
        if 'rt / rw' in label.lower():
            all_data['identitas']['rt'] = clean_text(row.get('col_3'))
            all_data['identitas']['rw'] = clean_text(row.get('col_5'))
            continue
            
        # Skip posisi geografis
        if 'posisi geografis' in label.lower():
            continue
        
        # Process mapping
        for category, mapping in mappings.items():
            if label.lower() in mapping:
                db_column, type_converter = mapping[label.lower()]
                value = clean_text(row.get('col_2')) or clean_text(row.get('col_3'))
                
                # Clean kabupaten/kota/kecamatan prefixes
                if db_column in ['kecamatan', 'kabupaten_kota', 'provinsi']:
                    if value:
                        value = re.sub(r'^(Kec\.|Kab\.|Prov\.)\s*', '', value, flags=re.IGNORECASE)
                
                all_data[category][db_column] = type_converter(value) if callable(type_converter) else value
                break
    
    return all_data

def extract_rekap_data(sheet_data):
    """Extract data rekapitulasi dari sheet data"""
    rekap_data = {'ptk_pd': [], 'sarpras': [], 'rombel': []}
    
    def find_section_start(header_text):
        for i, row in enumerate(sheet_data):
            if header_text in str(row.get('col_0', '')):
                return i
        return -1
    
    # Extract PTK dan PD data
    start_idx_ptk = find_section_start('Data PTK dan PD')
    if start_idx_ptk != -1:
        for row in sheet_data[start_idx_ptk + 2:]:
            uraian = clean_text(row.get('col_1'))
            if not uraian or 'keterangan' in uraian.lower():
                break
            if uraian in ['Laki - Laki', 'Perempuan', 'TOTAL']:
                rekap_data['ptk_pd'].append({
                    'deskripsi': uraian, 
                    'guru': to_int(row.get('col_2')), 
                    'tendik': to_int(row.get('col_3')), 
                    'ptk_total': to_int(row.get('col_4')), 
                    'pd_total': to_int(row.get('col_5'))
                })
    
    # Extract Sarpras data
    start_idx_sarpras = find_section_start('Data Sarpras')
    if start_idx_sarpras != -1:
        for row in sheet_data[start_idx_sarpras + 2:]:
            uraian = clean_text(row.get('col_1'))
            if not uraian or 'total' in uraian.lower():
                break
            rekap_data['sarpras'].append({
                'sarana': uraian, 
                'jumlah': to_int(row.get('col_2'))
            })
    
    # Extract Rombel data
    start_idx_rombel = find_section_start('Data Rombongan Belajar')
    if start_idx_rombel != -1:
        current_kelas = None
        i = 0
        while i < len(sheet_data[start_idx_rombel + 2:]):
            row = sheet_data[start_idx_rombel + 2 + i]
            kelas = clean_text(row.get('col_1'))
            jenis = clean_text(row.get('col_2'))
            
            if kelas:
                current_kelas = kelas
            
            if jenis == 'L' and i + 1 < len(sheet_data[start_idx_rombel + 2:]):
                next_row = sheet_data[start_idx_rombel + 2 + i + 1]
                rekap_data['rombel'].append({
                    'tingkat_kelas': current_kelas, 
                    'jumlah_laki_laki': to_int(row.get('col_3')), 
                    'jumlah_perempuan': to_int(next_row.get('col_3')), 
                    'jumlah_total': to_int(row.get('col_4'))
                })
                i += 2  # Skip next row karena sudah diproses
            else:
                i += 1
    
    return rekap_data

def get_indonesia_id(cursor):
    """Dapatkan ID Indonesia dari tabel negara"""
    cursor.execute("SELECT id_negara FROM negara WHERE nama_negara = 'Indonesia' LIMIT 1")
    result = cursor.fetchone()
    if result:
        return result[0]
    else:
        raise Exception("Data 'Indonesia' tidak ditemukan di tabel 'negara'.")

def get_or_create_id(cursor, table, column, value, parent_fk_col=None, parent_fk_val=None):
    """Dapatkan ID atau buat baru jika belum ada"""
    if value is None:
        return None
    
    # Build query untuk cek existing
    query = f"SELECT id_{table.split('_')[0]} FROM {table} WHERE {column} = %s"
    params = [value]
    
    if parent_fk_col and parent_fk_val is not None:
        query += f" AND {parent_fk_col} = %s"
        params.append(parent_fk_val)
    
    query += " LIMIT 1"
    cursor.execute(query, tuple(params))
    result = cursor.fetchone()
    
    # Clear any remaining results
    if cursor.rowcount > 1:
        cursor.fetchall()
    
    if result:
        return result[0]
    
    # Insert new record
    cols = f"({column}"
    vals = "(%s"
    insert_params = [value]
    
    if parent_fk_col and parent_fk_val is not None:
        cols += f", {parent_fk_col})"
        vals += ", %s)"
        insert_params.append(parent_fk_val)
    else:
        cols += ")"
        vals += ")"
    
    cursor.execute(f"INSERT INTO {table} {cols} VALUES {vals}", tuple(insert_params))
    return cursor.lastrowid

def insert_or_update_data(cursor, table_name, npsn, data_dict):
    """Insert atau update data ke tabel"""
    if not any(v is not None for v in data_dict.values()):
        return
    
    data_dict['npsn_fk'] = npsn
    
    # Cek apakah record sudah ada
    cursor.execute(f"SELECT npsn_fk FROM {table_name} WHERE npsn_fk = %s", (npsn,))
    exists = cursor.fetchone()
    
    if exists:
        # UPDATE existing record
        update_cols = [f"`{col}` = %s" for col in data_dict if col != 'npsn_fk']
        values = [val for key, val in data_dict.items() if key != 'npsn_fk']
        values.append(npsn)
        
        if update_cols:
            sql = f"UPDATE {table_name} SET {', '.join(update_cols)} WHERE npsn_fk = %s"
            cursor.execute(sql, tuple(values))
    else:
        # INSERT new record
        cols = list(data_dict.keys())
        placeholders = ', '.join(['%s'] * len(cols))
        sql = f"INSERT INTO {table_name} (`{'`, `'.join(cols)}`) VALUES ({placeholders})"
        cursor.execute(sql, list(data_dict.values()))

def insert_rekap_data(cursor, npsn, rekap_data):
    """Insert data rekapitulasi ke tabel-tabel rekap"""
    try:
        # Delete existing data untuk NPSN ini
        cursor.execute("DELETE FROM rekap_ptk_pd WHERE npsn_fk = %s", (npsn,))
        cursor.execute("DELETE FROM rekap_sarpras WHERE npsn_fk = %s", (npsn,))
        cursor.execute("DELETE FROM rekap_rombel WHERE npsn_fk = %s", (npsn,))
        
        # Insert PTK PD data
        for ptk_pd in rekap_data.get('ptk_pd', []):
            if ptk_pd.get('deskripsi'):
                cursor.execute("""
                    INSERT INTO rekap_ptk_pd (npsn_fk, deskripsi, guru, tendik, ptk_total, pd_total)
                    VALUES (%s, %s, %s, %s, %s, %s)
                """, (
                    npsn, 
                    ptk_pd['deskripsi'], 
                    ptk_pd.get('guru'), 
                    ptk_pd.get('tendik'), 
                    ptk_pd.get('ptk_total'), 
                    ptk_pd.get('pd_total')
                ))
        
        # Insert Sarpras data
        for sarpras in rekap_data.get('sarpras', []):
            if sarpras.get('sarana'):
                cursor.execute("""
                    INSERT INTO rekap_sarpras (npsn_fk, sarana, jumlah)
                    VALUES (%s, %s, %s)
                """, (
                    npsn, 
                    sarpras['sarana'], 
                    sarpras.get('jumlah')
                ))
        
        # Insert Rombel data
        for rombel in rekap_data.get('rombel', []):
            if rombel.get('tingkat_kelas'):
                cursor.execute("""
                    INSERT INTO rekap_rombel (npsn_fk, tingkat_kelas, jumlah_laki_laki, jumlah_perempuan, jumlah_total)
                    VALUES (%s, %s, %s, %s, %s)
                """, (
                    npsn, 
                    rombel['tingkat_kelas'], 
                    rombel.get('jumlah_laki_laki'), 
                    rombel.get('jumlah_perempuan'), 
                    rombel.get('jumlah_total')
                ))
                
    except Exception as e:
        print(f"  [REKAP ERROR] Error insert rekap data untuk NPSN {npsn}: {e}")
        raise

def import_all_data_for_school(npsn, info_data, profile_data_sheets):
    """Import semua data sekolah ke database"""
    conn = None
    try:
        conn = get_db_connection()
        if not conn:
            raise Exception("Tidak bisa mendapatkan koneksi database")
            
        cursor = conn.cursor()
        
        # Extract data profil dari Excel
        profil_sheet = None
        for sheet_name, sheet_data in profile_data_sheets.items():
            if 'profil' in sheet_name.lower():
                profil_sheet = sheet_data
                break
        
        if not profil_sheet:
            raise Exception("Sheet profil tidak ditemukan")
            
        profil_data = extract_profil_data(profil_sheet)
        
        # 1. Insert/Update sekolah_identitas (tanpa menimpa koordinat yang sudah ada)
        id_negara = get_indonesia_id(cursor)
        identitas = profil_data['identitas']
        identitas['npsn'] = npsn
        
        # Get wilayah IDs
        id_prov = get_or_create_id(cursor, 'provinsi', 'nama_provinsi', identitas.pop('provinsi', None), 'id_negara_fk', id_negara)
        id_kab = get_or_create_id(cursor, 'kabupaten_kota', 'nama_kabupaten', identitas.pop('kabupaten_kota', None), 'id_provinsi_fk', id_prov)
        id_kec = get_or_create_id(cursor, 'kecamatan', 'nama_kecamatan', identitas.pop('kecamatan', None), 'id_kabupaten_fk', id_kab)
        identitas['id_kecamatan_fk'] = id_kec
        
        # Insert/Update identitas sekolah
        cols = list(identitas.keys())
        placeholders = ', '.join(['%s'] * len(cols))
        
        # Update semua kolom kecuali koordinat (lintang, bujur) yang akan diupdate terpisah
        update_clause = ', '.join([f"`{col}` = VALUES(`{col}`)" for col in cols if col not in ['npsn', 'lintang', 'bujur']])
        
        query = f"""
            INSERT INTO sekolah_identitas (`{'`, `'.join(cols)}`) 
            VALUES ({placeholders}) 
            ON DUPLICATE KEY UPDATE {update_clause}
        """
        cursor.execute(query, list(identitas.values()))

        # 2. Insert/Update tabel lain dari data Excel
        insert_or_update_data(cursor, 'sekolah_pelengkap', npsn, profil_data['pelengkap'])
        insert_or_update_data(cursor, 'sekolah_kontak', npsn, profil_data['kontak'])
        insert_or_update_data(cursor, 'sekolah_lainnya', npsn, profil_data['lainnya'])

        # 3. Insert/Update data dari Info JSON (koordinat, kepala sekolah, operator)
        if info_data.get('lintang') and info_data.get('bujur'):
            try:
                cursor.execute("""
                    UPDATE sekolah_identitas 
                    SET lintang = %s, bujur = %s 
                    WHERE npsn = %s
                """, (info_data['lintang'], info_data['bujur'], npsn))
            except Exception as e:
                print(f"  [WARNING] Gagal update koordinat untuk NPSN {npsn}: {e}")
        
        # Update kepala sekolah dan operator dari info JSON jika tersedia
        if info_data.get('kepala_sekolah') or info_data.get('operator'):
            updates, values = [], []
            if info_data.get('kepala_sekolah'):
                updates.append("kepala_sekolah = COALESCE(%s, kepala_sekolah)")
                values.append(info_data['kepala_sekolah'])
            if info_data.get('operator'):
                updates.append("operator_pendataan = COALESCE(%s, operator_pendataan)")
                values.append(info_data['operator'])
            
            if updates:
                values.append(npsn)
                cursor.execute(f"""
                    UPDATE sekolah_lainnya 
                    SET {', '.join(updates)} 
                    WHERE npsn_fk = %s
                """, tuple(values))

        # 4. Proses Data Rekapitulasi
        rekap_sheet = None
        for sheet_name, sheet_data in profile_data_sheets.items():
            if 'rekapitulasi' in sheet_name.lower():
                rekap_sheet = sheet_data
                break
        
        if rekap_sheet:
            try:
                rekap_data = extract_rekap_data(rekap_sheet)
                insert_rekap_data(cursor, npsn, rekap_data)
            except Exception as e:
                print(f"  [REKAP WARNING] Gagal import data rekapitulasi untuk NPSN {npsn}: {e}")
                # Continue without failing the whole process

        conn.commit()
        print(f"  [IMPORT] Data untuk NPSN {npsn} berhasil diimpor/diperbarui.")
        return True

    except Exception as e:
        if conn:
            conn.rollback()
        print(f"  [IMPORT GAGAL] NPSN {npsn}: {e}")
        traceback.print_exc()
        return False
    finally:
        if conn and conn.is_connected():
            cursor.close()
            conn.close()

def run_database_import(converted_files):
    """Jalankan proses import database"""
    if not converted_files:
        return True
        
    success_count = 0
    total_count = len(converted_files)
    
    for url_id, nama_sekolah, excel_path, info_json_path, profile_json_path in converted_files:
        if should_stop:
            break
            
        try:
            # Load info data
            with open(info_json_path, 'r', encoding='utf-8') as f:
                info_data = json.load(f)
            
            # Load profile data
            with open(profile_json_path, 'r', encoding='utf-8') as f:
                profile_data_sheets = json.load(f)
            
            # Find profil sheet to extract NPSN
            profil_sheet = None
            for sheet_name, sheet_data in profile_data_sheets.items():
                if 'profil' in sheet_name.lower():
                    profil_sheet = sheet_data
                    break
            
            if not profil_sheet:
                print(f"  [IMPORT WARNING] Sheet profil tidak ditemukan di {profile_json_path}")
                continue
            
            # Extract NPSN
            npsn = None
            profil_data = extract_profil_data(profil_sheet)
            npsn = profil_data.get('identitas', {}).get('npsn')
            
            if not npsn:
                print(f"  [IMPORT WARNING] NPSN tidak ditemukan di {profile_json_path}")
                continue
            
            # Import to database
            if import_all_data_for_school(npsn, info_data, profile_data_sheets):
                success_count += 1
                # Clean up temporary files after successful import
                hapus_file_sementara(excel_path, info_json_path, profile_json_path)
            else:
                print(f"  [IMPORT FAILED] Gagal import data untuk {nama_sekolah} (NPSN: {npsn})")
                
        except Exception as e:
            print(f"  [IMPORT ERROR] Gagal memproses file untuk {nama_sekolah}: {e}")
            traceback.print_exc()
            continue
    
    print(f"  [IMPORT SUMMARY] {success_count}/{total_count} berhasil diimpor")
    return success_count == total_count and not should_stop

# ================================================================
# SECTION 5: HAPUS FILE - TETAP SAMA
# ================================================================

def hapus_file_sementara(excel_path, info_json_path, profile_json_path):
    """Menghapus file-file sementara setelah berhasil diimpor"""
    try:
        for file_path in [excel_path, info_json_path, profile_json_path]:
            if os.path.exists(file_path):
                os.remove(file_path)
                print(f"  [CLEANUP] Dihapus: {os.path.basename(file_path)}")
    except Exception as e:
        print(f"  [CLEANUP WARNING] Gagal menghapus file: {e}")

# ================================================================
# SECTION 6: FUNGSI UTAMA - DIPERBAIKI
# ================================================================

def main():
    """Fungsi utama scraper - VERSI DIPERBAIKI"""
    global log_id
    parser = argparse.ArgumentParser(description="Scraper data sekolah Dapodik.")
    parser.add_argument('--log_id', type=int, required=True, help='ID log proses dari database')
    args = parser.parse_args()
    log_id = args.log_id
    
    print(f"Proses dimulai untuk log_id: {log_id}")
    
    try:
        # 1. Ambil URL dari database
        selected_url_ids = get_task_details_from_log(log_id)
        if not selected_url_ids:
            raise Exception("Tidak ada URL ID yang ditemukan untuk diproses.")
        
        url_list = get_urls_from_database(selected_url_ids)
        if not url_list:
            raise Exception("Tidak ada URL aktif yang ditemukan untuk diproses.")
        
        # 2. Setup folder dan update PID
        batch_dir = create_batch_folder()
        update_scraping_log(log_id, pid=os.getpid())

        print(f"\n--- TAHAP 1 & 2: SCRAPING DATA SEKOLAH ---")
        print(f"Total URL untuk diproses: {len(url_list)}")
        print(f"Folder penyimpanan: {batch_dir}")
        
        # 3. Proses scraping dengan improved error handling
        downloaded_files, success_scrape = scrape_all_files(url_list, batch_dir, log_id)
        
        if should_stop:
            print("\nProses dihentikan oleh pengguna.")
            update_scraping_log(
                log_id, 
                status='cancelled', 
                completed_at=datetime.now().strftime('%Y-%m-%d %H:%M:%S'),
                error_message="Proses dibatalkan oleh pengguna"
            )
            return
            
        if not success_scrape:
            raise Exception("Proses scraping dihentikan atau gagal total.")
        
        print(f"\n--- TAHAP 3: KONVERSI EXCEL KE JSON ---")
        print(f"File yang berhasil diunduh: {len(downloaded_files)}")
        
        # 4. Konversi Excel ke JSON
        converted_files, success_convert = convert_excel_to_json(downloaded_files)
        
        if should_stop:
            print("\nProses dihentikan oleh pengguna selama konversi.")
            update_scraping_log(
                log_id, 
                status='cancelled', 
                completed_at=datetime.now().strftime('%Y-%m-%d %H:%M:%S'),
                error_message="Proses dibatalkan oleh pengguna selama konversi"
            )
            return
            
        if not success_convert:
            raise Exception("Proses konversi dihentikan.")

        print(f"\n--- TAHAP 4 & 5: IMPORT DATABASE & CLEANUP ---")
        print(f"File yang siap diimpor: {len(converted_files)}")
        
        # 5. Import ke database
        success_import = run_database_import(converted_files)
        
        if should_stop:
            print("\nProses dihentikan oleh pengguna selama import.")
            update_scraping_log(
                log_id, 
                status='cancelled', 
                completed_at=datetime.now().strftime('%Y-%m-%d %H:%M:%S'),
                error_message="Proses dibatalkan oleh pengguna selama import"
            )
            return
            
        if not success_import:
            print("Beberapa data gagal diimpor, tetapi proses tetap dilanjutkan.")
            
        # Update status final
        update_scraping_log(
            log_id, 
            status='completed', 
            completed_at=datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        )
        
        print("\n==============================================")
        print("SEMUA PROSES BERHASIL DISELESAIKAN!")
        print("==============================================")

    except Exception as e:
        print(f"\n[ERROR FATAL] Proses dihentikan: {e}")
        error_details = traceback.format_exc()
        print(f"Detail error:\n{error_details}")
        
        if log_id:
            current_status = check_log_status(log_id)
            if current_status != 'cancelled':
                update_scraping_log(
                    log_id, 
                    status='failed', 
                    error_message=f"{e}\n{error_details}", 
                    completed_at=datetime.now().strftime('%Y-%m-%d %H:%M:%S')
                )

if __name__ == "__main__":
    main()