FROM php:8.0.3-apache

RUN a2enmod rewrite

RUN docker-php-ext-install pdo pdo_mysql

RUN yes | pecl install xdebug \
    docker-php-ext-enable xdebug \
    && echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo “xdebug.remote_autostart=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo “xdebug.remote_host=host.docker.internal” >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo “xdebug.remote_port=9000" >> /usr/local/etc/php/conf.d/xdebug.ini

ADD . /var/www/html

# COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# CMD [ "composer", "install" ]