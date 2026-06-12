# Variables
COMPOSE_FILE := srcs/docker-compose.yml
ENV_FILE := .env

# Commandes
.PHONY: up down restart logs ps fclean help

help: ## Affiche cette aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}'

up: ## Lance Postgres et Directus
	docker compose --env-file $(ENV_FILE) -f $(COMPOSE_FILE) up -d

down: ## Arrête les conteneurs
	docker compose --env-file $(ENV_FILE) -f $(COMPOSE_FILE) down

restart: down up ## Redémarre tout

logs: ## Affiche les logs
	docker compose --env-file $(ENV_FILE) -f $(COMPOSE_FILE) logs -f

ps: ## Vérifie l'état des conteneurs
	docker compose --env-file $(ENV_FILE) -f $(COMPOSE_FILE) ps

fclean: down ## Supprime tout (Conteneurs + Volumes + Données)
	docker compose --env-file $(ENV_FILE) -f $(COMPOSE_FILE) down -v
	rm -rf srcs/db_data
	@echo "Tout est nettoyé !"