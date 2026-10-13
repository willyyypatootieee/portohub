FROM php:8.2-apache

# Install PDO MySQL so native PHP can connect to the MySQL service.
RUN docker-php-ext-install pdo_mysql \
    && a2enmod rewrite

COPY php/uploads.ini /usr/local/etc/php/conf.d/uploads.ini

# Pastikan folder upload bisa ditulis www-data meski di-bind mount dari host.
COPY docker/entrypoint.sh /usr/local/bin/portfoliohub-entrypoint
RUN chmod +x /usr/local/bin/portfoliohub-entrypoint

# =============================================================================
# Web root diarahkan ke folder public/.
#
# Semua route (landing, login, studio, portfolio) berada di public/, sedangkan
# kode backend (app/, views/, database/, docs/) tetap di /var/www/html dan
# TIDAK dapat diakses langsung dari browser.
# =============================================================================
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
        /etc/apache2/sites-available/*.conf \
        /etc/apache2/apache2.conf \
        /etc/apache2/conf-available/*.conf

# Aset statis (CSS, JS, gambar, upload) berada di luar public/ agar rapi.
# Alias ini membuat URL /assets/... tetap dapat diakses browser.
COPY docker/assets-alias.conf /etc/apache2/conf-available/portfoliohub-assets.conf
RUN a2enconf portfoliohub-assets

COPY . /var/www/html/

EXPOSE 80

ENTRYPOINT ["portfoliohub-entrypoint"]
CMD ["apache2-foreground"]
