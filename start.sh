    #!/usr/bin/env bash

echo "Configuring Pre requisites -------> "
docker-compose down && docker-compose up -d 

echo "Configuring Dependencies -----------> "
docker-compose run composer install --ignore-platform-reqs --quiet
docker exec web_api_php bash -c 'chmod 777 -R /var/www' 
docker exec web_api_php php artisan optimize:clear

echo "Starting Migrations & Data Seeding -------> "
docker exec web_api_php php artisan migrate
docker exec web_api_php php artisan db:seed  --class=DistanceTableSeeder

echo "Starting Unit and Intergration test cases ------->"
docker exec web_api_php php ./vendor/phpunit/phpunit/phpunit ./tests

exit 0
