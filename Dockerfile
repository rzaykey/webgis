FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

RUN apt-get update && apt-get install -y sudo \
    && echo "www ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers

# Install Node.js dan NPM
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs && \
    npm install -g npm@latest

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Clean up
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Create user and set permissions
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www
RUN mkdir -p /var/www/html/vendor
RUN chown -R www:www /var/www/html

# Set proper permissions for Laravel
RUN mkdir -p /var/www/html/storage/logs /var/www/html/bootstrap/cache && \
    chown -R www:www /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
# Copy .env jika ada
COPY .env.example /var/www/html/.env
RUN chown www:www /var/www/html/.env && \
    chmod 644 /var/www/html/.env
USER www

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory contents (done in compose file)
# Expose port 9000 for PHP-FPM and 5173 for Vite
EXPOSE 9000 5173
