#!/bin/bash

echo "===================================="
echo "     ORBITA Laravel Setup"
echo "===================================="

echo "1. Install Composer..."
composer install

echo "2. Membuat .env..."
if [ ! -f .env ]; then
    cp .env.example .env
fi

echo "3. Generate App Key..."
php artisan key:generate

echo "4. Storage Link..."
php artisan storage:link

echo "5. Clear Cache..."
php artisan optimize:clear

echo ""
echo "===================================="
echo "Setup selesai!"
echo "Jalankan:"
echo "php artisan migrate"
echo "php artisan serve"
echo "===================================="