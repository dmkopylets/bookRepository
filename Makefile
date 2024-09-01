DOCKER_COMPOSE = docker-compose
EXEC_PHP       = $(DOCKER_COMPOSE) exec php-fpm
DOCKER_COMPOSE_FILE = -f docker-compose.yml


local-build: local-compose-file dc-build init-local
dev-build: dev-compose-file dc-build init-dev
prod-build: dc-build init-prod
init-local: dc-down dc-up composer-i  clear-cache
init-dev: dc-down dc-up dc-certbot composer-i migrate swagger-generate fixtures clear-cache
init-prod: dc-down dc-up dc-certbot composer-i migrate clear-cache


local-compose-file:
	$(eval DOCKER_COMPOSE_FILE = -f docker-compose.yml)

dev-compose-file:
	$(eval DOCKER_COMPOSE_FILE = -f docker-compose.base.yml -f docker-compose.dev.yml )

dc-build:
	$(DOCKER_COMPOSE) $(DOCKER_COMPOSE_FILE) build postgres php-fpm nginx

dc-up:
	$(DOCKER_COMPOSE) $(DOCKER_COMPOSE_FILE) up -d postgres php-fpm nginx

dc-down:
	$(DOCKER_COMPOSE) $(DOCKER_COMPOSE_FILE) down --remove-orphans

dc-certbot:
	$(DOCKER_COMPOSE) $(DOCKER_COMPOSE_FILE) up -d icertbot

bash:
	$(EXEC_PHP) bash

fixtures:
	$(EXEC_PHP) sh -c " php bin/console doctrine:fixtures:load --no-interaction"

test:
	$(EXEC_PHP) sh -c " APP_ENV=test php bin/phpunit"

composer-i:
	$(EXEC_PHP) sh -c " composer install"

clear-cache:
	$(EXEC_PHP) bash -c " rm -rf backend/var"

test-init:
	$(EXEC_PHP) sh -c " php bin/console doctrine:database:drop --env=test --force --if-exists; php bin/console doctrine:database:create --env=test --ansi; php bin/console  doctrine:schema:creat --env=test;"

migrate:
	$(EXEC_PHP) sh -c " php bin/console doctrine:migrations:migrate --no-interaction"

migrate-diff:
	$(EXEC_PHP) sh -c " php bin/console doctrine:migrations:diff"

nginx-restart:
	cd docker; docker exec -it nginx sh -c "nginx -t && nginx -s reload"

swagger-generate:
	$(EXEC_PHP) sh -c "./vendor/bin/openapi /var/www/src -o /var/www/api/openApi/swagger.json"

jwt-ssl-key-generate:
	$(EXEC_PHP) sh -c " php bin/console lexik:jwt:generate-keypair --skip-if-exists"
