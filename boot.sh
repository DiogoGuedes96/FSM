#!/bin/bash

echo "Starting Booting Sequence..."
echo "Preparing the Environment..."
echo "Clearing Cached files..."
composer dump-autoload -o
php artisan optimize:clear

echo "Installing Main dependencies..."
npm install

echo "Installing modules dependencies..."
php artisan module:npm-i Calls
php artisan module:npm-i Primavera
php artisan module:npm-i Products
php artisan module:npm-i Clients
php artisan module:npm-i Orders
echo "Npm installed on all Modules"

php artisan module:composer-i Calls
php artisan module:composer-i Primavera
php artisan module:composer-i Products
php artisan module:composer-i Clients
php artisan module:composer-i Orders
echo "Composer installed on all Modules"


# echo "Initiating Asterisk sequence..."
# ./asterisk.sh
# echo "Asterisk sequences success."

echo "Boot sequence completed."
