@echo off
title ORBITA Laravel Setup

echo ====================================
echo         ORBITA Laravel Setup
echo ====================================
echo.

echo [1/5] Install Composer...
call composer install
echo.

echo [2/5] Membuat file .env...
if not exist .env (
    copy .env.example .env > nul
    echo Berhasil membuat .env
) else (
    echo File .env sudah ada
)
echo.

echo [3/5] Generate Application Key...
call php artisan key:generate
echo.

echo [4/5] Membuat Storage Link...
call php artisan storage:link
echo.

echo [5/5] Membersihkan Cache...
call php artisan optimize:clear
echo.

echo ====================================
echo Setup berhasil!
echo ====================================
echo.
echo Langkah selanjutnya:
echo   1. Atur konfigurasi database pada file .env
echo   2. Jalankan:
echo      php artisan migrate
echo      php artisan serve
echo.

pause