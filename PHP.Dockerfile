FROM php:fpm

RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    libzip-dev \
    zip

RUN docker-php-ext-install \
    pdo \ 
    pdo_mysql \
    curl \
    zip

RUN pecl install xdebug && docker-php-ext-enable xdebug

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /app

CMD [ "composer", "install" ]