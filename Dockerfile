# Build stage
FROM php:8.3-apache AS build

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpq-dev \
    wget \
    git \
    unzip \
    libxml2-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql

# Copy application files & configurations
COPY ./ /var/www
COPY apache.conf /etc/apache2/sites-available/000-default.conf
COPY php.ini /usr/local/etc/php/

# Enable Apache modules
RUN a2enmod rewrite

WORKDIR /var/www

# Set permissions
RUN chown -R www-data:www-data /var/www/
RUN chmod 777 public


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