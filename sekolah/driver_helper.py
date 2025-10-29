#!/usr/bin/env python
# -*- coding: utf-8 -*-

"""
===============================================================
DRIVER HELPER (PEMBANTU DRIVER) - v2
===============================================================
- Setup driver Selenium (undetected_chromedriver) secara terpusat
- ✅ BARU: Menambahkan pencarian chrome.exe (binary_location)
  secara eksplisit untuk environment web server.
===============================================================
"""

import undetected_chromedriver as uc
import os
import random
import logging

# Fungsi baru untuk mencari chrome.exe
def find_chrome_executable():
    """Mencari path chrome.exe di lokasi standar Windows"""
    logging.info("Mencari chrome.exe...")
    possible_paths = [
        r"C:\Program Files\Google\Chrome\Application\chrome.exe",
        r"C:\Program Files (x86)\Google\Chrome\Application\chrome.exe",
        os.path.expandvars(r"%USERPROFILE%\AppData\Local\Google\Chrome\Application\chrome.exe")
    ]
    
    for path in possible_paths:
        if os.path.exists(path):
            logging.info(f"✓ chrome.exe ditemukan di: {path}")
            return path
    
    logging.warning("⚠ chrome.exe tidak ditemukan di lokasi standar.")
    return None

def setup_driver(driver_path: str):
    """
    Setup driver Selenium dengan path eksplisit
    """
    try:
        logging.info("="*50)
        logging.info("🚀 Memulai Setup Driver Selenium (v2)...")
        
        options = uc.ChromeOptions()
        
        # --- Mode dasar ---
        options.add_argument("--headless=new")
        options.add_argument("--no-sandbox")
        options.add_argument("--disable-dev-shm-usage")
        options.add_argument("--disable-gpu")
        options.add_argument("--window-size=1920,1080")

        # --- Anti deteksi ---
        options.add_argument("--disable-blink-features=AutomationControlled")
        options.add_argument(f"--user-agent={random.choice(USER_AGENTS)}")

        # --- PATH EKSPLISIT (INI KUNCINYA) ---
        
        # 1. Tentukan path untuk user profile
        project_root = os.path.abspath(os.path.join(os.path.dirname(__file__), '..'))
        profile_path = os.path.join(project_root, 'temp', 'chrome_profile')
        os.makedirs(profile_path, exist_ok=True)
        options.add_argument(f"--user-data-dir={profile_path}")
        logging.info(f"✓ User Data Dir: {profile_path}")

        # 2. Tentukan path ke chromedriver.exe (dari argumen)
        if not os.path.exists(driver_path):
            logging.error(f"✗✗✗ FATAL: chromedriver.exe tidak ditemukan di: {driver_path}")
            raise FileNotFoundError(f"Driver tidak ditemukan di {driver_path}")
        logging.info(f"✓ Driver Path: {driver_path}")

        # 3. (BARU) Tentukan path ke chrome.exe (binary)
        chrome_binary_path = find_chrome_executable()
        if chrome_binary_path:
            options.binary_location = chrome_binary_path
            logging.info(f"✓ Binary Location diset ke: {chrome_binary_path}")
        else:
            logging.warning("⚠ Tidak dapat menemukan chrome.exe, mengandalkan deteksi otomatis (mungkin gagal).")

        driver = uc.Chrome(
            options=options,
            driver_executable_path=driver_path,
            version_main=None,
            use_subprocess=True
        )
        
        driver.set_page_load_timeout(90)
        logging.info("✓ Driver Selenium berhasil diinisialisasi.")
        logging.info("="*50)
        return driver
        
    except Exception as e:
        logging.error(f"✗ Gagal total setup driver: {e}")
        import traceback
        logging.error(traceback.format_exc())
        return None

USER_AGENTS = [
    # User agent yang sesuai dengan versi 141 Anda
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36"
]