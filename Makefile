build:
	@docker compose -f compose.yml -f compose.local.yml build

up:
	@docker compose -f compose.yml -f compose.local.yml up -d

down:
	@docker compose -f compose.yml -f compose.local.yml down