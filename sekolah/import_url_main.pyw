import sys
import subprocess
import logging
import argparse
from datetime import datetime

# Setup logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

def run_process(script_name, url_induk_id=None, max_retries=300):
    """Run individual import process"""
    try:
        cmd = [sys.executable, script_name]
        if url_induk_id:
            cmd.extend(['--url_induk_id', str(url_induk_id)])
        if max_retries:
            cmd.extend(['--max_retries', str(max_retries)])
        
        logger.info(f"Running: {' '.join(cmd)}")
        
        result = subprocess.run(
            cmd,
            capture_output=True,
            text=True,
            timeout=3600  # 1 hour timeout
        )
        
        if result.returncode == 0:
            logger.info(f"{script_name} completed successfully")
            if result.stdout:
                logger.info(f"Output: {result.stdout}")
            return True
        else:
            logger.error(f"{script_name} failed with return code: {result.returncode}")
            if result.stderr:
                logger.error(f"Error: {result.stderr}")
            return False
            
    except subprocess.TimeoutExpired:
        logger.error(f"{script_name} timed out after 1 hour")
        return False
    except Exception as e:
        logger.error(f"Error running {script_name}: {e}")
        return False

def main():
    parser = argparse.ArgumentParser(description='Main controller for import process')
    parser.add_argument('--url_induk_id', type=int, help='URL Induk ID to process')
    parser.add_argument('--process_type', choices=['all', 'kabupaten', 'kecamatan', 'sekolah', 'transfer'], 
                       default='all', help='Type of process to run')
    parser.add_argument('--max_retries', type=int, default=3, help='Maximum retry attempts')
    
    args = parser.parse_args()
    
    logger.info("=== STARTING MAIN IMPORT PROCESS ===")
    logger.info(f"URL Induk ID: {args.url_induk_id}")
    logger.info(f"Process Type: {args.process_type}")
    logger.info(f"Max Retries: {args.max_retries}")
    
    success_count = 0
    total_processes = 0
    
    processes = []
    
    if args.process_type == 'all':
        processes = [
            ('import_url_kabupaten.pyw', 'Import Kabupaten'),
            ('import_url_kecamatan.pyw', 'Import Kecamatan'), 
            ('import_url_sekolah.pyw', 'Import Sekolah'),
            ('transfer_to_scraping_urls.pyw', 'Transfer to Scraping URLs')
        ]
    elif args.process_type == 'kabupaten':
        processes = [('import_url_kabupaten.pyw', 'Import Kabupaten')]
    elif args.process_type == 'kecamatan':
        processes = [('import_url_kecamatan.pyw', 'Import Kecamatan')]
    elif args.process_type == 'sekolah':
        processes = [('import_url_sekolah.pyw', 'Import Sekolah')]
    elif args.process_type == 'transfer':
        processes = [('transfer_to_scraping_urls.pyw', 'Transfer to Scraping URLs')]
    
    for script, description in processes:
        total_processes += 1
        logger.info(f"\n=== STARTING {description.upper()} ===")
        
        if run_process(script, args.url_induk_id, args.max_retries):
            success_count += 1
            logger.info(f"✅ {description} completed successfully")
        else:
            logger.error(f"❌ {description} failed")
            if args.process_type == 'all':
                logger.warning(f"Continuing with next process...")
            else:
                logger.error(f"Process failed, stopping execution")
                sys.exit(1)
    
    logger.info(f"\n=== IMPORT PROCESS SUMMARY ===")
    logger.info(f"Total Processes: {total_processes}")
    logger.info(f"Successful: {success_count}")
    logger.info(f"Failed: {total_processes - success_count}")
    
    if success_count == total_processes:
        logger.info("🎉 ALL PROCESSES COMPLETED SUCCESSFULLY!")
        sys.exit(0)
    else:
        logger.warning(f"⚠️ {total_processes - success_count} processes failed")
        sys.exit(1)

if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        logger.info("\nProcess interrupted by user")
        sys.exit(1)
    except Exception as e:
        logger.error(f"Unexpected error: {e}")
        sys.exit(1)