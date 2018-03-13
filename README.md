<p  align="center"><img  src="https://user-images.githubusercontent.com/1039236/36820389-964422c0-1d13-11e8-8dac-d58014f59c24.png"></p>

<p align="center">
<a href="https://github.com/lubusIN/laravel-gymie/releases"><img src="https://img.shields.io/github/release/lubusIN/laravel-gymie.svg?style=flat-square" alt="Latest Stable Version"></a>
<a href="https://scrutinizer-ci.com/g/lubusIN/laravel-gymie/build-status/master"><img src="https://img.shields.io/scrutinizer/build/g/lubusIN/laravel-gymie.svg?style=flat-square" alt="Build Status"></a>
<a href="https://styleci.io/repos/123349662"><img src="https://styleci.io/repos/123349662/shield" alt="StyleCI Status"></a>
<a href="https://scrutinizer-ci.com/g/lubusIN/laravel-gymie"><img src="https://img.shields.io/scrutinizer/g/lubusin/laravel-gymie.svg?style=flat-square" alt="Scrutinizer Code Quality"></a>
<a href="https://github.com/lubusIN/laravel-gymie/blob/master/LICENSE.md"><img src="https://img.shields.io/badge/License-MIT-brightgreen.svg?style=flat-square" alt="License"></a>
<a href="https://github.com/lubusin/laravel-gymie/blob/master/contributing.md"><img src="https://img.shields.io/badge/PRs-welcome-brightgreen.svg?style=flat-square" alt="PRs"></a>
</p>

----------

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
-  JSON PHP Extension
- GD PHP Extension
- Imagick PHP Extension 

***Note:***
Improper permission on `storage` & `public` folder will lead to server & application errors

##  Installation
1. Clone to your server root `git clone git@github.com:lubusIN/laravel-gymie.git`
2. Run `composer install` to install all dependencies
3. Create .env in application root `touch .env`
4. Add your database details & optional sentry DNS
```
DB_HOST= [HOST]
DB_DATABASE=[DBHOST]
DB_USERNAME=[USERNAME]
DB_PASSWORD= [PASSWORD]
SENTRY_DSN= [SENTRYDNS]
```
5. Run `php artisan key:generate` to generate key
6. Run `php artisan migrate --seed` to install the database & required data
7. All done! use the following credentials to log in
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
Please see [CHANGELOG](https://github.com/spatie/laravel-medialibrary/blob/master/CHANGELOG.md) for more information what has changed recently.

## Contributing
Please see [CONTRIBUTING](https://github.com/spatie/laravel-medialibrary/blob/master/CONTRIBUTING.md) for details.

##  Security Vulnerabilities
If you discover a security vulnerability within Laravel, please send an e-mail at info@lubus.in. All security vulnerabilities will be promptly addressed.  

##  Support Us
[LUBUS](http://lubus.in) is a web design agency based in Mumbai, India.

You can pledge on [patreon](https://www.patreon.com/lubus) to support the development & maintenance of Gymie and other [opensource](https://github.com/lubusIN/) stuff we are building.

##  License
Gymie is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
