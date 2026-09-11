#!/bin/bash

set -e

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

echo "Menyalakan MariaDB..."
sudo systemctl start mariadb

echo "Menyalakan Wazuh..."

(
    cd /home/azhehe/wazuh-docker/single-node
    docker compose up -d
)

cleanup() {
    echo
    echo "Menghentikan development environment..."

    echo "Menghentikan Wazuh..."
    (
        cd /home/azhehe/wazuh-docker/single-node
        docker compose down
    )

    echo "Menghentikan MariaDB..."
    sudo systemctl stop mariadb

    echo "Semua service sudah dimatikan."
}

trap cleanup EXIT INT TERM

cd "$PROJECT_DIR"

echo "Menjalankan development environment..."

npx concurrently \
    -c "#93c5fd,#c4b5fd,#fdba74,#86efac,#f9a8d4" \
    "cd /home/azhehe/llama.cpp && ./build/bin/llama-server -hf wesjos/Qwen3-4B-toolcall-GGUF:Q4_K_M -ngl 99 --jinja --host 127.0.0.1 --port 3232" \
    "cd /home/azhehe/wazuh-ai-api && .venv/bin/uvicorn api:app --host 127.0.0.1 --port 3333" \
    "php artisan serve" \
    "php artisan queue:listen --tries=1" \
    "npm run dev" \
    --names="llama,ai-api,server,queue,vite"