# 🚀 PRODUCTION DEPLOYMENT GUIDE - ClassBilliard

**Last Updated:** January 4, 2026  
**Status:** Complete Setup Instructions

---

## 🔴 **CRITICAL FIX: Vite Manifest Error**

Jika anda mendapat error: `Vite manifest not found at: /public/build/manifest.json`

**Ini terjadi karena:** Folder `public/build/` tidak di-build atau tidak di-upload ke server.

---

## ✅ **SOLUSI: 3 LANGKAH DEPLOYMENT**

### **LANGKAH 1: Update Git & Push Build Files**

```bash
# 1. Update .gitignore untuk include public/build
git add .gitignore
git commit -m "Include build files in production"

# 2. Build assets locally
npm install
npm run build

# 3. Add build files ke git
git add public/build/
git commit -m "Build assets for production"

# 4. Push ke repository
git push origin main
```

### **LANGKAH 2: Deploy ke Server**

#### **⭐ RECOMMENDED: Via Git Push (PALING PRAKTIS)**

Build files sudah ter-include di git, jadi tinggal push dan pull:

**Step 1: Build & Push di Komputer Lokal**
```powershell
# Buka PowerShell di folder project
cd C:\laragon\www\ClassBilliard

# Build
npm install
npm run build

# Commit & push
git add public/build/
git commit -m "Build assets for production"
git push origin main
```

**Step 2: Pull & Deploy di Server (via SSH/PuTTY)**
```bash
cd /home/u716336029/domains/classbilliard.com/public_html

# Pull code terbaru (include build files)
git pull origin main

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Clear Laravel caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan cache:clear

# Verify installation
php artisan --version
```

**Expected Output:**
```
Laravel Framework 12.44.0
```

**Step 3: Setup Environment (IMPORTANT!)**
```bash
# Edit .env file
nano .env
```

**Update dengan values ini:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://classbilliard.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=u716336029_classbilliard
DB_USERNAME=u716336029_admin
DB_PASSWORD=YOUR_DB_PASSWORD
```

Press `Ctrl+X` → `Y` → `Enter` untuk save

**Step 4: Verify Vite Build**
```bash
ls -la public/build/manifest.json

# Output should show:
# -rw-r--r-- 1 user user 1234 Jan 4 13:45 public/build/manifest.json
```

**✅ SELESAI! Aplikasi sudah live di production.**

---

#### **Alternatif 2: Via cPanel File Manager (Kalau ada)**

```
1. Buka cPanel hosting panel anda
2. Cari "File Manager"
3. Navigate ke: /public_html/public/
4. Upload folder build/ dari browser
5. Done!
```

---

#### **Alternatif 3: Via SCP Command (Dari PowerShell)**

Jika punya SSH key, bisa langsung upload dari Windows:

```powershell
# Download WinSCP atau gunakan SCP di PowerShell
# (Windows 10+ support native scp)

# Build dulu
npm install
npm run build

# Upload folder public/build/ ke server
scp -r "C:\laragon\www\ClassBilliard\public\build" u716336029@classbilliard.com:/home/u716336029/domains/classbilliard.com/public_html/public/

# Atau kalau ada SSH key
scp -i "C:\path\to\key.pem" -r "C:\laragon\www\ClassBilliard\public\build" u716336029@classbilliard.com:/home/u716336029/domains/classbilliard.com/public_html/public/
```

### **LANGKAH 3: Verifikasi Production**

```bash
# Check if manifest.json exists
ls -la public/build/manifest.json

# Test di browser
curl https://classbilliard.com/login
```

---

## 🔧 **ENVIRONMENT VARIABLES UNTUK PRODUCTION**

Update `.env` di server (SSH):

```env
APP_NAME="Billiard Class"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE      # Jangan copy dari .env lokal!
APP_DEBUG=false                         # CRITICAL: Harus false
APP_URL=https://classbilliard.com

BCRYPT_ROUNDS=12
LOG_LEVEL=error

# Database - Sesuaikan dengan hosting
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u716336029_classbilliard
DB_USERNAME=u716336029_admin
DB_PASSWORD=YOUR_SECURE_PASSWORD        # Gunakan password yang kuat

# Session & Cache
SESSION_DRIVER=file
SESSION_ENCRYPT=true
CACHE_STORE=file

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=kbangetya@gmail.com
MAIL_PASSWORD=pfwuawtcebfumloi         # Pertimbangkan App Password dari Google
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@classbilliard.com
MAIL_FROM_NAME="Billiard Class"

# Queue
QUEUE_CONNECTION=sync                   # atau 'database' untuk production
```

---

## 📋 **PRODUCTION CHECKLIST**

Sebelum go-live, pastikan:

- [ ] `APP_DEBUG=false` di .env
- [ ] `APP_ENV=production` di .env
- [ ] APP_KEY sudah di-generate: `php artisan key:generate`
- [ ] Database sudah di-migrate: `php artisan migrate --force`
- [ ] Seed roles & permissions: `php artisan db:seed --class=RoleAndPermissionSeeder`
- [ ] Seed users: `php artisan db:seed --class=SetupUsersWithRolesSeeder`
- [ ] Cache config: `php artisan config:cache`
- [ ] Cache routes: `php artisan route:cache`
- [ ] Vite build exists: `ls public/build/manifest.json`
- [ ] Storage symlink: `php artisan storage:link`
- [ ] File permissions correct:
  ```bash
  chmod -R 775 storage bootstrap/cache
  chmod -R 755 public
  ```
- [ ] HTTPS/SSL installed dan aktif
- [ ] Database backup scheduled
- [ ] Error monitoring setup (Sentry/New Relic)

---

## 🚀 **QUICK DEPLOY SCRIPT**

Karena Node.js tidak tersedia, gunakan script yang hanya deploy PHP:

```bash
#!/bin/bash

echo "🚀 Deploying ClassBilliard to Production..."
echo "NOTE: Build files (public/build/) harus sudah di-upload via FTP"

# Update code dari git
git pull origin main

# Install PHP dependencies saja
composer install --optimize-autoloader --no-dev

# Run migrations
php artisan migrate --force

# Seed if needed
php artisan db:seed --class=RoleAndPermissionSeeder --force
php artisan db:seed --class=SetupUsersWithRolesSeeder --force

# Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan cache:clear

# Fix permissions
chmod -R 775 storage bootstrap/cache

echo "✅ Deployment Complete!"
echo "✅ Vite assets (public/build/) harus sudah ada dari FTP upload"
```

---

## 🔐 **SECURITY PRODUCTION SETUP**

### **.htaccess Configuration** (sudah ada di `public/`)
- Ensure `public/.htaccess` sudah di-upload ke server
- Ini menghandle URL rewriting untuk Laravel

### **Database Security**
```sql
-- Create limited user (instead of root)
CREATE USER 'u716336029_admin'@'localhost' IDENTIFIED BY 'secure_password';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX ON u716336029_classbilliard.* TO 'u716336029_admin'@'localhost';
FLUSH PRIVILEGES;
```

### **SSL/HTTPS**
- Enable SSL certificate di hosting panel
- Force HTTPS dengan `.htaccess`:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

---

## 🆘 **TROUBLESHOOTING**

### **Error: Vite manifest not found**
```bash
# Solution: Build assets
npm run build
git add public/build/
git push origin main
# Then redeploy to server
```

### **Error: Storage disk not found**
```bash
# Create storage symlink
php artisan storage:link
```

### **Error: Permission denied**
```bash
# Fix file permissions
chmod -R 775 storage bootstrap/cache
chmod -R 755 public
```

### **Error: Seeder not found**
```bash
# Clear cache dan coba lagi
php artisan cache:clear
php artisan db:seed --class=RoleAndPermissionSeeder --force
```

---

## 📞 **SUPPORT**

Jika ada issues, cek:
1. Server logs: `/home/u716336029/domains/classbilliard.com/public_html/storage/logs/`
2. Error log: `tail -f storage/logs/laravel.log`
3. SSH error logs: `tail -f /var/log/error_log`

**Happy Deploying! 🎉**
