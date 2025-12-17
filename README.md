# Taurus CRM

Professional CRM system for insurance lead management with role-based dashboards, real-time chat, attendance tracking, and automated salary calculations.

---

## 🚀 Deploy to Your Contabo VPS (15 Minutes Total)

### Step 1: Push to GitHub (2 minutes)
```bash
# In your local project (Windows PowerShell)
git init
git add .
git commit -m "Initial deployment"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/taurus-crm.git
git push -u origin main
```

### Step 2: One-Command Install on Contabo (10 minutes)
```bash
# SSH to Contabo
ssh root@75.119.145.66

# Run this single command (replace YOUR_GITHUB_URL):
wget -O - https://raw.githubusercontent.com/YOUR_USERNAME/taurus-crm/main/install.sh | bash
```

### Step 3: Point Domain & Get SSL (3 minutes)
```bash
# Still on Contabo server
certbot --nginx -d crm.taurustechnologies.co
```

**Done!** Visit `https://crm.taurustechnologies.co`

---

## 📝 Create `install.sh` in Your Repo

Add this file to your project root before pushing to GitHub:

```bash
#!/bin/bash
set -e

echo "🚀 Installing Taurus CRM on Contabo VPS..."

# Update system
apt update && apt upgrade -y

# Install all dependencies
apt install -y nginx mysql-server php8.2-fpm php8.2-cli php8.2-mysql \
  php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd \
  php8.2-bcmath supervisor certbot python3-certbot-nginx git unzip

# Install Composer
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Node.js 18
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt install -y nodejs

# Setup MySQL
mysql -e "CREATE DATABASE IF NOT EXISTS taurus;"
mysql -e "CREATE USER IF NOT EXISTS 'taurus'@'localhost' IDENTIFIED BY 'TaurusSecure2025!';"
mysql -e "GRANT ALL PRIVILEGES ON taurus.* TO 'taurus'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

# Clone and setup app
cd /var/www
rm -rf taurus-crm
git clone https://github.com/YOUR_USERNAME/taurus-crm.git taurus-crm
cd taurus-crm

# Environment setup
cp .env.example .env
sed -i 's|APP_ENV=.*|APP_ENV=production|g' .env
sed -i 's|APP_DEBUG=.*|APP_DEBUG=false|g' .env
sed -i 's|DB_DATABASE=.*|DB_DATABASE=taurus|g' .env
sed -i 's|DB_USERNAME=.*|DB_USERNAME=taurus|g' .env
sed -i 's|DB_PASSWORD=.*|DB_PASSWORD=TaurusSecure2025!|g' .env

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan key:generate
php artisan storage:link
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
chown -R www-data:www-data /var/www/taurus-crm
chmod -R 775 storage bootstrap/cache

# Configure Nginx
cat > /etc/nginx/sites-available/taurus-crm << 'EOF'
server {
    listen 80;
    server_name crm.taurustechnologies.co;
    root /var/www/taurus-crm/public;
    
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    
    index index.php;
    charset utf-8;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    
    error_page 404 /index.php;
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

ln -sf /etc/nginx/sites-available/taurus-crm /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl restart nginx

# Setup Laravel Reverb (WebSockets)
cat > /etc/supervisor/conf.d/laravel-reverb.conf << 'EOF'
[program:laravel-reverb]
process_name=%(program_name)s
command=php /var/www/taurus-crm/artisan reverb:start
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/taurus-crm/storage/logs/reverb.log
stopwaitsecs=3600
EOF

supervisorctl reread
supervisorctl update
supervisorctl start laravel-reverb:*

# Setup cron for scheduled tasks
(crontab -l 2>/dev/null; echo "* * * * * cd /var/www/taurus-crm && php artisan schedule:run >> /dev/null 2>&1") | crontab -

echo ""
echo "✅ Installation complete!"
echo ""
echo "📋 Next steps:"
echo "1. Point your domain DNS: crm.taurustechnologies.co → $(curl -s ifconfig.me)"
echo "2. Run: certbot --nginx -d crm.taurustechnologies.co"
echo "3. Visit: https://crm.taurustechnologies.co"
echo ""
echo "🔐 Default login:"
echo "   Email: admin@taurus.com"
echo "   Password: 12345678"
echo "   (Change immediately!)"
echo ""
```

**Before running, update these in the script:**
- Line 37: Replace `YOUR_USERNAME/taurus-crm.git` with your GitHub URL

---

## 🌐 DNS Setup (Hostinger)
Login to Hostinger → Domains → taurustechnologies.co → DNS:
```
Add A Record:
  Name: crm
  Points to: 75.119.145.66
  TTL: 3600
```
Wait 5-15 minutes for DNS propagation.

---

## 📋 Default Credentials
**Email:** `admin@taurus.com`  
**Password:** `12345678`  
⚠️ **Change immediately after first login!**

---

## 🔄 Update After Deployment

### With Laravel Forge/Ploi:
Just push to GitHub → Auto-deploys! 🎉

### Manual VPS Updates:
```bash
ssh root@YOUR_SERVER
cd /var/www/taurus-crm
git pull
composer install --no-dev
npm run build
php artisan migrate --force
php artisan config:cache
```

---

## ✨ Key Features

### Role-Based Dashboards
- **Super Admin** - Full system access, executive metrics
- **Manager** - Team oversight, reports, settings
- **Ravens Closer** - Sales calling interface (3-phase form)
- **Paraguins Closer/Validator** - Lead verification workflow
- **Retention Officer** - Customer retention management
- **QA** - Quality assurance review
- **Employee** - Attendance tracking only

### Core Modules
- ✅ **Lead Management** - Import CSV, track pipeline
- ✅ **Sales Pipeline** - Status tracking, team performance
- ✅ **Real-time Chat** - Laravel Reverb (100% local, no external service)
- ✅ **Attendance System** - Night shift support (7 PM - 5 AM)
- ✅ **Salary Calculation** - Automated with fines/bonuses
- ✅ **Dock System** - Manual fines for HR/QA
- ✅ **Insurance Carriers** - Agent commissions per carrier
- ✅ **Reports & Analytics** - CSV exports, metrics

### Attendance Features
- Auto-checkout at 6 AM for night shift
- Check-in window: 6 PM - 6 AM only
- Late detection (after 7:15 PM)
- Punctuality bonus calculation
- Manual entry for admins

---

## 📱 Tech Stack

- **Backend:** Laravel 11, PHP 8.2
- **Frontend:** Livewire 3, Bootstrap 5
- **Database:** MySQL 8.0
- **WebSockets:** Laravel Reverb
- **Assets:** Vite
- **Queue:** Redis (optional)

---

## 📂 Project Structure

```
taurus-crm/
├── app/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php      # Main dashboard routing
│   │   └── Admin/                        # Role-specific controllers
│   ├── Services/
│   │   ├── AttendanceService.php        # Attendance logic
│   │   └── SalaryCalculationService.php # Salary calculations
│   └── Models/                           # Database models
├── resources/
│   ├── views/                            # Blade templates
│   │   ├── index.blade.php              # Executive dashboard
│   │   ├── ravens/                      # Ravens closer views
│   │   ├── employee/                    # Employee views
│   │   └── admin/                       # Admin modules
│   └── scss/                            # Styling
├── routes/
│   └── web.php                          # All routes (role-based)
├── database/
│   └── migrations/                      # Database structure
└── public/
    └── build/                           # Compiled assets
```

---

## 🔒 Security

- All routes protected by role-based middleware
- Spatie Laravel Permission for RBAC
- CSRF protection enabled
- SQL injection prevention (Eloquent ORM)
- XSS protection
- SSL/HTTPS enforced
- Password hashing (bcrypt)

---

## 📝 Important Notes

### Night Shift Configuration
Office hours: **7:00 PM to 5:00 AM**
- Attendance marks for "shift day" not calendar day
- Before 5 AM = previous day's shift
- Auto-checkout at 6:10 AM

### Scheduled Tasks (Cron)
- **6:10 AM** - Auto-checkout employees
- **7:30 PM** - Mark absent users

### Real-time Chat
- Uses Laravel Reverb (local WebSocket server)
- No external dependencies (not Pusher/Ably)
- Must run as supervisor service

---

## 🆘 Support & Logs

### Application Logs
```bash
tail -f /var/www/taurus-crm/storage/logs/laravel.log
```

### Reverb Logs
```bash
tail -f /var/www/taurus-crm/storage/logs/reverb.log
```

### Nginx Logs
```bash
tail -f /var/log/nginx/access.log
tail -f /var/log/nginx/error.log
```

### Check Services
```bash
sudo supervisorctl status
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
```

---

## 📄 Additional Documentation

- **[DEPLOY.md](DEPLOY.md)** - Detailed deployment guide (792 lines)
- **[CHAT_SETUP.md](CHAT_SETUP.md)** - Real-time chat configuration
- **[NIGHT_SHIFT_ATTENDANCE.md](NIGHT_SHIFT_ATTENDANCE.md)** - Attendance system docs
- **[ATTENDANCE_RESTRICTIONS.md](ATTENDANCE_RESTRICTIONS.md)** - Time window rules
- **[IMPLEMENTATION_TASKS.md](IMPLEMENTATION_TASKS.md)** - Pending features

---

## 📄 License

Proprietary - Taurus Technologies. All rights reserved.

---

**Ready to deploy?** Follow the steps above or see [DEPLOY.md](DEPLOY.md) for the complete guide!
