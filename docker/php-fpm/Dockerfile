FROM php:8.1-fpm-alpine

RUN echo http://dl-cdn.alpinelinux.org/alpine/edge/community/ >> /etc/apk/repositories

RUN apk update

RUN apk add \
    unzip \
    git \
    openssl \
    libxml2-dev \
    php81-common \
    php81-mysqli \
    php81-pdo \
    php81-curl \
    php81-exif \
    php81-xml \
    php81-phar \
    php81-dom \
    php81-xmlreader \
    php81-ctype \
    php81-mbstring \
    php81-gd \
    php81-redis \
    php81-opcache \
    $PHPIZE_DEPS

RUN docker-php-ext-install mysqli pdo opcache pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app
