FROM php:7.4-fpm-alpine3.11

ARG UID=1000
ARG GID=1000

RUN mkdir -p /app && chown ${UID}:${GID} /app
WORKDIR /app

USER root

RUN apk --update upgrade \
    && apk add --update --no-cache autoconf automake make g++ libressl-dev \

    # APCU
    && pecl install apcu && docker-php-ext-enable apcu \

    # BCMATH
    && docker-php-ext-install bcmath \

    # ZIP
    && apk add --update libzip-dev && docker-php-ext-install zip \

    # PostgreSQL libraries
    && apk add --update --no-cache libpq \

    # Build dependancies for PostgreSQL \
    && apk add --update --no-cache --virtual .docker-php-postgresql-dependancies postgresql-client postgresql-dev \
    && docker-php-ext-configure pdo_pgsql \
    && docker-php-ext-configure pgsql \
    && docker-php-ext-install pdo_pgsql pgsql \

    # OPCACHE
    && docker-php-ext-configure opcache --enable-opcache && docker-php-ext-install opcache \

    # INTL
    && apk add --update icu-dev && docker-php-ext-install intl \

    # PCNTL
    && apk add --update php7-pcntl && docker-php-ext-install pcntl \

    # GD
    && apk add --update libpng-dev && docker-php-ext-install gd \

    # IMAGICK
    && apk add --update imagemagick-dev \
    && pecl install imagick \
    && docker-php-ext-enable imagick \

    # SOCKETS
    && docker-php-ext-install sockets \

    # UNZIP
    && apk add --update unzip \

    # Install composer
    && export COMPOSER_MEMORY_LIMIT=-1 \
    && curl -sS "https://getcomposer.org/installer" | php \
    && mv composer.phar /usr/local/bin/composer \
    && composer global require hirak/prestissimo --no-plugins --no-scripts \

    && apk del autoconf \
    binutils \
    file \
    gcc \
    gdbm \
    gmp \
    isl \
    libatomic \
    libc-dev \
    libffi \
    libgomp \
    libldap \
    libmagic \
    libsasl \
    libtool \
    m4 \
    mpc1 \
    musl-dev \
    python \
    re2c \
    readline \
    sqlite-libs \
    .docker-php-postgresql-dependancies \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/* /var/cache/apk/*

USER ${UID}:${GID}

COPY docker/prod/etc/php/* /usr/local/etc/php/conf.d/

COPY composer.* ./
RUN composer install --prefer-dist --no-scripts --no-autoloader && composer clearcache

COPY --chown=${UID}:${GID} . .

RUN composer dump-autoload --no-dev --optimize --apcu

CMD ["php-fpm"]
