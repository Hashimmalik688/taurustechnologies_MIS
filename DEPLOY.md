# Taurus CRM - Complete Deployment Guide
## Deploy on Contabo VPS with crm.taurustechnologies.co

---

## ⏱️ Total Time: 2-3 hours

---

## 📋 What You Need Before Starting

- ✅ Contabo VPS IP address 75.119.145.66
- ✅ Contabo VPS root password Hashim122004
- ✅ Hostinger login (for DNS)
- ✅ This guide open
- ✅ 2-3 hours of uninterrupted time

---

# PART 1: CONFIGURE DNS IN HOSTINGER (5 minutes)

## Step 1: Get Your VPS IP Address

Your Contabo VPS IP address should be in your Contabo welcome email or control panel.

**Write it down here:** `75.119.145.66 `

## Step 2: Configure Hostinger DNS

1. **Login to Hostinger:**
   - Go to: https://hpanel.hostinger.com
   - Enter your email and password

2. **Go to DNS Settings:**
   - Click **Domains** (left sidebar)
   - Click on **taurustechnologies.co**
   - Click **DNS / Name Servers** tab
   - Click **Manage DNS records**

3. **Add A Record:**
   - Click **Add Record**
   - Fill in:
     ```
     Type:      A
     Name:      crm
     Points to: 75.119.145.66
     TTL:       3600
     ```
   - Click **Save**

4. **Wait for DNS to work (15 minutes - 2 hours)**

   Test if it's ready:
   ```bash
   nslookup crm.taurustechnologies.co
   ```
   When you see your VPS IP, proceed to Part 2!

---

# PART 2: DEPLOY ON CONTABO VPS (1.5-2 hours)

## Step 1: Connect to Your VPS

Open Command Prompt or PowerShell (Windows):

```bash
ssh root@75.119.145.66
```
DB : TaurusSecure2025!

When asked "Are you sure?", type `yes` and press Enter.

Enter your root password when prompted.

**You're now in your VPS! All commands below run here.**

---

## Step 2: Install All Required Software (20-30 minutes)

Copy and paste each block of commands:

### Update System
```bash
apt update && apt upgrade -y
```

### Install Web Server and PHP
```bash
apt install -y nginx mysql-server php8.2-fpm php8.2-cli php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-intl php8.2-bcmath php8.2-soap supervisor unzip
```

### Install Composer (PHP package manager)
```bash
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer
```

### Install Node.js (for building assets)
```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt install -y nodejs
```

**✅ All software installed!**

---

## Step 3: Secure MySQL Database

```bash
mysql_secure_installation
```

Answer the questions:
- Set root password? **Y** (create a strong password)
- Remove anonymous users? **Y**
- Disallow root login remotely? **Y**
- Remove test database? **Y**
- Reload privilege tables? **Y**

---

## Step 4: Create Database

```bash
mysql -u root -p
```

Enter your MySQL root password. Then copy-paste this: Hashim@122004

```sql
CREATE DATABASE taurus_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'taurus_user'@'localhost' IDENTIFIED BY 'TaurusSecure2025!';
GRANT ALL PRIVILEGES ON taurus_crm.* TO 'taurus_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

**⚠️ IMPORTANT:** Change `TaurusSecure2025!` to your own strong password!

**Write down your database password:** `________________________`

---

## Step 5: Upload Your Application Files

You have two options. Choose ONE:

### Option A: Upload via SCP (From Your Windows PC)

Open a **NEW** Command Prompt/PowerShell window (keep SSH open):

```bash
cd C:\code\taurus-crm\taurus-crm-master
scp -r . root@75.119.145.66:/var/www/taurus.crm
```

This uploads all files. Takes 5-10 minutes.

### Option B: Clone from GitHub (Recommended - You already uploaded to Git)

In your VPS SSH:

```bash
cd /var/www
git clone https://github.com/Hashimmalik688/taurus.crm.git
```

**✅ Files uploaded! Your folder is now: /var/www/taurus.crm**

---

## Step 6: Configure Environment

```bash
cd /var/www/taurus.crm
cp .env.production.example .env
nano .env
```

The nano editor opens. Find and update these lines:

```env
APP_NAME="Taurus CRM"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://crm.taurustechnologies.co

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=taurus_crm
DB_USERNAME=taurus_user
DB_PASSWORD=TaurusSecure2025!

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@taurustechnologies.co
```

**Press:** `CTRL+X`, then `Y`, then `Enter` to save.

---

## Step 7: Install Dependencies (15-20 minutes)

```bash
cd /var/www/taurus.crm

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install JavaScript dependencies
npm install

# Build frontend assets
npm run build
```

This takes 10-15 minutes. Wait for it to complete.

---

## Step 8: Setup Application

```bash
cd /var/www/taurus.crm

# Generate security key
php artisan key:generate

# Link storage folder
php artisan storage:link

# Run database migrations
php artisan migrate --force

# Create initial admin user and roles
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=SettingsSeeder

# Optional: Add dummy data for testing
php artisan db:seed --class=DummyUsersSeeder
php artisan db:seed --class=DummyLeadsSeeder
php artisan db:seed --class=DummySalesSeeder

# Optimize application
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Step 9: Set File Permissions

```bash
chown -R www-data:www-data /var/www/taurus.crm
chmod -R 775 /var/www/taurus.crm/storage
chmod -R 775 /var/www/taurus.crm/bootstrap/cache
```

---

## Step 10: Configure Nginx Web Server

```bash
nano /etc/nginx/sites-available/taurus.crm
```

Copy and paste this entire block:

```nginx
server {
    listen 80;
    listen [::]:80;

    server_name crm.taurustechnologies.co;

    root /var/www/taurus.crm/public;
    index index.php index.html;

    charset utf-8;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_read_timeout 300;
        fastcgi_send_timeout 300;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    client_max_body_size 100M;
    client_body_timeout 300s;

    access_log /var/log/nginx/taurus-crm-access.log;
    error_log /var/log/nginx/taurus-crm-error.log;
}
```

**Save:** `CTRL+X`, then `Y`, then `Enter`

Enable the site:

```bash
ln -s /etc/nginx/sites-available/taurus-crm /etc/nginx/sites-enabled/
nginx -t
systemctl reload nginx
```

---

## Step 11: Test HTTP Access

Open your browser and visit:

**http://crm.taurustechnologies.co**

You should see your CRM login page! 🎉

**If you see "Welcome to nginx"**, wait 5 minutes and refresh.

**If you get an error**, check the troubleshooting section at the end.

---

## Step 12: Install SSL Certificate (HTTPS)

```bash
apt install certbot python3-certbot-nginx -y

certbot --nginx -d crm.taurustechnologies.co
```

Answer the prompts:
- Enter email: **your-email@example.com**
- Agree to Terms? **Y**
- Share email with EFF? **N** (optional)
- Redirect HTTP to HTTPS? **2** (YES, recommended)

**Test renewal:**
```bash
certbot renew --dry-run
```

Now visit: **https://crm.taurustechnologies.co** 🔒

---

## Step 13: Setup Background Workers

```bash
nano /etc/supervisor/conf.d/taurus-crm-worker.conf
```

Paste:

```ini
[program:taurus-crm-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/taurus.crm/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/taurus.crm/storage/logs/worker.log
stopwaitsecs=3600
```

**Save:** `CTRL+X`, then `Y`, then `Enter`

Start workers:

```bash
supervisorctl reread
supervisorctl update
supervisorctl start taurus-crm-worker:*
```

---

## Step 14: Setup Scheduled Tasks (Cron)

```bash
crontab -e
```

Choose nano (option 1), then add this line:

```bash
* * * * * cd /var/www/taurus.crm && php artisan schedule:run >> /dev/null 2>&1
```

**Save:** `CTRL+X`, then `Y`, then `Enter`

---

## Step 15: Enable Firewall

```bash
ufw allow 'Nginx Full'
ufw allow OpenSSH
ufw --force enable
ufw status
```

---

## Step 16: Setup Automated Backups

```bash
mkdir -p /root/backups
nano /root/backup-taurus.sh
```

Paste (update password):

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)

# Backup database
mysqldump -u taurus_user -p'TaurusSecure2025!' taurus_crm > /root/backups/db_$DATE.sql

# Backup important files
tar -czf /root/backups/files_$DATE.tar.gz /var/www/taurus.crm/storage /var/www/taurus.crm/.env

# Keep only last 7 days
find /root/backups -name "db_*" -mtime +7 -delete
find /root/backups -name "files_*" -mtime +7 -delete

echo "Backup completed: $DATE"
```

**Save and schedule:**

```bash
chmod +x /root/backup-taurus.sh

crontab -e
# Add this line (runs daily at 2 AM):
0 2 * * * /root/backup-taurus.sh >> /var/log/taurus-backup.log 2>&1
```

---

# PART 3: TEST AND GO LIVE! (15 minutes)

## Step 1: Login to Your CRM

Visit: **https://crm.taurustechnologies.co**

**Default Login:**
- Email: `admin@example.com`
- Password: `password`

## Step 2: CHANGE PASSWORD IMMEDIATELY!

1. Click your name in top right
2. Go to Profile or Settings
3. Change password to something strong

## Step 3: Test Features

- [ ] Create a new lead
- [ ] Import leads from CSV (test with sample file)
- [ ] Mark attendance
- [ ] Calculate salary
- [ ] Generate a report
- [ ] Export to CSV
- [ ] Test on your phone

---

# 🔄 MAKING LIVE CHANGES

## YES! You can make live changes WITHOUT full redeployment!

### Changes That Work Instantly:

#### 1. Edit Blade Templates (Views)
```bash
nano /var/www/taurus.crm/resources/views/YOUR_FILE.blade.php
# Make changes
# Save (CTRL+X, Y, Enter)
# Refresh browser - changes appear instantly!
```

#### 2. Edit CSS/Colors
```bash
# Edit any view file's <style> section
nano /var/www/taurus.crm/resources/views/index.blade.php
# Make changes
# Refresh browser - changes appear!
```

#### 3. Change Text/Content
```bash
# Any text in blade files can be changed instantly
# Just edit and refresh browser
```

### Changes That Need Simple Commands:

#### 1. Edit PHP Code (Controllers/Models)
```bash
nano /var/www/taurus.crm/app/Http/Controllers/YourController.php
# Make changes
# Then run:
php artisan config:clear
php artisan cache:clear
# Refresh browser
```

#### 2. Change .env Settings
```bash
nano /var/www/taurus.crm/.env
# Make changes
# Then run:
php artisan config:cache
# Restart services:
systemctl restart php8.2-fpm
```

#### 3. Add New Routes
```bash
nano /var/www/taurus.crm/routes/web.php
# Add routes
# Then run:
php artisan route:cache
```

### Changes That Need Asset Rebuild:

#### If you edit CSS/JS files in resources/
```bash
cd /var/www/taurus.crm
npm run build
# Wait 2-3 minutes
# Refresh browser
```

### Full Update Process (Only if you pulled from Git)
```bash
cd /var/www/taurus.crm
git pull origin main
composer install --no-dev
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
supervisorctl restart taurus-crm-worker:*
```

---

# 📝 Common Tasks

## View Error Logs
```bash
tail -f /var/www/taurus.crm/storage/logs/laravel.log
```

## Restart Services
```bash
systemctl restart nginx
systemctl restart php8.2-fpm
supervisorctl restart taurus-crm-worker:*
```

## Clear All Caches
```bash
cd /var/www/taurus.crm
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Run Backup Manually
```bash
/root/backup-taurus.sh
```

## Check Service Status
```bash
systemctl status nginx
systemctl status php8.2-fpm
systemctl status mysql
supervisorctl status
```

---

# 🆘 Troubleshooting

## Problem: "500 Internal Server Error"

**Solution:**
```bash
# Check logs
tail -f /var/www/taurus.crm/storage/logs/laravel.log

# Fix permissions
cd /var/www/taurus.crm
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Clear cache
php artisan cache:clear
php artisan config:clear
```

## Problem: "No application encryption key"

**Solution:**
```bash
cd /var/www/taurus.crm
php artisan key:generate
php artisan config:cache
```

## Problem: Database connection failed

**Check:**
```bash
nano /var/www/taurus.crm/.env
# Verify DB_PASSWORD matches what you set earlier

# Test MySQL connection:
mysql -u taurus_user -p taurus_crm
# Enter password - if it connects, credentials are correct
```

## Problem: Assets (CSS/JS) not loading

**Solution:**
```bash
cd /var/www/taurus.crm
php artisan storage:link
npm run build
php artisan cache:clear
systemctl restart nginx
```

## Problem: Can't upload large CSV files

**Solution:**
```bash
nano /etc/php/8.2/fpm/php.ini

# Find and update:
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300

# Save and restart:
systemctl restart php8.2-fpm
```

## Problem: Changes not showing

**Solution:**
```bash
cd /var/www/taurus.crm
php artisan view:clear
php artisan config:clear
php artisan cache:clear

# Hard refresh browser: CTRL+SHIFT+R
```

---

# 📞 Quick Reference

## Your Setup
- **URL:** https://crm.taurustechnologies.co
- **App Location:** /var/www/taurus.crm
- **Database:** taurus_crm
- **Backups:** /root/backups

## Default Credentials (CHANGE THESE!)
- **Email:** admin@example.com
- **Password:** password

## Important Files
- **Config:** /var/www/taurus.crm/.env
- **Logs:** /var/www/taurus.crm/storage/logs/laravel.log
- **Nginx Config:** /etc/nginx/sites-available/taurus-crm

## Useful Commands
```bash
# View logs
tail -f /var/www/taurus.crm/storage/logs/laravel.log

# Restart everything
systemctl restart nginx php8.2-fpm
supervisorctl restart taurus-crm-worker:*

# Clear all caches
cd /var/www/taurus.crm
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Backup now
/root/backup-taurus.sh
```

---

# ✅ Deployment Checklist

## Before Going Live
- [ ] DNS pointing to VPS IP
- [ ] All software installed
- [ ] Database created
- [ ] Application files uploaded
- [ ] .env configured correctly
- [ ] Dependencies installed
- [ ] Database migrated
- [ ] Nginx configured
- [ ] SSL certificate installed
- [ ] Workers running
- [ ] Cron jobs set
- [ ] Firewall enabled
- [ ] Backups scheduled

## After Going Live
- [ ] Can access https://crm.taurustechnologies.co
- [ ] Changed admin password
- [ ] Tested creating a lead
- [ ] Tested importing CSV
- [ ] Tested on mobile
- [ ] Email configured (optional)
- [ ] Documented passwords securely

---

# 🎉 Congratulations!

Your Taurus CRM is now live at:
**https://crm.taurustechnologies.co**

You can:
- ✅ Make instant changes to views/templates
- ✅ Edit PHP code with simple cache clear
- ✅ Import thousands of leads
- ✅ Manage employees and salaries
- ✅ Generate reports and export data
- ✅ Access from anywhere
- ✅ Use on mobile devices

**Enjoy your new CRM!** 🚀

---

**Total Deployment Time:** 2-3 hours (first time)
**Future changes:** Instant to 5 minutes depending on what you change