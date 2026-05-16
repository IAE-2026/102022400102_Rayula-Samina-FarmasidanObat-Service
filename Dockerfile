FROM php:8.2-cli

# Install dependency
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip

# Install extension MySQL
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set workdir
WORKDIR /app

# Copy composer dulu
COPY composer.json composer.lock ./

# Install dependency Laravel (dengan flag ignore platform)
RUN composer install --ignore-platform-reqs --no-scripts

# Copy semua file project
COPY . .

# Generate autoload
RUN composer dump-autoload --optimize --ignore-platform-reqs

# Permission
RUN chmod -R 777 storage bootstrap/cache

# Expose port
EXPOSE 8000

# Run Laravel
CMD php artisan serve --host=0.0.0.0 --port=8000