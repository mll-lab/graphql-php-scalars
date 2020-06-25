.PHONY: it
it: stan test

.PHONY: help
help: ## Displays this list of targets with descriptions
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: setup
setup: build vendor ## Set up the project

.PHONY: build
build: ## Build the dev container
	docker-compose build

.PHONY: shell
shell: ## Jump into a shell in the php container
	docker-compose run php bash

.PHONY: stan
stan: ## Run static analysis
	docker-compose run php vendor/bin/phpstan analyse --memory-limit=2048M

.PHONY: test
test: ## Run PHPUnit tests
	docker-compose run php vendor/bin/phpunit

vendor: composer.json ## Install dependencies through composer
	docker-compose run php composer install
