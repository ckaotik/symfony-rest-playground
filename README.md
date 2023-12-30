# Symfonx Rest API

A simple example project for a Symfony-based REST API.

## Setup

1. Create your `.env` file based on the provided `.env.example`.
2. Start the containers using `docker compose up --build -d`
3. Add `symfony.localhost` to your `etc/hosts` file.
4. From here on, all commands should be run inside of the `php` service.
4. Setup database structure: Run `php bin/console doctrine:migrations:migrate` inside the `php` service.

The project can be reached under https://symfony.localhost, and PHPmyAdmin is available under http://symfony.localhost:8080.

### Tests

1. Create a test database:
  `DATABASE_URL=mysql://root:root@db:3306/symfony php bin/console doctrine:database:create --env=test`
2. Grant permissions:
  `DATABASE_URL=mysql://root:root@db:3306/symfony php bin/console doctrine:query:sql --env=test "GRANT ALL PRIVILEGES ON symfony_test.* TO 'symfony'@'%';"`
2. Install tables:
  `php bin/console doctrine:migrations:migrate -n --env=test`
3. Install fixtures:
  `php bin/console --env=test doctrine:fixtures:load`
4. Run tests using `php bin/phpunit`

## Structure

This repository contains
- a docker environment
- a simple REST API
- some frontend for consuming the API

A production setup should of course separate these, to allow for individual URLs, hosting and scaling. Especially, since the REST API requires a lot fewer dependencies.

## FAQ

- Q: How can I use console commands?  
  A: You can connect to the PHP container using `docker compose exec -it php /bin/bash`. When using Git for Windows, use `//bin/bash` (with an extra slash).
- Q: I need some data to use this!  
  A: Use the default UI or add your own using the `db` container or [PHPMyAdmin](http://symfony.localhost:8080), or run `php bin/console doctrine:query:sql "$(< import/product.sql)"` to import some demo products.
- Q: How do I ensure my changes conform to the repo code standards?  
  A: Some linters and sniffers are available inside of the symfony project:
    - `./vendor/bin/phpcs --standard=PSR12 src tests` to check php code formatting
    - `./vendor/bin/phpstan analyse --level=7 src tests` to run static analysis against your php code
    - `php bin/console lint:twig templates/` to lint Twig templates
- Q: What's next?  
  A: Possible extensions could be...
    - [Frontend browser tests](https://symfony.com/doc/current/testing.html#application-tests), [1](https://symfony.com/doc/current/the-fast-track/de/17-tests.html)
    - [Translatable entities](https://github.com/KnpLabs/DoctrineBehaviors/blob/master/docs/translatable.md)
    - More/proper [DTO mapping](https://symfony.com/blog/new-in-symfony-6-3-mapping-request-data-to-typed-objects)
    - And so much more!

## API Endpoints

Below is a table overview for the currently implemented API endpoints:

| Method     | Endpoint                               | Functionality                  |
|------------|----------------------------------------|--------------------------------|
| POST       | /api/v1/products/                      | CREATE product                 |
| GET        | /api/v1/products/                      | READ list of products          |
| GET        | /api/v1/products/{id}                  | READ product                   |
| DELETE     | /api/v1/products/{id}                  | DELETE product                 |
| POST       | /api/v1/carts/                         | CREATE cart                    |
| GET        | /api/v1/carts/                         | READ list of carts             |
| GET        | /api/v1/carts/{id}                     | READ cart                      |
| DELETE     | /api/v1/carts/{id}                     | DELETE cart                    |
| POST       | /api/v1/carts/{cart_id}/positions/     | CREATE position on cart        |
| GET        | /api/v1/carts/{cart_id}/positions/     | READ list of positions on cart |
| DELETE     | /api/v1/carts/{cart_id}/positions/     | DELETE positions on cart       |
| GET        | /api/v1/carts/{cart_id}/positions/{id} | READ position on cart          |
| PUT        | /api/v1/carts/{cart_id}/positions/{id} | UPDATE position on cart        |
| DELETE     | /api/v1/carts/{cart_id}/positions/{id} | DELETE position from cart      |
