#!/bin/bash

# Taurus CRM Deployment Script
# This script deploys the Laravel application on your VPS

echo "🚀 Starting Taurus CRM Deployment..."

# Pull latest changes (if using Git)
echo "📥 Pulling latest changes..."
git pull origin main

# Install/Update Composer dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

# Install/Update NPM dependencies
echo "📦 Installing NPM dependencies..."
npm install

# Build frontend assets
echo "🎨 Building frontend assets..."
npm run build

# Clear and cache configuration
echo "🔧 Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Clear application cache
php artisan cache:clear

# Restart queue workers (if using)
echo "🔄 Restarting queue workers..."
php artisan queue:restart

# Set proper permissions
echo "🔐 Setting permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "✅ Deployment completed successfully!"
echo "🌐 Your CRM should now be live at your domain"