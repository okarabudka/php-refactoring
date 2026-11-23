# Makefile — local Docker workflow. Run `make help` for the list of targets.

SHELL := /bin/bash
.DEFAULT_GOAL := help

# Run as the host user so files written into the mounted project stay editable; -T works without a TTY (CI).
RUN := docker compose run --rm -T -u $(shell id -u):$(shell id -g) app

.PHONY: help build install test stan cs fix check sh

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## ' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-8s\033[0m %s\n", $$1, $$2}'

build: ## Build the PHP 8.5 CLI image
	docker compose build

install: build ## Install Composer dependencies
	$(RUN) composer install --no-interaction

test: ## Run the PHPUnit tests (pass options with ARGS="--filter Task1")
	$(RUN) composer test -- $(ARGS)

stan: ## Run PHPStan (level max)
	$(RUN) composer stan

cs: ## Check code style (PHP CS Fixer, dry run)
	$(RUN) composer cs

fix: ## Fix code style (PHP CS Fixer)
	$(RUN) composer fix

check: cs stan test ## Run all checks: code style, static analysis, tests

sh: ## Open a shell in the container
	docker compose run --rm -u $(shell id -u):$(shell id -g) app bash
