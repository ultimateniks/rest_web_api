# Web API System.
This is used to Create, Fetch and Patch Orders.

## Software requirement for the Web API system.

- [Docker](https://www.docker.com/) as the container service to isolate the environment.
- [Php](https://php.net/) to develop backend support.
- [Laravel](https://laravel.com) as the server framework / controller layer
- [MySQL](https://mysql.com/) as the database layer
- [NGINX](https://docs.nginx.com/nginx/admin-guide/content-cache/content-caching/) as a proxy / content-caching layer

## How to Install & Run with docker
1. Clone the repo.
2. This contains our Source (laravel/php) code along with docker files
3. We have used the Google Distance Matrix API for distance calculation
4. Set Google Distance Matrix Api key `GOOGLE_API_KEY` in environment(.env) file located in root folder.
   We need to get API key from the url `https://cloud.google.com/maps-platform/routes/` after login and then create new project and get the API for the same.
5. For Building Docker Containers, migrations and test cases run file ./start.sh. You may need to grant executable permission to 'start.sh' file.
On Ubuntu: sudo chmod +x start.sh. This will create required containers and migrate tables in created DB and run test cases.

## Manually Starting the docker and test Cases

1. You can run docker-compose up from terminal
2. Server is accessible at http://localhost:8080
3. Run manual testcase suite by `docker exec web_api_php php ./vendor/phpunit/phpunit/phpunit ./tests/` and it will run unit tests and integration tests



## Codebase Structure 

**./app**
- contains Controller folder, Models, Validators, helpers, Services for the Order API's.

**.env**
- This is used for configuring settings like Database, Google API, etc.
    Set GOOGLE_API_KEY here for the project.   

**./tests**
- this folder contains Unit and Feature folders for running test cases.


## Swagger
Open URL `http://localhost:8080/order-api` for API's and Here we can Perform POST, GET, PATCH for Orders.


## Assumptions

- Docker is already installed on the system.
- Authentication is not needed.
