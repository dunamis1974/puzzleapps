FROM php:8.5-fpm-trixie

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        libpq-dev \
        libxslt1-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype-dev \
        libxml2-dev \
        libonig-dev \
        zlib1g-dev; \
    docker-php-ext-configure gd --with-freetype --with-jpeg; \
    docker-php-ext-install -j"$(nproc)" pdo_pgsql xsl gd mbstring; \
    rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY --chown=www-data:www-data . .

RUN set -eux; \
    mkdir -p \
        files \
        log \
        tmp \
        config/conf.d \
        core/config/conf.d \
        css \
        images \
        xslt \
        admin/tiny_mce/plugins/ibrowser/scripts/phpThumb/cache \
        admin/tiny_mce/plugins/ibrowser/temp; \
    chown -R www-data:www-data \
        files \
        log \
        tmp \
        config/conf.d \
        core/config/conf.d \
        css \
        images \
        xslt \
        admin/tiny_mce/plugins/ibrowser/scripts/phpThumb/cache \
        admin/tiny_mce/plugins/ibrowser/temp; \
    chmod 0755 \
        files \
        log \
        tmp \
        config/conf.d \
        core/config/conf.d \
        css \
        images \
        xslt \
        admin/tiny_mce/plugins/ibrowser/scripts/phpThumb/cache \
        admin/tiny_mce/plugins/ibrowser/temp

EXPOSE 9000

USER www-data

CMD ["php-fpm"]
