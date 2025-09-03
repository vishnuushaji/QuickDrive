FROM php:8.2-apache

WORKDIR /var/www/html

# Install dependencies with PostgreSQL SSL support
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev libzip-dev zip unzip \
    libpq-dev postgresql-client ca-certificates \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip \
    && apt-get clean

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy application
COPY . .

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Apache config
RUN echo '<VirtualHost *:${PORT}>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

EXPOSE 80

# Start script
RUN echo '#!/bin/bash\n\
echo "Starting application..."\n\
\n\
# Export database variables\n\
export DB_CONNECTION=pgsql\n\
export DB_HOST=ep-blue-fire-adpperjk-pooler.c-2.us-east-1.aws.neon.tech\n\
export DB_PORT=5432\n\
export DB_DATABASE=neondb\n\
export DB_USERNAME=neondb_owner\n\
export DB_PASSWORD=npg_0CUOjSHGfdh1\n\
export DB_SSLMODE=require\n\
\n\
# Test connection\n\
echo "Testing database connection..."\n\
php artisan db:show || echo "Database connection test failed"\n\
\n\
# Run migrations\n\
echo "Running migrations..."\n\
php artisan migrate --force || echo "Migrations failed"\n\
\n\
# Create Sanctum tables if missing\n\
echo "Publishing Sanctum migrations..."\n\
php artisan vendor:publish --provider="Laravel\\\\Sanctum\\\\SanctumServiceProvider" --force\n\
php artisan migrate --force\n\
\n\
# Seed the database with UserSeeder only\n\
echo "Seeding database with users..."\n\
php artisan db:seed --class=UserSeeder --force || echo "User seeding completed or failed"\n\
\n\
# Clear and cache\n\
echo "Clearing and caching config..."\n\
php artisan config:clear\n\
php artisan config:cache\n\
\n\
# Set Apache port\n\
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf\n\
\n\
echo "Application setup complete! Starting Apache..."\n\
echo "================================="\n\
echo "Default login credentials:"\n\
echo "Admin: admin@quickdrive.com / password123"\n\
echo "Developer: developer@quickdrive.com / password123"\n\
echo "Client: client@quickdrive.com / password123"\n\
echo "================================="\n\
# Start Apache\n\
apache2-foreground' > /start.sh && chmod +x /start.sh

CMD ["/start.sh"]