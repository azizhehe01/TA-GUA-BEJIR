# TA-GUA-BEJIR

Aplikasi web berbasis **Laravel 12** yang dilengkapi dengan **Livewire 3**, **Volt**, **Flux UI**, dan **Vite (Tailwind CSS 4)**.

---

## 📋 Teknologi & Spesifikasi Tambahan

- **Backend Framework:** PHP ^8.2 (Laravel 12)
- **Authentication & Starter Kit:** Laravel Fortify, Sanctum, Livewire Volt & Flux UI
- **Frontend Build Tool:** Vite 7 + Tailwind CSS 4
- **Database:** SQLite (default) / Support MySQL & PostgreSQL
- **Background Jobs:** Laravel Queue (Database Driver)

---

## 🛠️ Kebutuhan Sistem & Prasyarat (Prerequisites)

Sebelum menjalankan repositori ini di lokal, pastikan perangkat Anda telah terinstall paket berikut:

1. **PHP `>= 8.2`** dengan ekstensi berikut:
   - `pdo`, `sqlite3` / `pdo_sqlite`
   - `mbstring`, `xml`, `curl`, `zip`, `intl`, `bcmath`, `fileinfo`, `openssl`
2. **Composer `>= 2.0`** (Package manager PHP)
3. **Node.js `>= 18.x`** (v20+ direkomendasikan) & **NPM `>= 9.x`**
4. **Git**

---

## 💻 Panduan Instalasi Paket Prasyarat Per OS

Pilih panduan sesuai dengan Sistem Operasi yang Anda gunakan:

### 1. Fedora (RedHat Family)

Jalankan perintah berikut di terminal Fedora untuk menginstall Git, PHP 8.2+, ekstensi yang dibutuhkan, Composer, Node.js, dan NPM:

```bash
sudo dnf update -y
sudo dnf install -y git php-cli php-common php-sqlite3 php-pdo php-mbstring php-xml php-curl php-zip php-intl php-bcmath composer nodejs npm
```

> **Verifikasi Instalasi:**
> ```bash
> php -v
> composer --version
> node -v
> npm -v
> ```

---

### 2. Zorin OS (Ubuntu / Debian Based)

Di Zorin OS (atau distro turunan Debian/Ubuntu), jalankan perintah berikut:

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y git php-cli php-sqlite3 php-mbstring php-xml php-curl php-zip php-intl php-bcmath composer nodejs npm
```

*Catatan: Jika versi PHP default di Zorin OS Anda masih di bawah 8.2, tambahkan PPA Ondřej Surý:*

```bash
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2 php8.2-cli php8.2-sqlite3 php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-intl php8.2-bcmath composer nodejs npm
```

---

### 3. Windows

Terdapat beberapa opsi instalasi prasyarat di Windows:

#### Opsi A: Menggunakan Windows Package Manager (`winget`) — Recommended
Buka **PowerShell** atau **Command Prompt** sebagai Administrator:

```powershell
winget install Git.Git
winget install PHP.PHP.8.3
winget install Composer.Composer
winget install OpenJS.NodeJS.LTS
```

*Jangan lupa mengaktifkan ekstensi PHP pada file `php.ini` Anda (hapus titik koma `;` di depan baris berikut):*
```ini
extension=pdo_sqlite
extension=sqlite3
extension=curl
extension=fileinfo
extension=mbstring
extension=openssl
extension=intl
```

#### Opsi B: Menggunakan Laragon / Laravel Herd / XAMPP
1. Download & install **Laragon** / **Laravel Herd for Windows**.
2. Pastikan versi PHP yang aktif adalah `>= 8.2`.
3. Install **Node.js LTS** dari [nodejs.org](https://nodejs.org/).
4. Install **Git for Windows** dari [git-scm.com](https://git-scm.com/).

#### Opsi C: Menggunakan WSL2 (Windows Subsystem for Linux)
Jika menggunakan WSL2 (misal Ubuntu/Debian di Windows), Anda dapat mengikuti panduan **Zorin OS / Ubuntu** di atas dari dalam terminal WSL2.

---

## 🚀 Langkah Setup & Inisialisasi Aplikasi (Local Development)

Setelah seluruh prasyarat terinstall, ikuti langkah-langkah berikut di terminal untuk memasang aplikasi:

### 1. Clone Repositori (jika belum)
```bash
git clone https://github.com/username/TA-GUA-BEJIR.git
cd TA-GUA-BEJIR
```

### 2. Salin File Environment `.env`
- **Linux (Fedora / Zorin OS / macOS) / Git Bash:**
  ```bash
  cp .env.example .env
  ```
- **Windows Command Prompt (CMD):**
  ```cmd
  copy .env.example .env
  ```
- **Windows PowerShell:**
  ```powershell
  Copy-Item .env.example .env
  ```

### 3. Install Dependency PHP (Composer)
```bash
composer install
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Buat File Database SQLite
- **Linux / Git Bash / WSL:**
  ```bash
  touch database/database.sqlite
  ```
- **Windows CMD:**
  ```cmd
  type nul > database/database.sqlite
  ```
- **Windows PowerShell:**
  ```powershell
  New-Item database/database.sqlite -ItemType File
  ```

### 6. Jalankan Migrasi & Seeder Database
```bash
php artisan migrate --seed
```

> **Akun Default (dari Seeder):**
> - **Email:** `test@example.com`
> - **Password:** `password`

### 7. Install Dependency Frontend (Node.js)
```bash
npm install
```

---

## 🏃 Menjalankan Aplikasi di Lokal

Anda dapat menjalankan aplikasi menggunakan salah satu dari dua cara berikut:

### Cara 1: Otomatis via Composer (Rekomendasi)
Perintah ini akan menjalankan server PHP, background queue listener, dan Vite frontend watcher secara bersamaan dalam satu terminal:

```bash
composer run dev
```

---

### Cara 2: Manual via Multi-Terminal
Jika ingin memantau masing-masing proses di terminal terpisah:

- **Terminal 1 (Web Server):**
  ```bash
  php artisan serve
  ```
- **Terminal 2 (Queue Listener):**
  ```bash
  php artisan queue:listen --tries=1
  ```
- **Terminal 3 (Vite Asset Bundler):**
  ```bash
  npm run dev
  ```

---

## 🌐 Akses Aplikasi

Buka browser dan akses alamat berikut:
```text
http://127.0.0.1:8000
```

---

## 🧪 Testing & Formatting

- **Menjalankan Pengujian (Tests):**
  ```bash
  composer test
  # Atau: php artisan test
  ```

- **Format Kode PHP (Pint):**
  ```bash
  ./vendor/bin/pint
  ```

- **Build Assets untuk Production:**
  ```bash
  npm run build
  ```
