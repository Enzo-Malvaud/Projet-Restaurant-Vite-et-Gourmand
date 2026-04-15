# ==============================================================================
# VARIABLES
# ==============================================================================

# Commandes de base
DC = docker compose
DC_PROD = $(DC) -f docker-compose.yml -f docker-compose.prod.yml --env-file .env.prod

# Exécution dans le container PHP
PHP_EXEC = $(DC) exec php
PHP_EXEC_PROD = $(DC_PROD) exec php

# Raccourcis Symfony Console
CONSOLE = $(PHP_EXEC) php bin/console
CONSOLE_PROD = $(PHP_EXEC_PROD) php bin/console

.PHONY: dev dev-tools prod down down-prod logs ps build build-prod clear-cache-dev clear-cache-prod install install-prod fixtures

# ==============================================================================
# DOCKER - DÉVELOPPEMENT
# ==============================================================================

dev:
	$(DC) up -d

dev-tools:
	$(DC) --profile dev up -d

down:
	$(DC) down --remove-orphans

build:
	$(DC) build --no-cache

logs:
	$(DC) logs -f

ps:
	$(DC) ps

# ==============================================================================
# DOCKER - PRODUCTION
# ==============================================================================

prod:
	$(DC_PROD) up -d --build

down-prod:
	$(DC_PROD) down --remove-orphans

build-prod:
	$(DC_PROD) build --no-cache

# ==============================================================================
# SYMFONY - INSTALLATION & CACHE
# ==============================================================================

clear-cache-dev:
	$(PHP_EXEC) rm -rf var/cache/dev

clear-cache-prod:
	$(PHP_EXEC_PROD) rm -rf var/cache/prod

install: clear-cache-dev
	$(PHP_EXEC) composer install
	$(CONSOLE) cache:clear

install-prod: clear-cache-prod
	$(PHP_EXEC_PROD) composer install --no-dev --optimize-autoloader
	$(CONSOLE_PROD) cache:clear --env=prod

# ==============================================================================
# BASES DE DONNÉES (Bonus de ce qu'on a vu ensemble)
# ==============================================================================

fixtures:
	$(CONSOLE) doctrine:fixtures:load --no-interaction