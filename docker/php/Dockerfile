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

# Install Node.js
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash -
RUN apt-get install -y nodejs

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Clean up
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Create user and set permissions
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www
USER www

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory contents (done in compose file)
# Expose port 9000 for PHP-FPM and 5173 for Vite
EXPOSE 9000 5173
