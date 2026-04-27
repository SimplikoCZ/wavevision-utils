bin=vendor/bin
chrome:=$(shell command -v google-chrome 2>/dev/null)
codeSnifferRuleset=codesniffer-ruleset.xml
coverage=$(temp)/coverage
coverageClover=$(coverage)/coverage.xml
php=php
src=src
temp=temp
tests=tests
dirs:=$(src) $(tests)
docker_run=docker compose run --rm php

all:
	 @$(MAKE) -pRrq -f $(lastword $(MAKEFILE_LIST)) : 2>/dev/null | awk -v RS= -F: '/^# File/,/^# Finished Make data base/ {if ($$1 !~ "^[#.]") {print $$1}}' | sort | egrep -v -e '^[^[:alnum:]]' -e '^$@$$'

# Setup

composer:
	$(docker_run) composer install

reset:
	rm -rf $(temp)/cache
	$(docker_run) composer dumpautoload

di: reset
	$(docker_run) bin/extract-services

fix: reset check-syntax phpcbf phpcs phpstan test

# QA

check-syntax:
	$(docker_run) $(bin)/parallel-lint -e $(php) $(dirs)

phpcs:
	$(docker_run) $(bin)/phpcs -sp --standard=$(codeSnifferRuleset) --extensions=php $(dirs)

phpcbf:
	$(docker_run) $(bin)/phpcbf -spn --standard=$(codeSnifferRuleset) --extensions=php $(dirs) ; true

phpstan:
	$(docker_run) $(bin)/phpstan analyze $(dirs)

# Tests

test:
	$(docker_run) $(bin)/phpunit

test-coverage: reset
	$(docker_run) $(bin)/phpunit --coverage-html=$(coverage)

test-coverage-clover: reset
	$(docker_run) $(bin)/phpunit --coverage-clover=$(coverageClover)

test-coverage-report: test-coverage-clover
	$(docker_run) $(bin)/php-coveralls --coverage_clover=$(coverageClover) --verbose

test-coverage-open: test-coverage
ifndef chrome
	open -a 'Google Chrome' $(coverage)/index.html
else
	google-chrome $(coverage)/index.html
endif

ci: check-syntax phpcs phpstan test-coverage-report
