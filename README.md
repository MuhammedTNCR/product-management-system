# Laravel Project with Elasticsearch, Redis and Docker

This is a Laravel project configured to use Docker (via Laravel Sail), Elasticsearch and Redis. Below are the steps for installation, configuration, and running the application.

## Prerequisites

Before you begin, ensure you have the following installed on your system:

- Docker
- Docker Compose
- PHP
- Composer
- Redis

## Installation

First, clone the repository:

```bash
git clone git@github.com:MuhammedTNCR/product-management-system.git
cd product-management-system
```

Next, install the PHP dependencies using Composer:
```bash
composer install
```

Then, copy the example environment file and modify it as needed:
```bash
cp .env.example .env
```

Generate application key:
```bash
php artisan key:generate
```

Ensure the following variables are correctly set in the .env file:
```bash
SCOUT_DRIVER=elastic
ELASTICSEARCH_HOST=http://elasticsearch:9200
ELASTIC_CONNECTION=default
ELASTIC_HOST=elasticsearch:9200
ELASTIC_SCOUT_DRIVER_REFRESH_DOCUMENTS=false
```

Update the docker-compose.yml file to include the Elasticsearch service:
```bash
elasticsearch:
    image: 'docker.elastic.co/elasticsearch/elasticsearch:8.14.0'
    environment:
        - discovery.type=single-node
        - xpack.security.enabled=false
    ports:
        - '9200:9200'
        - '9300:9300'
    volumes:
        - 'sail-elasticsearch:/usr/share/elasticsearch/data'
    networks:
        - sail
    healthcheck:
        test: [ "CMD-SHELL", "curl -f http://localhost:9200 || exit 1" ]
        interval: 30s
        timeout: 10s
        retries: 3
```
Now, build and start the Docker containers using Laravel Sail:
```bash
./vendor/bin/sail up -d
```

Run the database migrations to set up the database schema:
```bash
./vendor/bin/sail artisan migrate --seed
```

Once the containers are up and running, you can access the Laravel application at http://localhost

To stop the Docker containers, use the following command:
```bash
./vendor/bin/sail down
```
You can find more sail information and commands at https://laravel.com/docs/10.x/sail


