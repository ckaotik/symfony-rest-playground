# Symfonx Rest API

A simple example project for a Symfony-based REST API.

## Installation

1. Create your `.env` file based on the provided `.env.example`.
2. Start the containers using `docker compose up --build -d`
3. Add `symfony.localhost` to your `etc/hosts` file.
4. Setup database structure: Run `php bin/console doctrine:migrations:migrate` inside the `php` service.

The project can be reached under https://symfony.localhost, and PHPmyAdmin is available under http://symfony.localhost:8080.

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
<dd>A: Either add your own using the `db` container or [PHPMyAdmin](http://symfony.localhost:8080), or run `php bin/console doctrine:query:sql "$(< import/product.sql)"` to import some demo products.</dd>
</dl>

## API

Below is a table overview for the (possibly not) implemented API endpoints:

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
