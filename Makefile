.PHONY : start build-start stop flush copy_env remove-dependencies \
				 install-dependencies create-key install-passport \
				 create_assets migrate test app init

.DEFAULT_GOAL := start

start:
	docker-compose up -d

build-start:
	docker-compose up -d --build

stop:
	docker-compose down

flush:
	docker-compose down -v

copy_env:
	cp .docker.env.example .docker.env
	cp .env.example .env

remove-dependencies:
	docker-compose exec php rm -rf vendor

install-dependencies:
	docker-compose exec php composer install

create-key:
	docker-compose exec php php artisan key:generate

install-passport:
	docker-compose exec php php artisan passport:install --force

migrate:
	docker-compose exec php php artisan migrate

test:
	docker-compose exec php vendor/bin/phpunit

app:
	docker-compose exec php bash

init: copy_env flush build-start remove-dependencies install-dependencies create-key migrate install-passport
