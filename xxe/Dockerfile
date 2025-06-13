FROM php:8.1-apache

RUN apt-get update && apt-get install -y \
    libxml2-dev \
    && docker-php-ext-install xml

RUN a2enmod rewrite

WORKDIR /var/www/html

COPY src/ .

RUN if [ -f process.php ]; then mv process.php import.php; fi

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

RUN mkdir -p /opt/system/flags /var/log/internal /etc/system/config /opt/system/backup \
    && echo "Flag{Bingo_bango_bongo_bish_bash_bosh}" > /flag.txt \
    && echo "admin_key: 7f9c8e6d4a2b1f5e" > /etc/system/config/secrets.conf \
    && echo "backup_location: /var/backup/db_dump.sql" > /var/log/internal/system.log \
    && touch /opt/system/flags/app_flag.txt \
    && touch /opt/system/backup/credentials.txt \
    && chmod 644 /opt/system/flags/app_flag.txt /opt/system/backup/credentials.txt

EXPOSE 4001

CMD ["apache2-foreground"]
