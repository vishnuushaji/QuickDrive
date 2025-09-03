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

# Enable Apache mod_rewrite (remove headers module)
RUN a2enmod rewrite

# Copy composer files + artisan first for caching
COPY composer.json composer.lock artisan ./

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev --no-scripts

# Copy the rest of the application
COPY . .

# Run package discovery
RUN php artisan package:discover --ansi || true

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Install Node dependencies and build assets
RUN npm install && npm run build

# Enhanced Apache configuration with CORS headers
RUN echo '<VirtualHost *:${PORT}>\n\
    DocumentRoot /var/www/html/public\n\
    \n\
    # Enable CORS headers\n\
    Header always set Access-Control-Allow-Origin "https://quickdrive-1.onrender.com"\n\
    Header always set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS, PATCH"\n\
    Header always set Access-Control-Allow-Headers "Accept, Authorization, Content-Type, X-Requested-With, X-CSRF-TOKEN, X-XSRF-TOKEN"\n\
    Header always set Access-Control-Allow-Credentials "true"\n\
    \n\
    # Handle preflight requests\n\
    RewriteEngine On\n\
    RewriteCond %{REQUEST_METHOD} OPTIONS\n\
    RewriteRule ^(.*)$ $1 [R=200,L]\n\
    \n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
        \n\
        # Laravel pretty URLs\n\
        RewriteEngine On\n\
        RewriteCond %{REQUEST_FILENAME} !-d\n\
        RewriteCond %{REQUEST_FILENAME} !-f\n\
        RewriteRule ^ index.php [L]\n\
    </Directory>\n\
    \n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Expose port
EXPOSE 80
ENV PORT=80

# Enhanced startup script
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
echo "Starting Laravel application..."\n\
\n\
# Generate app key if missing\n\
if [ ! -f .env ] || ! grep -q "APP_KEY=" .env; then\n\
    echo "Setting up environment..."\n\
    cp .env.example .env 2>/dev/null || true\n\
    php artisan key:generate --force\n\
fi\n\
\n\
# Wait for database (if using external DB)\n\
echo "Waiting for database connection..."\n\
php artisan tinker --execute="DB::connection()->getPdo(); echo \"Database connected successfully\\n\";" || echo "Database not ready yet, continuing..."\n\
\n\
# Run database migrations\n\
echo "Running migrations..."\n\
php artisan migrate --force || echo "Migration failed, continuing..."\n\
\n\
# Clear and optimize Laravel\n\
echo "Optimizing Laravel..."\n\
php artisan config:clear\n\
php artisan route:clear\n\
php artisan view:clear\n\
php artisan cache:clear\n\
\n\
php artisan config:cache || echo "Config cache failed"\n\
php artisan route:cache || echo "Route cache failed"\n\
php artisan view:cache || echo "View cache failed"\n\
\n\
# Set Apache port\n\
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf\n\
\n\
echo "Starting Apache server on port ${PORT}..."\n\
# Start Apache\n\
apache2-foreground' > /usr/local/bin/start.sh

RUN chmod +x /usr/local/bin/start.sh

CMD ["/usr/local/bin/start.sh"]