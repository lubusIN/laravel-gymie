# Gymie - Gym Management System

## Overview

Gymie is a web application specifically designed for gyms to efficiently manage their members, staff, payments, and attendance tracking. It enables fitness centers to streamline internal operations, handle member data effectively, and gain real-time access to critical information. By improving administrative workflows and enhancing the member experience, Gymie supports the automation and digital transformation of gym management.

## Requirements

- PHP >= 8.1
- Laravel Framework ^10.0
- Filament Admin Panel 3.x
- Livewire ^3.0
- nnjeim/world ^1.1
- barryvdh/laravel-dompdf ^3.1
- spatie/laravel-permission ^6.0
- bezhansalleh/filament-shield ^3.3.6
- Laravel Herd *(optional for local development)*

## Installation

To set up Gymie, follow these steps:

### 1. Clone the Repository

Clone the repository to your local system:

```bash
git clone git@github.com:lubusIN/laravel-gymie.git
```

### 2. Go to folder

```bash
cd laravel-gymie
```

### 3. Install dependencies
```bash
composer install
```

### 3. Create a `.env` file from the example file:
   `cp .env.example .env`

### 4. Update the `.env` file
    - Set your database credentials.
    - Update other relevant configuration values.
    - Set your application URL:
        ```env
        APP_URL=https://gymie.test
        ```
### 5. Generate the application key:
   `php artisan key:generate`

### 6. Run database migrations:

    ```bash
    php artisan migrate
    ```

    > For development or testing purposes, you can run all seeders at once (use with caution):

    ```bash
    php artisan db:seed
    ```

    > _(Optional)_ Refresh and reseed the database (useful in development):

    ```bash
    php artisan migrate:refresh --seed
    ```

### 7. Create a symbolic link for storage:

    ```bash
    php artisan storage:link
    ```

### 8. Create a user to access the application:

    ```bash
    php artisan make:filament-user
    ```

### 9. Assigning Super Admin Role

    To grant full access to the admin panel, run the following command with the email of an existing user:

    ```bash
    php artisan shield:super-admin user@example.com
    ```

## Troubleshooting

**Memory Errors**

Ensure PHP has enough memory allocated. Edit your php.ini:

```ini
memory_limit = 512M
```
**Seeder Performance**

Seeders (like WorldSeeder) can add significant data and slow down performance. For production, avoid full seeding and run only necessary seeders:

```bash
php artisan db:seed --class=WorldSeeder
```

## Development

### 1. Start the development server:
```bash
php artisan serve
```
Or with Laravel Herd:
```bash
herd
```

### 2. Start the queue worker

To process background jobs:

```bash
php artisan queue:work
```

### 3. Start the Laravel scheduler 

```bash
php artisan schedule:work
```
> [!NOTE]
> The scheduler must be running continuously to trigger time-based tasks (e.g., status updates).
> 
> If those tasks dispatch queued jobs (like import/export or notifications), then the queue worker must also be running to process them.

## Meet Your Artisans

[LUBUS](http://lubus.in) is a web design agency based in Mumbai.

<img src="https://user-images.githubusercontent.com/1039236/40877801-3fa8ccf6-66a4-11e8-8f42-19ed4e883ce9.png" />
