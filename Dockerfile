FROM php:8.2-apache

# Cài các extension cần thiết cho Laravel
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev libonig-dev libxml2-dev \
    netcat-openbsd \
    && docker-php-ext-install pdo pdo_mysql zip

# Cài Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Bật mod_rewrite cho Apache
RUN a2enmod rewrite

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Sao chép source Laravel vào container
COPY . /var/www/html

WORKDIR /var/www/html

# Cấp quyền cho storage và bootstrap
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Cài thư viện PHP qua Composer
RUN composer install --no-interaction --prefer-dist --optimize-autoloader



# Copy script entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
