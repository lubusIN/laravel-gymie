# Gymie - Gym Management System

## Overview

Gymie is a web application specifically designed for gyms to efficiently manage their members, staff, payments, and attendance tracking. It enables fitness centers to streamline internal operations, handle member data effectively, and gain real-time access to information. By improving administrative workflows and enhancing the member experience, Gymie supports the automation and digital transformation of gym management.

## Requirements

- PHP >= 8.1
- Laravel Framework ^10.0
- Filament Admin Panel 3.x
- Livewire ^3.0
- nnjeim/world ^1.1
- barryvdh/laravel-dompdf ^3.1
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

### 4. Prepare the environment
Run the following script to prepare your environment:

```bash
composer run prepare-env
```
This will:
- Copy `.env.example` to `.env` (if missing)
- Clear config cache
- Generate application key
- Create a symbolic link to the storage folder

### 5. Configure the `.env` file
- Set your database credentials.  
- Update other relevant configuration values.  
- Set your application URL:
    
  ```env
  APP_URL=https://laravel-gymie.test
  ```

### 6. Database Setup
You can set up the database in one of two ways, depending on your requirements:

**Option 1: Blank Setup (Recommended for Production)**

Run the following command:

```bash
composer run setup
```
> [!NOTE]
> This command will prompt you to create an admin user via the terminal.

This will:
- Set up the environment (.env, app key, storage link)
- Run a fresh migration to create database tables
- Seed the world data (countries, states, cities)
- Create a default Filament admin user

**Option 2: Demo Setup**

If you want to explore the system with all demo data preloaded, use:

```bash
composer run setup-demo
```
This command will:
- Reset the database
- Seed all available demo data
- Prepare the environment automatically

> [!CAUTION]
> This process will erase all existing data. Use it only in a local or demo environment.

Login credentials:
```bash
Email: test@example.com
Password: test
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
