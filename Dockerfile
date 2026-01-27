FROM php:8.2-apache

# Active mod_rewrite
RUN a2enmod rewrite

# Installe l'extension MySQL pour PHP
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copie ton code dans Apache
COPY src/ /var/www/html/
COPY db/ /var/www/db/
COPY migrate.php /var/www/

# Donne les bons droits
RUN chown -R www-data:www-data /var/www/html

CMD ["apache2-foreground"]
