network:
	docker network create linkinpurry-network

db:
	docker compose up --build linkinpurry-db

web-dev:
	docker compose up --build linkinpurry-web-dev

web-prod:
	docker compose up --build linkinpurry-web-prod

stop:
	docker compose down

reset:
	docker compose down
	docker volume rm linkinpurry-db-data
	docker volume rm linkinpurry-upload-data

