network:
	docker network create linkinpurry-network
	echo "Network created"

db:
	docker compose up --build db

web-dev:
	docker compose up --build web-dev

web-prod:
	docker compose up --build web-prod

stop:
	docker compose down

reset:
	docker compose down
	sudo rm -rf ./db-data
	echo "Reset complete"


