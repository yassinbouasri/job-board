# Use PHP 8.2 FPM image
FROM php:8.2-fpm

# Set working directory inside container
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    zip \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    && docker-php-ext-install intl pdo pdo_pgsql zip

# Install Composer (latest version)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . .

# Set recommended PHP.ini settings
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini

# Permissions (optional, use with caution in production)
RUN chown -R www-data:www-data /var/www/html

# Expose port (not necessary for FPM, but good for info)
EXPOSE 9000

# Use the default PHP-FPM command
CMD ["php-fpm"]
