FROM php:7.4-apache

RUN apt update && apt-get install -y git libzip-dev
RUN docker-php-ext-install pdo_mysql zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
ENV COMPOSER_ALLOW_SUPERUSER 1

RUN a2enmod rewrite

WORKDIR /var/www

# Install opcache apcu
RUN docker-php-ext-install opcache
RUN pecl install apcu
RUN docker-php-ext-enable apcu

# Install Node.js
RUN curl -sL https://deb.nodesource.com/setup_10.x | bash -
RUN apt-get install -y nodejs
