# los-app
# init localhost
```shell
# install ssl certificate ssl/los.local.ua.crt
# add hosts
127.0.0.1 los.local.ua

# generate docker image
docker compose build --progress plain

# start docker container
docker compose up -d

npm install

# create .env
# enter bash and install packages
docker compose exec app_los bash
# enter using www-data user
docker compose exec --user www-data app_los bash
composer install

# generate app key
php artisan key:generate

# import database from ../resources/data.sql

# run app
run dev script from package.json

# run tests
php artisan test

# run php code sniffer
vendor/bin/phpcs

# run php code sniffer refactor
vendor/bin/phpcbf
```
