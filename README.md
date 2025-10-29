````markdown
# DapoKalsel - Konfigurasi Singkat

## 1. Clone Project
```bash
# XAMPP
git clone https://github.com/penjamoen/dapokalsel.git C:/xampp/htdocs/dapokalsel

# Laragon
git clone https://github.com/penjamoen/dapokalsel.git C:/laragon/www/dapokalsel
````

## 2. Setup Database

1. Buka phpMyAdmin
2. Buat database baru dengan nama **btikp**
3. Import file `data/db/btikp.sql`

## 3. Konfigurasi `config.ini`

Edit file `helper/config.ini`:

```ini
[database]
host = localhost
username = root
password = 
database = btikp
```

## 4. Konfigurasi `connection.php`

Tidak Usah di ubah

```php
<?php
$config = parse_ini_file(__DIR__ . "/config.ini", true);
$conn = new mysqli(
    $config['database']['host'],
    $config['database']['username'],
    $config['database']['password'],
    $config['database']['database']
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```

## 5. Install Requirement Python

```bash
cd [path/to/dapokalsel]
XAMPP → cd C:/xampp/htdocs/dapokalsel
Laragon → cd C:/laragon/www/dapokalsel

# Buat virtual environment
python -m venv venv
.\venv\Scripts\activate   # Windows
# source venv/bin/activate   # Linux/Mac

# Install semua library
pip install -r requirements.txt
```

## 6. Jalankan Aplikasi

* Start Apache & MySQL (XAMPP/Laragon)
* Akses: [http://localhost/dapokalsel/](http://localhost/dapokalsel/)
* Login: **admin@gmail.com / 44449999**

✅ Selesai, aplikasi + scraper siap digunakan!
```