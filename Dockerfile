FROM php:8.1-apache

# Enable Apache modules (optional but safe)
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy all files into Apache's public directory
COPY . /var/www/html/

# Change default directory index to your PHP file
RUN echo "DirectoryIndex scoreboard\\ \\(1\\).php" > /etc/apache2/conf-enabled/directoryindex.conf

# Fix permissions (recommended)
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80
