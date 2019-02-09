vendor/autoload.php:
	composer install --no-interaction --prefer-dist

.PHONY: install
install: vendor/autoload.php
	@echo 'Project has been installed.'

.PHONY: psr
psr: vendor/autoload.php
	vendor/bin/phpcs --standard=PSR2 src/ -n
	vendor/bin/phpcs --standard=PSR2 tests/ -n

.PHONY: tests
tests: vendor/autoload.php
	vendor/bin/phpunit --verbose

.PHONY: check-build
check-build: vendor/autoload.php
	make psr tests
