build:
	docker build -t devoptimus/admin-fpm --target=fpm .
	docker build -t devoptimus/admin-node --target=node .
	docker build -t devoptimus/admin-nginx --target=nginx .

up: build
	docker compose up

