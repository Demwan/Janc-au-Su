FROM php:8.1-apache

# Install required PHP extensions (including pdo_mysql, etc.)
RUN docker-php-ext-install pdo pdo_mysql

# Copy your project files
COPY . /var/www/html/

# Copy the entrypoint script and give it execute permissions
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

# Set the entrypoint to generate .env file before starting Apache
ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]
