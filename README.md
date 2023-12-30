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
3. Run tests using `php bin/phpunit`

## Structure

This repository contains
- a docker environment
- a simple REST API
- some frontend for consuming the API

A production setup should of course separate these, to allow for individual URLs, hosting and scaling. Especially, since the REST API requires a lot fewer dependencies.

## FAQ

<dl>
<dt>Q: How can I use console commands?</dt>
<dd>A: You can connect to the PHP container using `docker compose exec -it php /bin/bash`. When using Git for Windows, use `//bin/bash` (with an extra slash).</dd>

<dt>Q: I need some data to use this!</dt>
<dd>A: Use the default UI or add your own using the `db` container or [PHPMyAdmin](http://symfony.localhost:8080), or run `php bin/console doctrine:query:sql "$(< import/product.sql)"` to import some demo products.</dd>

<dt>Q: How do I ensure my changes conform to the repo code standards?</dt>
<dd>A: Some linters and sniffers are available inside of the symfony project:
    <ul>
        <li>`./vendor/bin/phpcs --standard=PSR12 src` to check php code formatting</li>
        <li>`./vendor/bin/phpstan analyse --level=7 src tests` to run static analysis against your php code</li>
        <li>`php bin/console lint:twig templates/` to lint Twig templates</li>
    </ul>
</dd>
</dl>

## API Endpoints

Below is a table overview for the (possibly not yet) implemented API endpoints:

| Method     | Endpoint                               | Functionality                  |
|------------|----------------------------------------|--------------------------------|
| POST       | /api/v1/products/                      | CREATE product                 |
| GET        | /api/v1/products/                      | READ list of products          |
| GET        | /api/v1/products/{id}                  | READ product                   |
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
