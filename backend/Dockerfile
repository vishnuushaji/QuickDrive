# Use PHP 8.2 with Apache
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev libzip-dev zip unzip \
    nodejs npm postgresql-client libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy composer files + artisan first for caching & package discovery
COPY composer.json composer.lock artisan ./

# Install PHP dependencies (disable scripts first)
RUN composer install --optimize-autoloader --no-dev --no-scripts

# Copy the rest of the application
COPY . .

# Run package discovery after full app is present (with error handling)
RUN php artisan package:discover --ansi || true

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Install Node dependencies and build assets
RUN npm install && npm run build

# Apache configuration
RUN echo '<VirtualHost *:${PORT}>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Expose port
EXPOSE 80
ENV PORT=80

# Startup script
RUN echo '#!/bin/bash\n\
# Generate app key if missing\n\
if [ ! -f .env ]; then\n\
    cp .env.example .env\n\
    php artisan key:generate\n\
fi\n\
\n\
# Run database migrations\n\
php artisan migrate --force || echo "Migration failed, continuing..."\n\
\n\
# Seed the database\n\
php artisan db:seed --force || echo "Seeding failed, continuing..."\n\
\n\
# Clear and cache config\n\
php artisan config:cache || echo "Config cache failed, continuing..."\n\
php artisan route:cache || echo "Route cache failed, continuing..."\n\
php artisan view:cache || echo "View cache failed, continuing..."\n\
\n\
# Set Apache port\n\
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf\n\
\n\
# Start Apache\n\
apache2-foreground' > /usr/local/bin/start.sh

RUN chmod +x /usr/local/bin/start.sh

CMD ["/usr/local/bin/start.sh"]
