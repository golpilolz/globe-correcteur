FROM composer:2 AS composer

FROM php:8.3-fpm-alpine as prod

ARG UNAME=app
ARG UID=1000
ARG GID=1000
ARG ENV=prod

COPY --from=composer /usr/bin/composer /usr/bin/composer

ENV TZ=Europe/Paris

RUN apk update && apk add \
    vim \
    libzip-dev \
    zip \
    unzip \
    libjpeg-turbo-dev \
    freetype-dev \
    libpng-dev \
    icu-dev \
    libpq-dev \
    sudo \
    bash \
    && docker-php-ext-install zip pdo_mysql \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install gd \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

RUN addgroup -g $GID $UNAME \
    && adduser \
        --disabled-password \
        --gecos "" \
        --home "/$UNAME" \
        --ingroup "$UNAME" \
        --uid "$UID" \
        "$UNAME"

USER $UNAME:$UNAME

WORKDIR /var/www

# Build dev image
FROM prod as dev
USER root:root
RUN apk add --no-cache bash \
    && curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | sudo -E bash
RUN apk add symfony-cli

RUN apk --no-cache add  \
        pcre-dev  \
        linux-headers  \
        ${PHPIZE_DEPS} \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host = host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port = 9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.idekey = PHPSTORM" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

USER $UNAME:$UNAME