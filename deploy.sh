#!/bin/bash
# RDV Discos Deployment Script
# ============================
# This script automates the deployment process on a VPS
# It should be run on the VPS after pulling changes from GitHub

echo "Starting deployment process for RDV Discos..."

# Pull the latest changes from the Git repository
echo "Pulling latest code from repository..."
git pull

# Install/update PHP dependencies
echo "Installing PHP dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Install/update JavaScript dependencies and build assets
echo "Installing JavaScript dependencies..."
npm ci
echo "Building frontend assets..."
npm run build

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Clear and rebuild application cache
echo "Optimizing application..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ensure storage directory is linked
echo "Linking storage directory..."
php artisan storage:link

# Set proper permissions
echo "Setting appropriate file permissions..."
find storage bootstrap/cache -type d -exec chmod 775 {} \;
find storage bootstrap/cache -type f -exec chmod 664 {} \;

# Restart queue worker (if using queues)
echo "Restarting queue worker..."
php artisan queue:restart

# Restart PHP-FPM service (if applicable)
# Uncomment the line below if using PHP-FPM
# echo "Restarting PHP-FPM..."
# sudo systemctl restart php8.1-fpm

echo "Deployment completed successfully!"
