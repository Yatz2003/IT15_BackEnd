mga buhaton negga

How to install
Download Php
Install Composer with php path
Php ( change php.ini extention ZIP, MYSQL)
Change Environment Variable
Open repo run composer install/update then cp .env.example .env
Run php artisan key:generate (if error: composer install)
Make empty database in Laragon and setup .env file with database
Migrate database: php artisan migrate:fresh
to seed database run :php artisan db:seed