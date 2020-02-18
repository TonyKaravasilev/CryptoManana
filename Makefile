vendor/autoload.php:
	composer install --no-interaction --prefer-dist

.PHONY: install
install: vendor/autoload.php
	@echo 'Project has been installed.'

.PHONY: psr
psr: vendor/autoload.php
	vendor/bin/phpcs --standard=PSR12 src/ -n
	vendor/bin/phpcs --standard=PSR12 tests/ -n

.PHONY: tests
tests: vendor/autoload.php
	vendor/bin/phpunit --verbose --no-coverage

.PHONY: check-system
check-system:
	php check.php

.PHONY: check-build
check-build: vendor/autoload.php
	make psr tests

.PHONY: coverage
coverage: vendor/autoload.php
	vendor/bin/phpunit --testdox --coverage-text
