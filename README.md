# MyBlogUltimate by Antoine DEGUIN

## Installation

run ``` composer install ```

(You need to have [composer](https://getcomposer.org/) installed)

## Database

You have to create a database and change your 'DATABASE_URL' in your '.env' file.

And you can generate the database : ``` php bin/console doctrine:schema:update --force ```

Once the database is generated, you can load the users fixtures with different roles.

``` php bin/console doctrine:fixtures:load ```

To connect with users fixtures:

| Username | Password | ROLES        |
| -------- | -------- | ------------ |
| user     | user     | ROLE_USER    |
| blogger  | centered | ROLE_BLOGGER |
| admin    | admin    | ROLE_ADMIN   |