# Symfonx Rest API

A simple example project for a Symfony-based REST API.

## Installation

1. Create your `.env` file based on the provided `.env.example`.
2. Start the containers using `docker compose up --build -d`
3. Add `symfony.localhost` to your `etc/hosts` file.

The project can be reached under https://symfony.localhost, and PHPmyAdmin is available under http://symfony.localhost:8080.

## FAQ

<dl>
<dt>Q: How can I use console commands?</dt>
<dd>A: You can connect to the PHP container using `docker compose exec -it php /bin/bash`. When using Git for Windows, use `//bin/bash` (with an extra slash).</dd>
</dl>