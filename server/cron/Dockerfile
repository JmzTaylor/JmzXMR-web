FROM php:7-fpm-alpine
RUN docker-php-ext-install mysqli pdo pdo_mysql
CMD sleep 30; php /code/cron.php
