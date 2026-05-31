#!/bin/sh
set -e

cd /var/www/html

if [ ! -f vendor/autoload.php ]; then
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

if ! grep -q '^APP_KEY=base64:' .env; then
  php artisan key:generate --force --ansi
fi

until php -r '
$host = getenv("DB_HOST") ?: "sql";
$port = getenv("DB_PORT") ?: "3306";
$database = getenv("DB_DATABASE") ?: "habitapp";
$username = getenv("DB_USERNAME") ?: "habitapp";
$password = getenv("DB_PASSWORD") ?: "habitapp";

try {
    new PDO("mysql:host={$host};port={$port};dbname={$database}", $username, $password);
    exit(0);
} catch (Throwable $throwable) {
    exit(1);
}
'; do
  echo "Waiting for MySQL..."
  sleep 3
done

php artisan migrate --force --no-interaction

exec php artisan serve --host=0.0.0.0 --port=8000
