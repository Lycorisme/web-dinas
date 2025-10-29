#!/usr/bin/env python
# -*- coding: utf-8 -*-

"""
===============================================================
DB HELPER (PEMBANTU DATABASE) - v4 (Fix DB Name & updated_at)
===============================================================
- Mengurus koneksi database (pymysql)
- Mendukung logging ke 'import_log' dan 'scraping_logs'
- ✅ FIX: Fungsi update_log_generic sekarang hanya menambahkan
  'updated_at' jika tabelnya adalah 'import_log'
- ✅ FIX: Mengganti nama database ke 'btikp'
===============================================================
"""

import pymysql
import configparser
import os
import logging
from datetime import datetime

# Setup logging dasar
logging.basicConfig(level=logging.INFO, format='%(asctime)s [%(levelname)s] %(message)s')

def get_config():
    """Membaca file config.ini"""
    try:
        # Path relatif dari file helper ini (sekolah/db_helper.py) ke helper/config.ini
        config_path = os.path.join(os.path.dirname(__file__), '..', 'helper', 'config.ini')
        config = configparser.ConfigParser()
        config.read(config_path)
        
        return {
            'host': config.get('database', 'host', fallback='localhost'),
            'port': config.getint('database', 'port', fallback=3306),
            'user': config.get('database', 'user', fallback='root'),
            'password': config.get('database', 'password', fallback=''),
            'database': config.get('database', 'database', fallback='btikp'), # ✅ FIX: Nama DB Sesuai SQL
            'charset': 'utf8mb4'
        }
    except Exception as e:
        logging.error(f"✗ Gagal membaca config.ini di {config_path}: {e}")
        return None

def get_db_connection():
    """Membuat koneksi database baru"""
    config = get_config()
    if not config:
        return None
    try:
        connection = pymysql.connect(
            **config,
            cursorclass=pymysql.cursors.DictCursor,
            connect_timeout=10 
        )
        logging.info("✓ Koneksi database (pymysql) berhasil")
        return connection
    except Exception as e:
        logging.error(f"✗ Gagal koneksi database: {e}")
        return None

# --- FUNGSI GENERIK UNTUK UPDATE LOG (DIPERBAIKI) ---

def update_log_generic(log_id, table_name, update_data):
    """Fungsi generik untuk mengupdate tabel log (import_log atau scraping_logs)."""
    if not log_id: return
    
    conn = get_db_connection()
    if not conn:
        logging.error(f"✗ Gagal koneksi DB untuk update log {table_name} ID: {log_id}")
        return
        
    try:
        with conn.cursor() as cursor:
            set_clauses = []
            params = []
            for key, value in update_data.items():
                set_clauses.append(f"`{key}` = %s")
                params.append(value)
            
            # === PERBAIKAN DI SINI ===
            # Hanya tambahkan 'updated_at' jika tabelnya adalah 'import_log'
            if table_name == 'import_log':
                set_clauses.append("`updated_at` = NOW()")
            # === AKHIR PERBAIKAN ===
            
            # Tambahkan completed_at jika status final
            if update_data.get('status') in ['completed', 'failed', 'cancelled']:
                set_clauses.append("`completed_at` = NOW()")
            
            if not set_clauses:
                logging.warning(f"Tidak ada data untuk diupdate pada log {table_name} ID: {log_id}")
                return

            query = f"UPDATE `{table_name}` SET {', '.join(set_clauses)} WHERE `id` = %s"
            params.append(log_id)
            
            cursor.execute(query, tuple(params))
            conn.commit()
            logging.info(f"✓ Log {table_name} ID {log_id} diperbarui: status={update_data.get('status')}")
            
    except Exception as e:
        logging.error(f"✗ Gagal memperbarui log {table_name} ID {log_id}: {e}")
        conn.rollback()
    finally:
        if conn: conn.close()

# --- FUNGSI SPESIFIK UNTUK import_log ---

def finish_log_entry(log_id, status, total_processed, total_success, total_failed, error_message=None, **kwargs):
    """Memperbarui log entry di tabel 'import_log'."""
    update_data = {
        'status': status,
        'total_processed': total_processed,
        'total_success': total_success,
        'total_failed': total_failed,
        'error_message': error_message
    }
    update_data.update(kwargs) 
    update_log_generic(log_id, 'import_log', update_data)

def create_log_entry(user_id, process_type, url_induk_id=None, total_to_process=0):
    """Membuat log baru di tabel 'import_log'."""
    conn = get_db_connection()
    if not conn: return None
    try:
        with conn.cursor() as cursor:
            query = """
                INSERT INTO import_log 
                (user_id, process_type, url_induk_id, status, total_processed, started_at, created_at, updated_at) 
                VALUES (%s, %s, %s, 'running', %s, NOW(), NOW(), NOW())
            """
            cursor.execute(query, (user_id, process_type, url_induk_id, total_to_process))
            conn.commit()
            log_id = cursor.lastrowid
            logging.info(f"✓ Log entry 'import_log' dibuat, ID: {log_id}")
            return log_id
    except Exception as e:
        logging.error(f"✗ Gagal membuat log entry 'import_log': {e}")
        conn.rollback()
        return None
    finally:
        if conn: conn.close()

def get_url_induk(url_induk_id):
    """Mengambil URL Induk dari database"""
    conn = get_db_connection()
    if not conn: return None
    try:
        with conn.cursor() as cursor:
            cursor.execute("SELECT url FROM url_induk_scrape WHERE id = %s", (url_induk_id,))
            result = cursor.fetchone()
            return result['url'] if result else None
    except Exception as e:
        logging.error(f"✗ Gagal mengambil URL Induk: {e}")
        return None
    finally:
        if conn: conn.close()

# --- FUNGSI SPESIFIK BARU UNTUK scraping_logs ---

def update_scraping_log(log_id, **kwargs):
    """
    Memperbarui log entry di tabel 'scraping_logs'.
    Menerima kwargs dinamis.
    """
    # Kolom yang valid di 'scraping_logs'
    valid_columns = [
        'status', 'total_urls', 'processed_urls', 
        'success_count', 'failed_count', 'error_message', 'pid'
    ]
    
    update_data = {}
    for key, value in kwargs.items():
        if key == 'total_processed': # Handle alias dari skrip impor
             update_data['total_urls'] = value 
        elif key in valid_columns:
            update_data[key] = value
        else:
            logging.warning(f"Kunci '{key}' tidak valid untuk tabel scraping_logs dan akan diabaikan.")
    
    if update_data:
        update_log_generic(log_id, 'scraping_logs', update_data)
    else:
        logging.warning(f"Tidak ada data valid untuk diupdate pada scraping_log ID: {log_id}")