FROM php:8.5-apache-bookworm
ENV DEBIAN_FRONTEND=noninteractive

RUN DEBIAN_FRONTEND=noninteractive apt-get update && \
    DEBIAN_FRONTEND=noninteractive apt-get -y install apt-utils libdav1d-dev git unzip nano && \
    DEBIAN_FRONTEND=noninteractive apt-get dist-upgrade -y

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/install-php-extensions

# Install default PHP Extensions
RUN install-php-extensions \
    bcmath \
    mysqli \
    intl \
    gd \
    tidy \
    xsl \
    zip \
    exif \
    gmp \
    memcached \
    redis \
    apcu \
    @composer

# Set error handling
RUN echo 'date.timezone = Pacific/Auckland' > "$PHP_INI_DIR/conf.d/timezone.ini" && \
    { \
        echo 'log_errors = On'; \
        echo 'error_log = /dev/stderr'; \
        echo 'memory_limit = 2G'; \
    } > "$PHP_INI_DIR/conf.d/errors.ini"

    RUN echo "post_max_size = 1G"  > $PHP_INI_DIR/conf.d/uploads.ini
    RUN echo "upload_max_filesize = 1G" >> $PHP_INI_DIR/conf.d/uploads.ini

    RUN echo "[opcache]" > $PHP_INI_DIR/conf.d/opcache.ini
    RUN echo "opcache.enable_cli=1" >> $PHP_INI_DIR/conf.d/opcache.ini
    RUN echo "opcache.jit_buffer_size=100M" >> $PHP_INI_DIR/conf.d/opcache.ini
    RUN echo "opcache.jit=tracing" >> $PHP_INI_DIR/conf.d/opcache.ini

    RUN echo "session.save_handler = redis" > $PHP_INI_DIR/conf.d/redis.ini
    RUN echo "session.save_path = tcp://srv-captain--redis:6379" >> $PHP_INI_DIR/conf.d/redis.ini


COPY ./docker/default.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite expires remoteip headers

ENV DOCUMENT_ROOT /var/www/html
COPY . $DOCUMENT_ROOT
WORKDIR $DOCUMENT_ROOT
RUN mkdir -p /var/www/html/public/assets
RUN mkdir -p /var/www/html/public/assets/.protected
RUN touch /var/www/html/public/assets/.htaccess
RUN mkdir -p /var/www/html/themes/codecamp/dist/css
RUN mkdir -p /var/www/html/themes/codecamp/dist/js

# Buildkit is not supported yet
# RUN --mount=type=cache,target=/var/cache/composer /usr/local/bin/composer install --no-dev --prefer-dist --no-interaction --no-progress
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader
RUN composer vendor-expose
RUN chown -R 33:33 $DOCUMENT_ROOT

RUN DEBIAN_FRONTEND=noninteractive apt purge -y git unzip
# Hacky approach to ensuring apache has access to it's root folder
 RUN sed -i 's|set -e|set -e\n\n/var/www/html/vendor/bin/sake db:build --flush\n\n\nchown -R www-data:www-data /var/www/html\n |' /usr/local/bin/docker-php-entrypoint

RUN chown -R www-data:www-data \
    /var/www/html/public/assets/.protected \
    /var/www/html/public/assets/.htaccess \
    /var/www/html/public/assets/ \
    /var/www/html/themes/codecamp/dist/css \
    /var/www/html/themes/codecamp/dist/js
