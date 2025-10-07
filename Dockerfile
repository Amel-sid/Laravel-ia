# ==============================================================================
# Stage 1: Base image avec dépendances
# ==============================================================================
FROM php:8.2-fpm as base

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
    nodejs \
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions (ajout de zip)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Create necessary directories
RUN mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Start PHP-FPM
# ==============================================================================
# Stage 2: Production image
# ==============================================================================
FROM base as production

# Copier seulement les fichiers nécessaires
COPY --chown=www-data:www-data . .

# Optimisations production
RUN composer install --no-dev --optimize-autoloader --no-scripts && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Sécurité : utilisateur non-root
USER www-data

# Exposer le port
EXPOSE 9000

# Démarrage
CMD ["php-fpm"]

# ==============================================================================
# Stage 3: Development image (default)
# ==============================================================================
FROM base as development

# Installer les dépendances de dev
RUN composer install --dev

CMD ["php-fpm"]
