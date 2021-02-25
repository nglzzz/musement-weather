FROM musement_weather/app:latest

ARG UID=1000
ARG GID=1000

WORKDIR /app

USER root

RUN apk add --update autoconf \
  g++ \
  libtool \
  make \

  # XDEBUG
  && pecl install xdebug-2.9.6 && docker-php-ext-enable xdebug \

  && apk del autoconf \
    binutils \
    file \
    g++ \
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
    make \
    mpc1 \
    musl-dev \
    python \
    re2c \
    readline \
    sqlite-libs \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/* /var/cache/apk/*

USER ${UID}:${GID}

COPY docker/dev/etc/php/* /usr/local/etc/php/conf.d/

RUN composer dump-autoload --optimize
