# Multi-stage build para optimización
FROM php:8.2-fpm-alpine AS base

# Instalar dependencias del sistema
RUN apk add --no-cache \
    autoconf \
    bash \
    curl \
    freetype-dev \
    g++ \
    gcc \
    git \
    icu-dev \
    jpeg-dev \
    libc-dev \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    make \
    mysql-client \
    nodejs \
    npm \
    oniguruma-dev \
    openssh-client \
    rsync \
    zlib-dev

# Configurar extensiones PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        gd \
        intl \
        mbstring \
        mysqli \
        pdo \
        pdo_mysql \
        xml \
        zip

# Instalar extensión Redis
RUN pecl install redis \
    && docker-php-ext-enable redis

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Crear usuario para aplicación
RUN addgroup -g 1000 -S www && \
    adduser -u 1000 -S www -G www

# Stage para desarrollo
FROM base AS development

# Instalar Xdebug para desarrollo
RUN apk add --no-cache $PHPIZE_DEPS linux-headers \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

# Configuración de Xdebug
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Configuración PHP para desarrollo
COPY docker/php/php-dev.ini /usr/local/etc/php/conf.d/app.ini

# Copiar archivos de la aplicación
COPY --chown=www:www . .

# La configuración de Database.php ya lee del .env, no necesitamos copiar Database.docker.php
# RUN cp app/Config/Database.docker.php app/Config/Database.php

# Configurar git como safe directory
RUN git config --global --add safe.directory /var/www/html

# Instalar dependencias de Composer
RUN composer install --no-cache --optimize-autoloader

# Configurar permisos
RUN chown -R www:www /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/writable

# Configurar assets para desarrollo
RUN if [ -d "/var/www/html/assets" ]; then \
        echo "Moviendo assets a public/assets..."; \
        rm -rf /var/www/html/public/assets; \
        mv /var/www/html/assets /var/www/html/public/assets; \
    fi

USER www

EXPOSE 9000

CMD ["php-fpm"]

# Stage para producción
FROM base AS production

# Configuración PHP para producción
COPY docker/php/php-prod.ini /usr/local/etc/php/conf.d/app.ini

# Copiar archivos de la aplicación
COPY --chown=www:www . .

# La configuración de Database.php ya lee del .env, no necesitamos copiar Database.docker.php
# RUN cp app/Config/Database.docker.php app/Config/Database.php

# Instalar dependencias solo de producción
RUN composer install --no-dev --no-cache --optimize-autoloader --classmap-authoritative

# Optimizaciones para producción
RUN rm -rf tests/ \
    && rm -rf docker/ \
    && rm -f composer.json composer.lock \
    && rm -f docker-compose.yml Dockerfile

# Configurar permisos
RUN chown -R www:www /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/writable

USER www

EXPOSE 9000

CMD ["php-fpm"]
