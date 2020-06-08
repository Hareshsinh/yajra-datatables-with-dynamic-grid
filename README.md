# yajra-datatables-with-dynamic-grid

## Installation

Please check the official laravel installation guide for server requirements before you start. [Official Documentation](https://laravel.com/docs/5.5/installation#installation)

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Install all the dependencies using composer

    composer install

Generate a new application key

    php artisan key:generate

Run migration for add database tables

    php artisan migrate --seed

for run the project

    php artisan ser

replace http://127.0.0.1:8000 to your domain

    http://127.0.0.1:8000/ - dynamic grid
    http://127.0.0.1:8000/users - dynamic grid with export information
    http://127.0.0.1:8000/drags - dynamic grid with ordering and resizing

