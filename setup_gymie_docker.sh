#! /bin/bash

# Define environment variables
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=gymie
DB_USERNAME=gymie
DB_PASSWORD=password

# Define project path
PROJECT_PATH=$(pwd)

# Set permissions for the local project directory
echo "Setting permissions for the project directory..."
sudo chmod -R 777 $PROJECT_PATH

# Stop and remove containers, networks, volumes, and images created by `up`
echo "Stopping and removing existing containers, networks, and volumes..."
docker-compose down -v

# Build and start Docker containers
echo "Building and starting Docker containers..."
docker-compose up --build -d

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
MYSQL_READY=false
for i in {1..30}; do
    if docker-compose exec mysql mysqladmin ping -h"$DB_HOST" --silent; then
        MYSQL_READY=true
        break
    fi
    echo "MySQL is unavailable - sleeping ($i/30)"
    sleep 5
done

if [ "$MYSQL_READY" = false ]; then
    echo "MySQL failed to start."
    docker-compose logs mysql
    exit 1
fi

# Create necessary directories and set permissions
echo "Creating necessary directories and setting permissions..."
docker-compose exec app sh -c "mkdir -p /var/www/vendor && chown -R www-data:www-data /var/www/vendor"
docker-compose exec app sh -c "mkdir -p /var/www/storage && chown -R www-data:www-data /var/www/storage"
docker-compose exec app sh -c "mkdir -p /var/www/bootstrap/cache && chown -R www-data:www-data /var/www/bootstrap/cache"
docker-compose exec app sh -c "mkdir -p /var/www/html && chown -R www-data:www-data /var/www/html"

# Install composer dependencies
echo "Installing composer dependencies..."
docker-compose exec app composer install

# Copy .env.example to .env
echo "Copying .env.example to .env..."
cp .env.example .env

# Update .env with database details
echo "Updating .env with database details..."
sed -i "s/DB_CONNECTION=.*/DB_CONNECTION=$DB_CONNECTION/" .env
sed -i "s/DB_HOST=.*/DB_HOST=$DB_HOST/" .env
sed -i "s/DB_PORT=.*/DB_PORT=$DB_PORT/" .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_DATABASE/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USERNAME/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASSWORD/" .env

# Generate application key
echo "Generating application key..."
docker-compose exec app php artisan key:generate

# Run migrations and seed the database
echo "Running migrations and seeding the database..."
docker-compose exec app php artisan migrate --seed

# Set permissions for storage and cache inside container
echo "Setting permissions for storage and cache inside the container..."
docker-compose exec app sh -c "chown -R www-data:www-data storage bootstrap/cache && chmod -R ug+rwx storage bootstrap/cache"

# Modify php-fpm listen directive
echo "Modifying PHP-FPM listen directive..."
docker-compose exec app sh -c "sed -i 's/listen = 127.0.0.1:9000/listen = 0.0.0.0:9000/' /usr/local/etc/php-fpm.d/www.conf"

# Restart php-fpm to apply changes
echo "Restarting PHP-FPM to apply changes..."
docker-compose exec app sh -c "pkill -o -USR2 php-fpm"

# Create test PHP file
echo "Creating test PHP file..."
docker-compose exec app sh -c "echo '<?php phpinfo(); ?>' > /var/www/html/test.php"

# Add cron job for scheduled tasks
echo "Adding cron job for scheduled tasks..."
(crontab -l 2>/dev/null; echo "* * * * * cd $PROJECT_PATH && docker-compose exec app php artisan schedule:run >> /dev/null 2>&1") | crontab -

echo "Laravel Gymie setup complete. Use the following credentials to log in:"
echo "Email: admin@gymie.in"
echo "Password: password"
