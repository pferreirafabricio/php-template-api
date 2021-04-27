FROM php:8.0.3-apache

RUN a2enmod rewrite

RUN docker-php-ext-install pdo pdo_mysql

RUN pecl install xdebug && docker-php-ext-enable xdebug

ADD . /var/www/html
