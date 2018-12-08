# MyBlogUltimate by Antoine DEGUIN

## Installation

run ``` composer install ```

(You need to have [composer](https://getcomposer.org/) installed)

## Database

You have to create a database and change your 'DATABASE_URL' in your '.env' file.

And you can generate the database : ``` php bin/console doctrine:schema:update --force ```

Once the database is generated, you can load the users fixtures with different roles.

``` php bin/console doctrine:fixtures:load ```

## Server

And you just need to run this command for launching the server :
``` php bin/console server:run ```

## Connection

To connect with the fixtures's users:

| Username | Password | ROLES        |
| -------- | -------- | ------------ |
| user     | user     | ROLE_USER    |
| blogger  | blogger  | ROLE_BLOGGER |
| admin    | admin    | ROLE_ADMIN   |
