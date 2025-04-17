# PHP refactoring tasks

This repository created for educational purposes.

## Installation using docker

1. Clone the project using git
2. Copy `.env.example` into `.env` (Don't need to change anything for local development)
3. Navigate to the project root directory and run `docker compose up -d`
4. Install dependencies - `docker compose exec app composer install`
5. Open in browser http://127.0.0.1:8080



## Build image commands
```bash
docker build -t php-refactoring ./docker
docker image ls
docker run -d --rm -p 8080:80 --name php-refactoring -v "$PWD":/var/www/ php-refactoring
docker ps

docker exec -it php-refactoring bash 
composer install
OR
docker exec php-refactoring composer install

docker stop php-refactoring
```
