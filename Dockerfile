# Usar la imagen oficial de PHP con Apache
FROM php:8.2-apache

RUN docker-php-ext-install mysqli

# Copiar la aplicación al directorio raíz de Apache
COPY ./app/ /var/www/html/

EXPOSE 80
