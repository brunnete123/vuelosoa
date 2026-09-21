# Usar la imagen oficial de PHP con Apache
FROM php:8.2-apache

RUN rm -f /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf \
	&& a2enmod mpm_prefork

RUN docker-php-ext-install mysqli

# Copiar la aplicación al directorio raíz de Apache
COPY ./app/ /var/www/html/

EXPOSE 80

CMD ["sh", "-c", "rm -f /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf && a2enmod mpm_prefork && exec apache2-foreground"]
