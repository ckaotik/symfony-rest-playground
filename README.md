# Symfonx Rest API

A simple example project for a Symfony-based REST API.

## Installation

1. Create your `.env` file based on the provided `.env.example`.
2. Start the containers using `docker compose up --build -d`
3. The project can be reached under https://symfony.localhost

## FAQ

<dl>
<dt>How can I use console commands?</dt>
<dd>You can connect to the PHP container using `docker exec -it symfony-rest-api-php-1 /bin/bash`</dd>
</dl>