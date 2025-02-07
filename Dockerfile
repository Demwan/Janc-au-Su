# Use the official PHP image with Apache
FROM php:8.1-apache

# Install the PDO and PDO MySQL extensions
RUN docker-php-ext-install pdo pdo_mysql

# Copy your project files into the container’s web root
COPY . /var/www/html/

# Expose port 80 (Apache)
EXPOSE 80
