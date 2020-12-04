ARG PHP_VERSION
FROM php:${PHP_VERSION}-fpm

# composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-install pdo_mysql bcmath

# redis
RUN pecl install -o -f redis \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis

# apache bench
RUN apt-get update \
    && apt-get install -y apache2-utils

WORKDIR /var/www

EXPOSE 9000
