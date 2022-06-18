DC=docker-compose
RUN=$(DC) run --rm app

dev:
dev: up start

bash:
bash: go_bash

test:
test: up test

stop:
	$(DC) kill
	$(DC) rm -f

build:
	$(DC) build

up:
	$(DC) up -d

go_bash:
	@$(RUN) /bin/bash

test:
	@$(RUN) phpdbg -qrr bin/phpunit --coverage-text

start:
	@$(RUN) php bin/console spreadsheet:import
