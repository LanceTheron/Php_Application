FROM php:8.0-apache

COPY ./app /var/www/html/
COPY ./docker/php.ini /usr/local/etc/php/

# Enable Apache mod_rewrite
RUN a2enmod rewrite
