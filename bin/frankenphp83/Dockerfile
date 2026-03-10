FROM dunglas/frankenphp:php8.3

# add additional extensions here:
RUN install-php-extensions \
    bcmath \
    calendar \
    curl \
    exif \
    gd \
    gettext \
    intl \
    mbstring \
    mysqli \
    opcache \
    pdo_mysql \
    redis \
    tidy \
    zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

#create aliases for common commands inside the docker container
RUN echo "#!/bin/bash\nvendor/bin/sake dev/build flush=" > /usr/local/bin/vbsdb
RUN echo "#!/bin/bash\nvendor/bin/sake dev/tasks/\$1 flush=" > /usr/local/bin/vbsdt
RUN echo "#!/bin/bash\ncomposer update" > /usr/local/bin/cu
RUN echo "#!/bin/bash\ncomposer install" > /usr/local/bin/ci
RUN echo "#!/bin/bash\ncomposer require \$1" > /usr/local/bin/cr
RUN chmod +x /usr/local/bin/vbsdb \
    /usr/local/bin/vbsdt \
    /usr/local/bin/cu \
    /usr/local/bin/ci \
    /usr/local/bin/cr

RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini
RUN sed -i 's/post_max_size = 8M/post_max_size = 100M/gi' /usr/local/etc/php/php.ini
RUN sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 100M/gi' /usr/local/etc/php/php.ini
RUN sed -i 's/memory_limit = 128M/memory_limit = 2G/gi' /usr/local/etc/php/php.ini

RUN echo "[opcache]" > /usr/local/etc/php/conf.d/opcache.ini
RUN echo "opcache.enable_cli=1" >> /usr/local/etc/php/conf.d/opcache.ini
RUN echo "opcache.jit_buffer_size=100M" >> /usr/local/etc/php/conf.d/opcache.ini
RUN echo "opcache.jit=tracing" >> /usr/local/etc/php/conf.d/opcache.ini

RUN echo "session.save_handler = redis" >> /usr/local/etc/php/conf.d/docker-php-ext-redis.ini
RUN echo "session.save_path = tcp://cache:6379" >> /usr/local/etc/php/conf.d/docker-php-ext-redis.ini
