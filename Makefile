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

.PHONY: download-old-phpdoc
download-old-phpdoc:
	@if [ ! -f ./phpdoc.phar ]; then\
	  wget https://github.com/phpDocumentor/phpDocumentor2/releases/download/v2.9.1/phpDocumentor.phar -O phpdoc.phar;\
	fi

.PHONY: download-new-phpdoc
download-new-phpdoc:
	@if [ ! -f ./phpdoc.phar ]; then\
	  wget https://github.com/phpDocumentor/phpDocumentor/releases/download/v3.3.1/phpDocumentor.phar -O phpdoc.phar;\
	fi

.PHONY: generate-docs
generate-docs:
	@if [ -f ./phpdoc.phar ]; then\
	  rm -rf docs/ && mkdir docs;\
	  php -ddisplay_errors=0 -dopcache.enable_cli=0 phpdoc.phar;\
	  if grep -i -q "No errors have been found" ./docs/api/reports/errors.html; then\
	    echo 'PHPDoc Inspection: 100%';\
	  else \
	    echo 'PHPDoc Inspection: Errors' && exit 1;\
	  fi;\
	else \
	  echo 'The phpdoc.phar is missing, use `make download-old-phpdoc` or `make download-new-phpdoc` to set it up.' && \
	  exit 1;\
	fi
