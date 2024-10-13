# Build stage
FROM php:8.2-apache AS builder
# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql
# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Production stage
FROM builder AS production
WORKDIR /var/www/html
# Copy application files
COPY src/ .
# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Development stage
FROM builder AS development
WORKDIR /var/www/html
# Install development tools
RUN pecl install xdebug && docker-php-ext-enable xdebug
# Copy application files
COPY src/ .
# Set permissions
RUN chown -R www-data:www-data /var/www/html