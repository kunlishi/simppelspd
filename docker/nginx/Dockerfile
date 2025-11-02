FROM nginx:1.27-alpine

RUN apk add --no-cache openssl tzdata

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY docker/nginx/entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh

ENV SSL_CERT_PATH=/etc/nginx/ssl/server.crt \
    SSL_KEY_PATH=/etc/nginx/ssl/server.key \
    SSL_CERT_CN=localhost \
    SSL_CERT_DAYS=365

ENTRYPOINT ["/entrypoint.sh"]
CMD ["nginx", "-g", "daemon off;"]
