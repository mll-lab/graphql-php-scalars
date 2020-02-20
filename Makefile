.PHONY: it help setup up shell stan test

it: up stan test

help: ## Displays this list of targets with descriptions
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}'

setup: up vendor ## Set up the project

build: ## Build the dev container
	docker-compose build

shell: ## Jump into a shell in the php container
	docker-compose run php bash

stan: ## Run static analysis
	docker-compose run php vendor/bin/phpstan analyse --memory-limit=2048M

test: ## Run PHPUnit tests
	docker-compose run php vendor/bin/phpunit

vendor: composer.json ## Install dependencies through composer
	docker-compose run php composer install
