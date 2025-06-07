FROM php:8.1-apache

RUN a2enmod rewrite headers

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN apt-get update && apt-get install -y \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

RUN echo "short_open_tag = On" >> /usr/local/etc/php/conf.d/short-tags.ini

COPY ./src/ /var/www/html/

RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
