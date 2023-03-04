# Executables (local)
DOCKER_COMP = docker-compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP_CONT) bin/console
NPM      = $(PHP_CONT) npm
CMD_ON_PROJECT = docker-compose run -u www-data --rm php

# Misc
.DEFAULT_GOAL = help
.PHONY        = help build dev prod app-prod app-dev up start down upgrade-front assets dependencies logs sh composer vendor sf cache

## —— The Symfony-docker Makefile ———————————————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	#@$(DOCKER_COMP) build --pull --no-cache
	$(PHP_CONT) 
 
dev:  ## Start container at dev stage
#-f 'docker-compose.debug.yml' 
	$(DOCKER_COMP) -f 'docker-compose.yml' -f 'docker-compose.override.yml' up -d

prod:
	$(MAKE) dependencies
	$(MAKE) app-prod

app-prod:
	# $(MAKE) cache
	# $(MAKE) assets

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up -d

start: build up ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down -v --remove-orphans

upgrade-front:
	$(MAKE) node_modules
	$(MAKE) cache
	$(MAKE) assets
	$(MAKE) dsm
	$(MAKE) javascript-prod
	$(MAKE) css
	$(MAKE) javascript-extensions

assets:
	$(CMD_ON_PROJECT) rm -rf public/bundles public/js
	$(PHP) bin/console pim:installer:assets --symlink --clean
 
dependencies: vendor

composer.lock: composer.json
	$(PHP) -d memory_limit=4G /usr/local/bin/composer update

vendor: composer.lock
	$(PHP) -d memory_limit=4G /usr/local/bin/composer install

## —— Composer ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

# vendor: ## Install vendors according to the current composer.lock file
# vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
# vendor: composer

## —— Symfony ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: ## Clear the cache
	$(SYMFONY) cache:clear

## —— npm ——————————————————————————————————————————————————————————————
npm-build: ## Run Webpack
	$(NPM) run build
npm-dev: ## Run Webpack in dev mode
	$(NPM) run dev
npm-install: ## Install all npm dependencies
	$(NPM) install
