# MFA

## System Requirements

    1. php^8.2
    2. Composer ^2.5
    3. mySql ^8.0

### Setup

#### Installing dependencies

    composer install

#### Generate the app key

    php artisan key:generate

#### Setting up the database

    cp .env.example .env

##### hint: Inside .env file change required credentials like

    DB_DATABASE=emis
    DB_USERNAME=root
    DB_PASSWORD=secret

##### After database setup migrate the migration files

    php artisan migrate --seed

Admin Login & Demo Team login can be found in UserTableSeeder

### Permission Management

##### Add new permissions inside Enum/Permissions.php and run command to update new permissions

    php artisan permissions:update
