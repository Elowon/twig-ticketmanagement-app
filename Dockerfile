# Use official PHP image with Apache
FROM php:8.2-apache

# Install system dependencies for Composer
RUN apt-get update && apt-get install -y unzip git && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Enable Apache mod_rewrite (needed for clean URLs)
RUN a2enmod rewrite

# Copy your app into the container
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Install PHP dependencies using Composer
RUN composer install

# Expose port 10000
EXPOSE 10000

# Start Apache server
CMD ["apache2-foreground"]
