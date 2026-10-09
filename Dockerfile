FROM php:8.2-apache

# Install PDO MySQL so native PHP can connect to the MySQL service.
RUN docker-php-ext-install pdo_mysql \
    && a2enmod rewrite

COPY . /var/www/html/

EXPOSE 80
