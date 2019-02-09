vendor/autoload.php:
	composer install --no-interaction --prefer-dist

.PHONY: install
install: vendor/autoload.php
	@echo 'Project has been installed.'
