DC=docker-compose
RUN=$(DC) run --rm app

dev:
dev: build up install start

bash:
bash: go_bash

test:
test: build up install test

stop:
	$(DC) kill

build:
	$(DC) build

up:
	$(DC) up -d

go_bash:
	@$(RUN) bash

install:
	@$(RUN) composer install

test:
	@$(RUN) php bin/phpunit

start:
	@$(RUN) php bin/console spreadsheet:import
