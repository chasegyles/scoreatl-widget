FROM php:8.1-apache

# Enable useful Apache modules
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy files to Apache's root
COPY . /var/www/html/

# Make sure the file has the correct permissions
RUN chmod -R 755 /var/www/html && \
    chown -R www-data:www-data /var/www/html

# Set DirectoryIndex to your PHP file with escaped parentheses
RUN echo "DirectoryIndex scoreboard\\ \\(1\\).php" > /etc/apache2/conf-enabled/directoryindex.conf

# Allow access to the root directory
RUN echo "<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>" > /etc/apache2/conf-enabled/permissions.conf

EXPOSE 80
