<p  align="center"><img  src="https://user-images.githubusercontent.com/1039236/36820389-964422c0-1d13-11e8-8dac-d58014f59c24.png"></p>

<p align="center">
<a href="https://github.com/lubusIN/laravel-gymie/releases"><img src="https://img.shields.io/github/release/lubusIN/laravel-gymie.svg?style=flat-square" alt="Latest Stable Version"></a>
<a href="https://github.com/lubusIN/laravel-gymie/blob/v1/LICENSE.md"><img src="https://img.shields.io/badge/License-MIT-brightgreen.svg?style=flat-square" alt="License"></a>
<a href="https://github.com/lubusin/laravel-gymie/blob/v1/contributing.md"><img src="https://img.shields.io/badge/PRs-welcome-brightgreen.svg?style=flat-square" alt="PRs"></a>
</p>

> [!WARNING]
> **v1 is no longer maintained.**
> ⚡ v3 is currently in **Alpha**, and we are preparing for the upcoming stable release.

<center>
<a href="https://lubus.in/">
<img src="https://user-images.githubusercontent.com/1039236/40877801-3fa8ccf6-66a4-11e8-8f42-19ed4e883ce9.png" />
</a>
</center>

# Gymie

Laravel based web application for gym & club management. Currently being used by many fitness centers. For more information, visit - https://www.gymie.in
 
![gymie-device-mockup](https://user-images.githubusercontent.com/1039236/36820312-3f709262-1d13-11e8-8ee6-0529120b8ac1.png)

  

> ***Note:***
>
> Currently, we are in the process of polishing the code to be ready for general use. Check [issues](https://github.com/lubusIN/laravel-gymie/issues) & [milestone](https://github.com/lubusIN/laravel-gymie/milestones) to know more about upcoming changes, features and improvements.

## Requirements
- PHP >= 7.1.3
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- Ctype PHP Extension
- JSON PHP Extension
- GD PHP Extension
- Imagick PHP Extension 

***Note:***
Improper permission on `storage` & `public` folder will lead to server & application errors

##  Installation
1. Clone to your server root `git clone -b v1 git@github.com:lubusIN/laravel-gymie.git`
> For faster updates and bleeding edge features, or if you want to help test the next version, use the `develop` branch instead of the `v1` branch.
2. Run `composer install` to install all dependencies
3. Create `.env` in application root 
```cp .env.example .env```
4. Update database details and optional sentry DNS in `.env`
5. Run `php artisan key:generate` to generate key
6. Run `php artisan migrate --seed` to install the database & required data
7. Add cron entry for scheduled task to update status for various modules (subscription expiration etc)
```
* * * * * cd /path-to-gymie && php artisan schedule:run >> /dev/null 2>&1
```
For more info: https://laravel.com/docs/5.7/scheduling#introduction

8. All right sparky! 

use the following credentials to log in
```
email: admin@gymie.in
password: password
```

## Troubleshooting

**APP_KEY not getting added to .env**
- Add APP_KEY to .env
- Copy generated key from terminal

**Permission / 500 Internal Server Error**

Change permission on storage & cache
```
sudo chgrp -R www-data storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache
``` 

## Live Demo

Test drive the application without going through the hassel of installation.

```
url: https://demo.gymie.in
email: admin@gymie.in
password: password
```

## Changelog

Please see the [Changelog](CHANGELOG.md) 

## Contributing

Thank you for considering contributing to the `Gymie`. You can read the contribution guide lines [here](CONTRIBUTING.md)

Check the development tasklist [here](https://github.com/lubusIN/laravel-gymie/projects/1) if something interest you or suggest something [here](https://github.com/lubusIN/laravel-gymie/issues)

##  Security Vulnerabilities
If you discover a security vulnerability within Laravel, please send an e-mail at info@lubus.in. All security vulnerabilities will be promptly addressed.  

##  Support Us

<a href="https://www.patreon.com/lubus">
<img src="https://c5.patreon.com/external/logo/become_a_patron_button.png" alt="Become A Patron"/>
</a>

[LUBUS](http://lubus.in) is a web design agency based in Mumbai, India.

You can pledge on [patreon](https://www.patreon.com/lubus) to support the development & maintenance of various [opensource](https://github.com/lubusIN/) stuff we are building.

## License

`Gymie` is open-sourced software licensed under the [MIT](LICENSE)

