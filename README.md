# training-php-sql

## Usage

```bash
docker compose up -d
docker compose exec app composer install
```

## Volume

```bash
docker compose down db
docker volume rm training-php-sql_db-data
docker compose up -d db
```
