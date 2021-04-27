FROM php:8.0-cli
COPY . /usr/src/php-api
WORKDIR /usr/src/php-api
CMD [ "php", "-S", "localhost:8000", "index.php" ]