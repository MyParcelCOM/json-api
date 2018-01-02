FROM composer:1.5.1

RUN apk add --no-cache --update --virtual alpine-sdk \
    && apk add --no-cache --update --virtual autoconf \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apk del autoconf \
    && apk del alpine-sdk \
    && rm -rf /tmp/*
