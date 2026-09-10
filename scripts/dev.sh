#!/bin/bash

set -e

echo "Menyalakan MariaDB...🦭🦭🦭🦭🦭🦭🦭🦭🦭"
sudo systemctl start mariadb

cleanup() {
    echo
    echo "Menghentikan MariaDB..."
    sudo systemctl stop mariadb
    echo "MariaDB sudah dimatikan."
}

trap cleanup EXIT INT TERM

echo "Menjalankan Laravel development environment..."

npx concurrently -c "#93c5fd,#c4b5fd,#fdba74" \
    "php artisan serve" \
    "php artisan queue:listen --tries=1" \
    "npm run dev"\
    --names='server,queue,vite'