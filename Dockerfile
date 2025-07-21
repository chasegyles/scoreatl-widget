FROM php:8.1-apache

# Copy your code into the container
COPY . /var/www/html/

# Expose port 80 (Apache default)
EXPOSE 80
