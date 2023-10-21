# Use the official PHP image with Apache
FROM php:apache

# Install the MySQLi extension
RUN docker-php-ext-install mysqli

# Copy your PHP application files to the container (if you have any)
# COPY /path/to/your/php/files /var/www/html

# Start the Apache web server
CMD ["apache2-foreground"]
