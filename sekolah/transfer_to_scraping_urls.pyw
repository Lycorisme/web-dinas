import sys
import os
import argparse
import mysql.connector

# Database connection
def get_db_connection():
    try:
        conn = mysql.connector.connect(
            host='localhost',
            user='root',
            password='',
            database='dapokalsel'
        )
        return conn
    except:
        return None

# Log process
def log_process(process_type, url_induk_id, status='running', total_success=0, total_failed=0, error=None, user_id=1):
    conn = get_db_connection()
    if not conn:
        return
    
    try:
        cursor = conn.cursor()
        if status == 'running':
            query = "INSERT INTO import_log (user_id, process_type, url_induk_id, status, started_at, created_at) VALUES (%s, %s, %s, %s, NOW(), NOW())"
            cursor.execute(query, (user_id, process_type, url_induk_id, status))
        else:
            query = """UPDATE import_log 
                      SET total_success = %s, total_failed = %s, status = %s, completed_at = NOW(), error_message = %s, updated_at = NOW()
                      WHERE process_type = %s AND url_induk_id = %s AND status = 'running' 
                      ORDER BY id DESC LIMIT 1"""
            cursor.execute(query, (total_success, total_failed, status, error, process_type, url_induk_id))
        conn.commit()
    except:
        if conn:
            conn.rollback()
    finally:
        if conn and conn.is_connected():
            conn.close()

# Transfer sekolah
def transfer_sekolah(url_induk_id, user_id=1):
    conn = get_db_connection()
    if not conn:
        return False, 0, 0
    
    try:
        cursor = conn.cursor()
        
        # Get sekolah data
        query = """
        SELECT s.id, s.npsn, s.nama_sekolah, s.url, s.jenjang,
               kc.id AS kecamatan_scrape_id, kb.id AS kabupaten_scrape_id
        FROM sekolah_scrape s
        JOIN kecamatan_scrape kc ON s.kecamatan_scrape_id = kc.id
        JOIN kabupaten_scrape kb ON kc.kabupaten_scrape_id = kb.id
        WHERE kb.url_induk_id = %s AND s.status = 'active'
        """
        cursor.execute(query, (url_induk_id,))
        sekolah_data = cursor.fetchall()
        
        if not sekolah_data:
            return True, 0, 0
        
        conn.autocommit(False)
        
        inserted = 0
        updated = 0
        
        for sekolah in sekolah_data:
            sekolah_id, npsn, nama_sekolah, url, jenjang, kecamatan_id, kabupaten_id = sekolah
            
            # Check existing
            cursor.execute("SELECT id, sekolah_scrape_id FROM scraping_urls WHERE url = %s", (url,))
            existing = cursor.fetchone()
            
            if existing:
                existing_id, existing_sekolah_id = existing
                if existing_sekolah_id != sekolah_id:
                    cursor.execute("""
                        UPDATE scraping_urls 
                        SET sekolah_scrape_id = %s, description = %s, user_id = %s, updated_at = NOW()
                        WHERE id = %s
                    """, (sekolah_id, nama_sekolah, user_id, existing_id))
                    if cursor.rowcount > 0:
                        updated += 1
            else:
                cursor.execute("""
                    INSERT INTO scraping_urls 
                    (user_id, sekolah_scrape_id, kecamatan_scrape_id, kabupaten_scrape_id, url, description, status, created_at, updated_at) 
                    VALUES (%s, %s, %s, %s, %s, %s, 'active', NOW(), NOW())
                """, (user_id, sekolah_id, kecamatan_id, kabupaten_id, url, nama_sekolah))
                if cursor.rowcount > 0:
                    inserted += 1
        
        conn.commit()
        conn.autocommit(True)
        
        return True, inserted, updated
        
    except:
        if conn:
            conn.rollback()
        return False, 0, 0
    finally:
        if conn and conn.is_connected():
            conn.close()

# Main
def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('--url_induk_id', type=int, required=True)
    parser.add_argument('--user_id', type=int, default=1)
    args = parser.parse_args()
    
    log_process('transfer', args.url_induk_id, 'running', user_id=args.user_id)
    
    success, inserted, updated = transfer_sekolah(args.url_induk_id, args.user_id)
    
    if success:
        log_process('transfer', args.url_induk_id, 'completed', inserted + updated, 0, user_id=args.user_id)
        sys.exit(0)
    else:
        log_process('transfer', args.url_induk_id, 'failed', 0, 1, "Transfer failed", user_id=args.user_id)
        sys.exit(1)

if __name__ == "__main__":
    main()