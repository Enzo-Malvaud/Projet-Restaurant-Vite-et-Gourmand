
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
 
.PHONY: dev dev-tools prod down down-prod build-prod logs ps ps-dev logs-dev \
        clear-cache-dev clear-cache-prod install-dev install-prod \
        migrate-dev migrate-prod fixtures-dev fixtures-prod \
        fix-perms network-check open-ports reset-dev reset-prod
 
# ==============================================================================
# DOCKER - ENVIRONNEMENT
# ==============================================================================
 
# Lancer en développement
dev:
	$(DC) up -d
	@echo "✅ Conteneurs de développement lancés"
 
# Lancer en développement avec les outils (profils additionnels)
dev-tools:
	$(DC) --profile dev up -d
	@echo "✅ Conteneurs de développement avec outils lancés"
 
# Lancer en production (Reconstruit et redémarre)
prod:
	$(DC_PROD) up -d --build --remove-orphans
	@echo "✅ Environnement de production démarré"
 
# Arrêter les containers en développement
down:
	$(DC) down --remove-orphans
	@echo "✅ Conteneurs de développement arrêtés"
 
# Arrêter les containers en production
down-prod:
	$(DC_PROD) down --remove-orphans
	@echo "✅ Conteneurs de production arrêtés"
 
# ==============================================================================
# DOCKER - ÉTAT & LOGS
# ==============================================================================
 
# Voir l'état des containers en développement
ps-dev:
	$(DC) ps
 
# Voir l'état des containers en production
ps:
	$(DC_PROD) ps
 
# Afficher les logs en développement
logs-dev:
	$(DC) logs -f nginx php
 
# Afficher les logs en production
logs:
	$(DC_PROD) logs -f nginx php
 
# ==============================================================================
# SYMFONY - INSTALLATION & CACHE (DÉVELOPPEMENT)
# ==============================================================================
 
# Installer les dépendances Composer en développement
install-dev:
	$(PHP_EXEC) composer install
	@make clear-cache-dev
	@echo "✅ Dépendances Composer installées (DEV)"
 
# Vider le cache en développement
clear-cache-dev:
	$(PHP_EXEC) rm -rf var/cache/dev
	$(CONSOLE) cache:clear --env=dev
	@echo "✅ Cache développement vidé"
 
# ==============================================================================
# SYMFONY - INSTALLATION & CACHE (PRODUCTION)
# ==============================================================================
 
# Installer les dépendances Composer en production
install-prod:
	$(PHP_EXEC_PROD) composer install --no-dev --optimize-autoloader
	@make clear-cache-prod
	@echo "✅ Dépendances Composer installées (PROD)"
 
# Vider le cache en production
clear-cache-prod:
	$(PHP_EXEC_PROD) rm -rf var/cache/prod
	$(CONSOLE_PROD) cache:clear --env=prod
	@echo "✅ Cache production vidé"
 
# ==============================================================================
# BASES DE DONNÉES - MIGRATIONS & FIXTURES (DÉVELOPPEMENT)
# ==============================================================================
 
# Lancer les migrations en développement
migrate-dev:
	$(CONSOLE) doctrine:migrations:migrate --no-interaction
	@echo "✅ Migrations appliquées (DEV)"
 
# Charger les fixtures en développement
fixtures-dev:
	$(CONSOLE) doctrine:fixtures:load --no-interaction
	@echo "✅ Fixtures chargées (DEV)"
 
# ==============================================================================
# BASES DE DONNÉES - MIGRATIONS & FIXTURES (PRODUCTION)
# ==============================================================================
 
# Lancer les migrations en production
migrate-prod:
	$(CONSOLE_PROD) doctrine:migrations:migrate --no-interaction
	@echo "✅ Migrations appliquées (PROD)"
 
# Charger les fixtures en production
fixtures-prod:
	$(CONSOLE_PROD) doctrine:fixtures:load --no-interaction
	@echo "✅ Fixtures chargées (PROD)"
 
# ==============================================================================
# MAINTENANCE & DIAGNOSTIC RÉSEAU (Ports 80 & 443)
# ==============================================================================
 
# Corriger les permissions de fichiers
fix-perms:
	$(PHP_EXEC_PROD) chmod -R 755 var/
	$(PHP_EXEC_PROD) chown -R www-data:www-data var/ public/
	@echo "✅ Permissions corrigées"
 
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
 
# Autoriser les ports dans le pare-feu système
open-ports:
	sudo ufw allow 80/tcp
	sudo ufw allow 443/tcp
	sudo ufw reload
	@echo "✅ Ports 80 et 443 ouverts dans le pare-feu"
 
# ==============================================================================
# RÉINITIALISATION COMPLÈTE
# ==============================================================================
 
# Réinitialiser complètement l'environnement de développement
reset-dev:
	@echo ""
	@echo "🔄 Réinitialisation complète de l'environnement de développement..."
	@echo ""
	@make down
	@echo ""
	@make dev
	@echo ""
	@make install-dev
	@echo ""
	@make migrate-dev
	@echo ""
	@make fixtures-dev
	@echo ""
	@echo "✅ Environnement de développement réinitialisé avec succès!"
	@echo ""
 
# Réinitialiser complètement l'environnement de production
reset-prod:
	@echo ""
	@echo "🔄 Réinitialisation complète de l'environnement de production..."
	@echo ""
	@make down-prod
	@echo ""
	@make prod
	@echo ""
	@make install-prod
	@echo ""
	@make migrate-prod
	@echo ""
	@make fixtures-prod
	@echo ""
	@echo "✅ Environnement de production réinitialisé avec succès!"
	@echo ""