# Build stage
FROM php:8.3-apache AS build

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www/html

COPY ./ ./

# Permission for apache user
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

RUN a2enmod rewrite

COPY apache.conf /etc/apache2/sites-available/000-default.conf
COPY php.ini /usr/local/etc/php/



# Development stage
FROM build AS development

# Install Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Start Apache in foreground
CMD ["apache2-foreground"]



# Production stage
FROM build AS production

# Start Apache in foreground
CMD ["apache2-foreground"]