#!/usr/bin/env bash
# exit on error
set -o errexit

echo "=== Starting Build Process ==="

# Install system dependencies
echo "Installing system dependencies..."
sudo apt-get update
sudo apt-get install -y sqlite3

# Create necessary directories
echo "Creating storage directories..."
mkdir -p storage/app/public
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p database

# Set proper permissions
echo "Setting file permissions..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Install PHP dependencies
echo "Installing PHP dependencies..."
composer install --no-interaction --optimize-autoloader --no-dev

# Setup SQLite database
echo "Setting up SQLite database..."
touch database/database.sqlite

# Copy environment file if not exists
if [ ! -f ".env" ]; then
    cp .env.example .env
fi

# Update environment configuration
echo "Configuring environment..."
sed -i 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/g' .env
sed -i 's/DB_DATABASE=laravel/DB_DATABASE=\/opt\/render\/project\/database\/database.sqlite/g' .env

# Generate application key if not exists
if ! grep -q "^APP_KEY=" .env; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Clear and cache config
echo "Caching configuration..."
php artisan config:clear
php artisan config:cache

# Clear and cache routes
php artisan route:clear
php artisan route:cache

# Clear and cache views
php artisan view:clear
php artisan view:cache

# Install Node.js dependencies and build assets
echo "Installing Node.js dependencies..."
npm install
npm run build

# Create storage link
echo "Creating storage link..."
php artisan storage:link

echo "=== Build Process Completed Successfully ==="
