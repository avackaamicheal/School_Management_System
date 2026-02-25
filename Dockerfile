# Using the official PHP image with Apache
FROM php:8.3-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    nodejs \
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd zip

# Enable Apache mod_rewrite (REQUIRED for Laravel Routes)
RUN a2enmod rewrite

# Configure Apache Document Root
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Apache to read Laravel's .htaccess file ---
# Without this, clicking "Login" might just reload the page because routing is blocked.
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Setting the working directory
WORKDIR /var/www/html

# Copy application code
COPY . .

# --- Dummy variables for Build Process ---
ENV BROADCAST_DRIVER log
ENV PUSHER_APP_KEY build_key
ENV PUSHER_APP_ID build_id
ENV PUSHER_APP_SECRET build_secret
ENV PUSHER_HOST build_host
ENV PUSHER_PORT 443
ENV PUSHER_SCHEME https
ENV PUSHER_APP_CLUSTER mt1

# Installing PHP Dependencies
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Build Frontend Assets
RUN npm install
RUN npm run build

# Fix Permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Final Environment Setup
ENV APP_ENV production
ENV LOG_CHANNEL stderr

# Start Apache without migration
# CMD ["/bin/sh", "-c", "php artisan config:clear && php artisan route:clear && php artisan view:clear && apache2-foreground"]


# Start Apache with migration...The artisan migrate "--force" is crucial for production
CMD ["/bin/sh", "-c", "php artisan migrate --force && php artisan config:clear && php artisan route:clear && php artisan view:clear && apache2-foreground"]
