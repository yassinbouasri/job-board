# Use PHP 8.2 FPM Alpine image (smaller and more secure)
FROM php:8.2-fpm-alpine

# Set working directory inside container
WORKDIR /var/www/html

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    git \
    unzip \
    postgresql-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    curl \
    openssl \
    busybox-extras \  # Includes telnet
    bind-tools \      # Includes dig, nslookup
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