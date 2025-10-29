#!/usr/bin/env python
# -*- coding: utf-8 -*-

"""
===============================================================
TRANSFER TO SCRAPING URLS - VERSI REFACTOR
===============================================================
- Menggunakan db_helper.py untuk operasi database (Menggantikan PHP Subprocess)
- Menerima argumen --url_induk_id dan --user_id
- Memindahkan data dari sekolah_scrape ke scraping_urls
- Menandai sekolah_scrape sebagai 'processed' (bukan 'active' lagi)
- Logging terpusat ke import_log via db_helper
===============================================================
"""

import sys
import os
import logging
import argparse
import traceback

# --- FIX PATH IMPORT (jika helper di subfolder 'sekolah') ---
project_root = os.path.dirname(os.path.abspath(__file__))
# Jika file ini ada di dalam folder 'sekolah', parentnya adalah root
parent_dir = os.path.dirname(project_root)
if parent_dir not in sys.path:
    sys.path.insert(0, parent_dir)
# --- AKHIR FIX PATH ---

# ----------------------------------------------------------
# 1. SETUP LOGGING & IMPORTS
# ----------------------------------------------------------
log_file = r'C:\laragon\www\dapokalsel\log_transfer.txt' # Log spesifik
logging.basicConfig(
    filename=log_file,
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s'
)

logging.info("=" * 80)
logging.info(">>> [REFACTOR] transfer_to_scraping_urls.pyw dimulai <<<")
logging.info(f"Arguments: {sys.argv}")

try:
    # Hanya butuh db_helper
    # Jika file ini di root, gunakan: from sekolah import db_helper
    # Jika file ini di dalam sekolah, gunakan: import db_helper
    try:
        # Coba import seolah skrip ini ada di root
        from sekolah import db_helper
        logging.info("✓ db_helper diimport dari subfolder 'sekolah'")
    except ImportError:
        # Jika gagal, coba import seolah skrip ini ada di dalam 'sekolah'
        import db_helper
        logging.info("✓ db_helper diimport dari folder yang sama ('sekolah')")

except ImportError as e:
    logging.error(f"✗ Gagal import db_helper: {e}")
    sys.exit(1)
except Exception as e:
    logging.error(f"✗ Error saat import: {e}")
    traceback.print_exc()
    sys.exit(1)

# ----------------------------------------------------------
# 2. PARSE ARGUMENTS
# ----------------------------------------------------------
parser = argparse.ArgumentParser(description='Transfer Sekolah data to scraping_urls')
parser.add_argument('--url_induk_id', type=int, required=True, help='URL Induk ID to process')
parser.add_argument('--user_id', type=int, required=True, help='User ID performing the transfer')
# Argumen --log_id tidak diperlukan di sini karena prosesnya relatif cepat dan log dibuat/diupdate di akhir

args = parser.parse_args()

logging.info(f"Parsed arguments:")
logging.info(f"  - url_induk_id: {args.url_induk_id}")
logging.info(f"  - user_id: {args.user_id}")

# ----------------------------------------------------------
# 3. FUNGSI UTAMA TRANSFER
# ----------------------------------------------------------

def run_transfer(url_induk_id, user_id):
    """Fungsi utama untuk mengambil data dan mentransfernya."""
    
    inserted_count = 0
    updated_count = 0
    processed_count = 0
    failed_count = 0
    log_id_transfer = None # ID log untuk proses transfer ini
    
    conn = None # Definisikan conn di scope luar try
    
    try:
        conn = db_helper.get_db_connection()
        if not conn:
            raise Exception("Gagal mendapatkan koneksi database.")

        # Buat log 'running' untuk transfer ini
        log_id_transfer = db_helper.create_log_entry(user_id, 'transfer', url_induk_id)
        if not log_id_transfer:
             logging.warning("Gagal membuat log entry untuk transfer, proses tetap berjalan.")

        # 1. Ambil data sekolah_scrape yang berstatus 'active' untuk url_induk_id ini
        logging.info(f"Mengambil data sekolah 'active' untuk URL Induk ID: {url_induk_id}")
        sekolah_to_transfer = []
        with conn.cursor() as cursor:
            query = """
                SELECT 
                    s.id AS sekolah_scrape_id, 
                    s.npsn, 
                    s.nama_sekolah, 
                    s.url, 
                    s.jenjang, 
                    s.kecamatan_scrape_id, 
                    kc.kabupaten_scrape_id -- Tambahkan ini
                FROM sekolah_scrape s
                JOIN kecamatan_scrape kc ON s.kecamatan_scrape_id = kc.id
                JOIN kabupaten_scrape kb ON kc.kabupaten_scrape_id = kb.id
                WHERE kb.url_induk_id = %s AND s.status = 'active' 
            """
            cursor.execute(query, (url_induk_id,))
            sekolah_to_transfer = cursor.fetchall()
            processed_count = len(sekolah_to_transfer) # Total yang akan diproses
        
        logging.info(f"Ditemukan {processed_count} data sekolah untuk ditransfer.")

        if not sekolah_to_transfer:
            logging.info("Tidak ada data sekolah baru untuk ditransfer.")
            # Update log sebagai completed (0 sukses) jika ada log_id
            if log_id_transfer:
                db_helper.finish_log_entry(log_id_transfer, 'completed', 0, 0, 0, "Tidak ada data sekolah baru.")
            return True # Selesai tanpa error

        # 2. Proses transfer (insert/update ke scraping_urls)
        logging.info("Memulai proses transfer ke scraping_urls...")
        sekolah_ids_processed = [] # Simpan ID sekolah_scrape yang berhasil diproses

        with conn.cursor() as cursor:
            for sekolah in sekolah_to_transfer:
                try:
                    s_id = sekolah['sekolah_scrape_id']
                    k_id = sekolah['kecamatan_scrape_id']
                    kb_id = sekolah['kabupaten_scrape_id'] # Ambil dari hasil query
                    url = sekolah['url']
                    nama = sekolah['nama_sekolah']
                    
                    # Cek apakah URL sudah ada di scraping_urls
                    cursor.execute("SELECT id, sekolah_scrape_id FROM scraping_urls WHERE url = %s", (url,))
                    existing = cursor.fetchone()
                    
                    if existing:
                        # Update jika sekolah_scrape_id berbeda (atau tambahkan field lain jika perlu)
                        if existing['sekolah_scrape_id'] != s_id:
                            cursor.execute("""
                                UPDATE scraping_urls 
                                SET sekolah_scrape_id = %s, kecamatan_scrape_id = %s, kabupaten_scrape_id = %s, 
                                    description = %s, user_id = %s, updated_at = NOW() 
                                WHERE id = %s 
                            """, (s_id, k_id, kb_id, nama, user_id, existing['id']))
                            if cursor.rowcount > 0:
                                updated_count += 1
                                sekolah_ids_processed.append(s_id)
                                logging.debug(f"  Updated URL: {url} (Sekolah ID: {s_id})")
                        else:
                            # Jika sama, tidak perlu update, tapi tandai sudah diproses
                            sekolah_ids_processed.append(s_id) 
                            logging.debug(f"  Skipped (already exists): {url} (Sekolah ID: {s_id})")
                    else:
                        # Insert baru
                        cursor.execute("""
                            INSERT INTO scraping_urls 
                            (user_id, sekolah_scrape_id, kecamatan_scrape_id, kabupaten_scrape_id, url, description, status, created_at, updated_at) 
                            VALUES (%s, %s, %s, %s, %s, %s, 'active', NOW(), NOW())
                        """, (user_id, s_id, k_id, kb_id, url, nama))
                        if cursor.lastrowid:
                            inserted_count += 1
                            sekolah_ids_processed.append(s_id)
                            logging.debug(f"  Inserted URL: {url} (Sekolah ID: {s_id})")
                        else:
                             failed_count += 1
                             logging.error(f"  Gagal insert URL: {url}")
                             
                    conn.commit() # Commit per item atau per batch? Per item lebih aman jika ada error

                except Exception as e_item:
                    failed_count += 1
                    logging.error(f"  Gagal memproses item sekolah ID {sekolah.get('sekolah_scrape_id', 'N/A')}: {e_item}")
                    conn.rollback() # Rollback item yang gagal

        logging.info(f"Transfer selesai: {inserted_count} inserted, {updated_count} updated, {failed_count} failed.")

        # 3. Tandai sekolah_scrape yang berhasil diproses sebagai 'processed'
        if sekolah_ids_processed:
            logging.info(f"Menandai {len(sekolah_ids_processed)} sekolah sebagai 'processed'...")
            try:
                with conn.cursor() as cursor:
                    placeholders = ','.join(['%s'] * len(sekolah_ids_processed))
                    query_update = f"UPDATE sekolah_scrape SET status = 'processed', updated_at = NOW() WHERE id IN ({placeholders})"
                    cursor.execute(query_update, tuple(sekolah_ids_processed))
                    conn.commit()
                    logging.info("✓ Berhasil menandai sekolah sebagai 'processed'.")
            except Exception as e_update:
                 logging.error(f"✗ Gagal menandai sekolah sebagai 'processed': {e_update}")
                 # Ini tidak dianggap error fatal untuk status log, tapi perlu dicatat
                 if log_id_transfer:
                      db_helper.finish_log_entry(log_id_transfer, 'failed' if failed_count > 0 else 'completed', 
                                                 processed_count, inserted_count + updated_count, failed_count, 
                                                 f"Gagal update status sekolah_scrape: {e_update}")
                 return False # Kembalikan False jika update status gagal

        # 4. Update log transfer final
        final_status = 'completed' if failed_count == 0 else 'failed'
        error_msg = f"{failed_count} item gagal diproses." if failed_count > 0 else None
        if log_id_transfer:
            db_helper.finish_log_entry(log_id_transfer, final_status, 
                                     processed_count, inserted_count + updated_count, failed_count, error_msg)

        return failed_count == 0 # Return True jika tidak ada error

    except Exception as e:
        logging.error(f"✗ Error fatal selama proses transfer: {e}")
        traceback.print_exc()
        # Update log sebagai failed jika ada log_id
        if log_id_transfer:
             db_helper.finish_log_entry(log_id_transfer, 'failed', processed_count, inserted_count + updated_count, 
                                        processed_count - (inserted_count + updated_count), f"FATAL: {str(e)}")
        return False # Return False jika ada error fatal
        
    finally:
         if conn:
             conn.close()
             logging.info("Koneksi database ditutup.")

# ----------------------------------------------------------
# 6. MAIN EXECUTION
# ----------------------------------------------------------
if __name__ == "__main__":
    try:
        success = run_transfer(args.url_induk_id, args.user_id)
        if success:
            logging.info("🎉 Proses transfer selesai tanpa error.")
            sys.exit(0) # Exit code 0 untuk sukses
        else:
            logging.error("❌ Proses transfer selesai dengan error.")
            sys.exit(1) # Exit code 1 untuk error
            
    except KeyboardInterrupt:
        logging.warning("\nProses transfer dihentikan oleh pengguna.")
        # Coba update log jadi cancelled jika ada log_id (meski mungkin sudah di-handle di db_helper)
        # if 'log_id_transfer' in locals() and log_id_transfer:
        #     db_helper.finish_log_entry(log_id_transfer, 'cancelled', 0,0,0, "Dibatalkan pengguna")
        sys.exit(1)
    except Exception as e_fatal:
        logging.error(f"✗✗✗ ERROR FATAL tidak tertangkap: {e_fatal}")
        traceback.print_exc()
        # Coba update log jadi failed jika ada log_id
        # if 'log_id_transfer' in locals() and log_id_transfer:
        #      db_helper.finish_log_entry(log_id_transfer, 'failed', 0,0,0, f"FATAL Uncaught: {str(e_fatal)}")
        sys.exit(1)
    finally:
        logging.info(">>> Script transfer selesai <<<")