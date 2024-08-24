.PHONY: run

SHELL = /bin/sh

export DOCKER_BUILDKIT=0
export COMPOSE_DOCKER_CLI_BUILD=0

USER_ID=$(shell id -u)
GROUP_ID=$(shell id -g)

export USER_ID
export GROUP_ID

queue:
	docker exec -i app php artisan queue:work --queue=jobs

migrate:
	docker exec -i app php artisan migrate:fresh --seed

buildapp:
	docker exec -i app composer install
	docker exec -i app php artisan jwt:secret
	docker exec -i app php artisan key:generate
	docker exec -i app php artisan optimize
	docker exec -i app php artisan optimize:clear

builddocs:
	docker exec -i app php artisan l5-swagger:generate

test:
	make rates
	docker exec -i app composer tests

rates:
	docker exec -i app php artisan rates:fetch

route:
	docker exec -i app composer dump-autoload
	docker exec -i app php artisan optimize

build:
	docker compose build

env:
	cat .env.example > .env

run:
	make restart \
	&& make install

install:
	make env && \
	make buildapp && \
	sleep 10 && \
	make migrate && \
	make rates && \
	make builddocs

restart:
	make down && \
	make build && \
	make up

up:
	if [ -d /var/run/docker.sock ];then \
	sudo chown ${USER} /var/run/docker.sock ;\
	fi
	if [ -d /run/docker.sock ];then \
	sudo chown ${USER} /run/docker.sock ;\
	fi
	docker compose up -d

down:
	docker compose down
