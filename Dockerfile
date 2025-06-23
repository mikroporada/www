
# Używamy obrazu Apache z PHP
FROM php:8.2-apache

# Zainstaluj wymagane rozszerzenia PHP
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-configure gd \
    && docker-php-ext-install \
        gd \
        pdo_mysql \
        zip \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

# Zainstaluj composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer

# Kopiujemy pliki projektu do katalogu serwera
COPY . /var/www/html/

# Włączamy mod_rewrite dla ładnych URL-i (opcjonalnie)
RUN a2enmod rewrite \
    && sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/sites-available/000-default.conf

# Konfiguracja Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Ustawienia uprawnień
RUN chown -R www-data:www-data /var/www/html

# Instalacja zależności
RUN composer install --no-dev --optimize-autoloader

EXPOSE 80

# Ustawienie katalogu roboczego
WORKDIR /var/www/html

# Uruchomienie Apache
CMD ["apache2-foreground"]