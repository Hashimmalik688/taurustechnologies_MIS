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
git clone https://github.com/Hashimmalik688/taurustechnologies.git taurus-crm
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
