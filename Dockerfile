FROM wordpress:php7.4-fpm-alpine

RUN docker-php-ext-install mysqli pdo pdo_mysql
