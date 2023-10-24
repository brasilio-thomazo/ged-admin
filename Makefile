username?=devoptimus
version?=0.0.1

build:
	docker build -t devoptimus/ged-admin-composer --target=composer .
	docker build -t devoptimus/ged-admin-cli --target=cli .
	docker build -t devoptimus/ged-admin-fpm --target=fpm .
	docker build -t devoptimus/ged-admin-node --target=node .
	docker build -t devoptimus/ged-admin-nginx --target=nginx .

push:
	docker push devoptimus/ged-admin-composer
	docker push devoptimus/ged-admin-cli
	docker push devoptimus/ged-admin-fpm
	docker push devoptimus/ged-admin-node
	docker push devoptimus/ged-admin-nginx

up: build
	docker compose up -d

down:
	docker compose down

