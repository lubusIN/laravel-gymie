
# Gymie - Gym Management System

## Overview


## Installation
To set up Gymie, follow these steps:
1. Clone the repository:
    `git clone git@github.com:lubusIN/laravel-gymie.git`
2. Create a `.env` file from the example file:
    `cp .env.example .env`
3. Update the `.env` file
    - Set your database credentials.  
    - Update other relevant configuration values.  
    - Set your application URL:
        
      ```env
      APP_URL=https://gymie.test
      ```
4.  Generate the application key:
    `php artisan key:generate`
5. Install dependencies:
    `composer install`
6. Run database migrations:

    ```bash
    php artisan migrate
    ```

    > **Note:** Running seeders can introduce a significant amount of data, which may affect performance, especially in production.  
    > To avoid potential issues, it is **advised to test seeders locally** or execute them in smaller batches in production.  
    > You can run a specific seeder using the following command:

    ```bash
    php artisan db:seed --class=WorldSeeder
    ```

    > For development or testing purposes, you can run all seeders at once (use with caution):

    ```bash
    php artisan db:seed
    ```
    > *(Optional)* Refresh and reseed the database (useful in development):

      ```bash
      php artisan migrate:refresh --seed
      ```
7. Create a symbolic link for storage:

    ```bash
    php artisan storage:link
    ```
8. Create a user to access the application:

    ```bash
    php artisan make:filament-user
    ```
9. Start the development server:
    `php artisan serve`
    Alternatively, you can use Laravel Herd:
    `herd`
10. *(Optional, for testing purposes)* Start the queue worker to process background jobs:

    ```bash
    php artisan queue:work
    ```

11. Ensure PHP Configuration
    - Locate your `php.ini` file and ensure the following setting is configured:
    
      ```ini
      memory_limit = 512M
      ```
## Tech Stack

* PHP & Laravel – Core framework
* Laravel Filament – Admin panel
* MySQL – Database
* Livewire – Interactive UI components
