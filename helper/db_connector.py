import mysql.connector
import configparser
import os
import sys

def get_db_connection():
    """
    Membaca konfigurasi dari config.ini dan mengembalikan object koneksi database.
    """
    try:
        # Menentukan path absolut ke file config.ini dari lokasi skrip ini
        config_path = os.path.join(os.path.dirname(__file__), 'config.ini')
        
        if not os.path.exists(config_path):
            print(f"Error: File konfigurasi tidak ditemukan di {config_path}")
            return None

        parser = configparser.ConfigParser()
        parser.read(config_path)
        
        db_config = parser['database']
        
        connection = mysql.connector.connect(
            host=db_config['host'],
            user=db_config['username'],
            password=db_config['password'],
            database=db_config['database'],
            auth_plugin='mysql_native_password'
        )
        return connection
    except mysql.connector.Error as err:
        print(f"Error connecting to database: {err}")
        # Cetak error ke stderr agar bisa ditangkap oleh PHP jika perlu
        sys.stderr.write(f"DB Connection Error: {err}\n")
        return None

if __name__ == '__main__':
    # Bagian ini untuk testing koneksi saat file dijalankan langsung
    conn = get_db_connection()
    if conn and conn.is_connected():
        print("Koneksi database berhasil.")
        conn.close()
    else:
        print("Koneksi database gagal.")