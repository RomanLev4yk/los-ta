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

# create .env
# enter bash and install packages
docker compose exec app_los bash
composer install

# generate app key and run migrations + seeds
php artisan key:generate
php artisan migrate:fresh --seed

# run tests
php artisan test
```
