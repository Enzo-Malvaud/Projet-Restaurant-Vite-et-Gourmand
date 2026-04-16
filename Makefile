# ==============================================================================
# VARIABLES
# ==============================================================================

# Commandes de base
DC      = docker compose
DC_PROD = $(DC) -f docker-compose.yml -f docker-compose.prod.yml --env-file .env.prod

# Exécution dans le container PHP
PHP_EXEC      = $(DC) exec php
PHP_EXEC_PROD = $(DC_PROD) exec php

# Raccourcis Symfony Console
CONSOLE      = $(PHP_EXEC) php bin/console
CONSOLE_PROD = $(PHP_EXEC_PROD) php bin/console

.PHONY: dev prod down down-prod build-prod logs ps clear-cache-prod install-prod migrate-prod fixtures-prod fix-perms network-check

# ==============================================================================
# DOCKER - ENVIRONNEMENT
# ==============================================================================

# Lancer en développement
dev:
	$(DC) up -d
	
dev-tools:
	$(DC) --profile dev up -d
# Lancer en production (Reconstruit et redémarre)
prod:
	$(DC_PROD) up -d --build --remove-orphans

# Arrêter les containers
down:
	$(DC) down --remove-orphans

down-prod:
	$(DC_PROD) down --remove-orphans

# Voir l'état et les logs
ps-dev:
	$(DC) ps

logs-dev:
	$(DC) logs -f nginx php

ps:
	$(DC_PROD) ps

logs:
	$(DC_PROD) logs -f nginx php

# ==============================================================================
# SYMFONY - INSTALLATION & CACHE
# ==============================================================================

clear-cache-prod:
	$(PHP_EXEC_PROD) rm -rf var/cache/prod
	$(CONSOLE_PROD) cache:clear --env=prod

install-prod:
	$(PHP_EXEC_PROD) composer install --no-dev --optimize-autoloader
	@make clear-cache-prod

# ==============================================================================
# BASES DE DONNÉES (MySQL & MongoDB)
# ==============================================================================

migrate-prod:
	$(CONSOLE_PROD) doctrine:migrations:migrate --no-interaction

fixtures-prod:
	$(CONSOLE_PROD) doctrine:fixtures:load --no-interaction

# ==============================================================================
# MAINTENANCE & DIAGNOSTIC RÉSEAU (Ports 80 & 443)
# ==============================================================================

# Corriger les permissions de fichiers
fix-perms:
	$(PHP_EXEC_PROD) chmod -R 755 var/
	$(PHP_EXEC_PROD) chown -R www-data:www-data var/ public/

# Vérification complète des ports Web (HTTP & HTTPS)
network-check:
	@echo "--- Vérification Ports 80 (HTTP) et 443 (HTTPS) ---"
	sudo ss -tulpn | grep -E ':80|:443'
	@echo ""
	@echo "--- État du Pare-feu (UFW) ---"
	sudo ufw status | grep -E '80|443'
	@echo ""
	@echo "--- Mapping Docker Nginx ---"
	$(DC_PROD) port nginx

# Autoriser les ports dans le pare-feu système si besoin
open-ports:
	sudo ufw allow 80/tcp
	sudo ufw allow 443/tcp
	sudo ufw reload