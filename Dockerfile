# Use official PHP image with Apache
FROM php:8.2-apache

# Install system dependencies for Composer
RUN apt-get update && apt-get install -y unzip git curl && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Enable Apache modules
RUN a2enmod rewrite

# Copy app
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Install PHP dependencies
RUN composer install

# Configure Apache
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's|<Directory /var/www/>|<Directory /var/www/html/public>|g' /etc/apache2/apache2.conf

# Allow .htaccess to work
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Use Render port
ENV PORT 10000
RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# Expose port
EXPOSE 10000

# Start Apache
CMD ["apache2-foreground"]
