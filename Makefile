SHELL := /bin/bash

setup: setup-config setup-npm setup-php

setup-config:
	. scripts/setup-config.sh

setup-npm:
	. scripts/setup-npm.sh

setup-php:
	. scripts/setup-php.sh

run:
	npm run build
	php artisan serve --port=9000
