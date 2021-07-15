
FROM php:7.2-apache
RUN apt-get update && apt-get install git -y
RUN a2enmod rewrite \
 && sed -i 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/000-default.conf \
 && mv /var/www/html /var/www/public \
 && curl -sS https://getcomposer.org/installer \
  | php -- --install-dir=/usr/local/bin --filename=composer \
 && echo "AllowEncodedSlashes On" >> /etc/apache2/apache2.conf \
 && sed -i 's,:80,:8080,g' /etc/apache2/sites-available/000-default.conf \
 && echo "Listen 8080" > /etc/apache2/ports.conf
 

# Create group and user 1000
RUN getent group 1000 || groupadd web -g 1000 \
&& getent passwd 1000 || adduser --uid 1000 --gid 1000 --disabled-password --gecos "" web

RUN usermod -a -G web www-data

  
# Time Zone
RUN echo "memory_limit=512M" > $PHP_INI_DIR/conf.d/memory-limit.ini
RUN echo "date.timezone=UTC" > $PHP_INI_DIR/conf.d/date_timezone.ini

ENV APACHE_RUN_GROUP=web
ENV COMPOSER_HOME=/var/www/.composer

WORKDIR /var/www
