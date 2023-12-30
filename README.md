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