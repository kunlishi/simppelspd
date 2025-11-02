#!/bin/sh
set -e

CERT_PATH="${SSL_CERT_PATH:-/etc/nginx/ssl/server.crt}"
KEY_PATH="${SSL_KEY_PATH:-/etc/nginx/ssl/server.key}"
CERT_DIR=$(dirname "$CERT_PATH")

if [ ! -f "$CERT_PATH" ] || [ ! -f "$KEY_PATH" ]; then
    echo "[nginx] generating self-signed certificate for ${SSL_CERT_CN:-localhost}"
    mkdir -p "$CERT_DIR"
    openssl req -x509 -nodes -newkey rsa:2048 \
        -keyout "$KEY_PATH" \
        -out "$CERT_PATH" \
        -days "${SSL_CERT_DAYS:-365}" \
        -subj "/C=ID/ST=Jakarta/L=Jakarta/O=Simppel/OU=Development/CN=${SSL_CERT_CN:-localhost}"
fi

exec "$@"