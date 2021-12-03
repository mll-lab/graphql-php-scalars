.PHONY: it
it: fix stan test normalize

.PHONY: help
help: ## Displays this list of targets with descriptions
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(firstword $(MAKEFILE_LIST)) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: setup
setup: vendor ## Set up the project

.PHONY: fix
fix: vendor
	vendor/bin/php-cs-fixer fix

.PHONY: stan
stan: ## Run static analysis
	vendor/bin/phpstan analyse --memory-limit=2048M

.PHONY: test
test: ## Run PHPUnit tests
	vendor/bin/phpunit

.PHONY: normalize
normalize: ## Normalize composer.json
	composer normalize

vendor: composer.json ## Install dependencies through composer
	composer install
