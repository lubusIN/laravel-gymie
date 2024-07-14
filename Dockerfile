# syntax=docker/dockerfile:1
FROM php:7.1-fpm

# Set the working directory
WORKDIR /var/www

# Install necessary dependencies and diagnostic tools
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libmagickwand-dev --no-install-recommends \
    libonig-dev \
    nano \
    iputils-ping \
    procps \
    net-tools \
    iproute2 \
    lsof \
    strace \
    htop

# Clean up the apt cache to reduce the image size
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install necessary PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd tokenizer ctype json xml

# Install and enable the Imagick extension
RUN pecl install imagick && docker-php-ext-enable imagick

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy the content of the current directory to the working directory in the container
COPY . /var/www

# Set ownership and permissions for the copied files
COPY --chown=www-data:www-data . /var/www

# Create PHP-FPM log directory and set permissions
RUN mkdir -p /var/log/php-fpm && chown -R www-data:www-data /var/log/php-fpm

# Add custom PHP-FPM configurations
RUN echo "catch_workers_output = yes" >> /usr/local/etc/php-fpm.d/www.conf
RUN echo "pm = ondemand" >> /usr/local/etc/php-fpm.d/www.conf
RUN echo "pm.max_requests = 500" >> /usr/local/etc/php-fpm.d/www.conf
RUN echo "request_slowlog_timeout = 5s" >> /usr/local/etc/php-fpm.d/www.conf
RUN echo "slowlog = /var/log/php-fpm/www-slow.log" >> /usr/local/etc/php-fpm.d/www.conf
RUN echo "rlimit_files = 65535" >> /usr/local/etc/php-fpm.d/www.conf

# Set permissions for Laravel directories
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www/storage /var/www/bootstrap/cache

# Switch to the www-data user
USER www-data

# Expose port 9000 and start the PHP-FPM server
EXPOSE 9000
CMD ["php-fpm"]
