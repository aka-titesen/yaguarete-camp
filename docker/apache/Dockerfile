# Dockerfile para Apache con PHP-FPM
FROM httpd:2.4-alpine

# Instalar dependencias adicionales
RUN apk add --no-cache \
    openssl \
    bash

# Habilitar módulos de Apache necesarios
RUN sed -i 's/#LoadModule rewrite_module/LoadModule rewrite_module/' /usr/local/apache2/conf/httpd.conf && \
    sed -i 's/#LoadModule headers_module/LoadModule headers_module/' /usr/local/apache2/conf/httpd.conf && \
    sed -i 's/#LoadModule deflate_module/LoadModule deflate_module/' /usr/local/apache2/conf/httpd.conf && \
    sed -i 's/#LoadModule expires_module/LoadModule expires_module/' /usr/local/apache2/conf/httpd.conf && \
    sed -i 's/#LoadModule proxy_module/LoadModule proxy_module/' /usr/local/apache2/conf/httpd.conf && \
    sed -i 's/#LoadModule proxy_fcgi_module/LoadModule proxy_fcgi_module/' /usr/local/apache2/conf/httpd.conf && \
    sed -i 's/#Include conf\/extra\/httpd-vhosts.conf/Include conf\/extra\/httpd-vhosts.conf/' /usr/local/apache2/conf/httpd.conf

# Copiar configuraciones personalizadas
COPY docker/apache/vhosts.conf /usr/local/apache2/conf/extra/httpd-vhosts.conf

# Crear directorios necesarios
RUN mkdir -p /var/log/apache2

EXPOSE 80

CMD ["httpd-foreground"]
