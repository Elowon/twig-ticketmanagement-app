# Use official PHP image with Apache
FROM php:8.2-apache

# Install system dependencies for Composer
RUN apt-get update && apt-get install -y unzip git && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Enable Apache mod_rewrite (needed for clean URLs)
RUN a2enmod rewrite

# Copy the entire app into the container
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Make Apache serve files from 'public' folder
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's|<Directory /var/www/>|<Directory /var/www/html/public>|g' /etc/apache2/apache2.conf

# Install PHP dependencies using Composer
RUN composer install

# Expose port 10000 (Render uses this)
EXPOSE 10000

# Start Apache in the foreground
CMD ["apache2-foreground"]
