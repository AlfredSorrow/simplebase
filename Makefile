local:
	docker-compose up -d
local-php:
	php -S localhost:8080 -t public public/index.php