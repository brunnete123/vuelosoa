# Usar la imagen oficial de PHP con Apache
FROM php:8.2-apache

RUN a2dismod mpm_event mpm_worker mpm_worker2 2>/dev/null || true \
	&& a2enmod mpm_prefork

RUN docker-php-ext-install mysqli

# Copiar la aplicación al directorio raíz de Apache
COPY ./app/ /var/www/html/

EXPOSE 80
