
# Używamy obrazu Apache z PHP
FROM php:8.2-apache

# Kopiujemy pliki projektu do katalogu serwera
COPY . /var/www/html/

# Włączamy mod_rewrite dla ładnych URL-i (opcjonalnie)
RUN a2enmod rewrite \
    && sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/sites-available/000-default.conf

# Restartujemy Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80