#!/usr/bin/env bash

sudo apt update
sudo apt install -y curl

echo "Configuring Pre requisites -------> "
docker-compose down && docker-compose up -d 

echo "Configuring Dependencies -----------> "
docker-compose run composer install --ignore-platform-reqs --quiet
docker exec web_api_php php artisan optimize:clear

echo "Starting Migrations & Data Seeding -------> "
sudo chmod 777 -R ./*
docker exec web_api_php php artisan migrate
docker exec web_api_php php artisan db:seed  --class=DistanceTableSeeder

echo "Starting Unit test cases ------->"
docker exec web_api_php php ./vendor/phpunit/phpunit/phpunit ./tests/Unit

echo "Starting Intergration test cases ------->"
docker exec web_api_php php ./vendor/phpunit/phpunit/phpunit ./tests/Feature


exit 0
