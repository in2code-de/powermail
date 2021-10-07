## Show this help
help:
	echo "$(EMOJI_interrobang) Makefile version $(VERSION) help "
	echo ''
	echo 'About this help:'
	echo '  Commands are ${BLUE}blue${RESET}'
	echo '  Targets are ${YELLOW}yellow${RESET}'
	echo '  Descriptions are ${GREEN}green${RESET}'
	echo ''
	echo 'Usage:'
	echo '  ${BLUE}make${RESET} ${YELLOW}<target>${RESET}'
	echo ''
	echo 'Targets:'
	awk '/^[a-zA-Z\-\_0-9]+:/ { \
		helpMessage = match(lastLine, /^## (.*)/); \
		if (helpMessage) { \
			helpCommand = substr($$1, 0, index($$1, ":")+1); \
			helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
			printf "  ${YELLOW}%-${TARGET_MAX_CHAR_NUM}s${RESET} ${GREEN}%s${RESET}\n", helpCommand, helpMessage; \
		} \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST)

## Stop all containers
stop:
	echo "$(EMOJI_stop) Shutting down"
	docker-compose stop
	sleep 0.4
	docker-compose down --remove-orphans

## Removes all containers and volumes
destroy: stop
	echo "$(EMOJI_litter) Removing the project"
	docker-compose down -v --remove-orphans
	git clean -dfx
	make link-compose-file

## Starts docker-compose up -d
start:
	echo "$(EMOJI_up) Starting the docker project"
	docker-compose up -d --build
	make urls

## Creates a backup of the database
mysql-dump:
	echo "$(EMOJI_floppy_disk) Dumping the database"
	mkdir -p $(SQLDUMPSDIR)
	docker-compose exec -u 1000:1000 mysql bash -c "mysqldump -u$(MYSQL_USER) -p$(MYSQL_PASSWORD) --no-tablespaces --add-drop-database --create-options --extended-insert --no-autocommit --quick --default-character-set=utf8mb4 $(MYSQL_DATABASE) | gzip > /$(SQLDUMPSDIR)/$(SQLDUMPFILE)"


## Restores the database from the backup file defined in .env
mysql-restore:
	echo "$(EMOJI_robot) Restoring the database"
	docker-compose exec mysql bash -c 'DUMPFILE="/$(SQLDUMPSDIR)/$(SQLDUMPFILE)"; if [[ "$${DUMPFILE##*.}" == "sql" ]]; then cat $$DUMPFILE; else zcat $$DUMPFILE; fi | mysql --default-character-set=utf8 -u$(MYSQL_USER) -p$(MYSQL_PASSWORD) $(MYSQL_DATABASE)'

## Starts composer-install
composer-install:
	echo "$(EMOJI_package) Installing composer dependencies"
	docker-compose exec php composer install

## Create necessary directories
create-dirs:
	echo "$(EMOJI_dividers) Creating required directories"
	mkdir -p $(TYPO3_CACHE_DIR)
	mkdir -p $(SQLDUMPSDIR)
	mkdir -p $(WEBROOT)/$(TYPO3_CACHE_DIR)

## Starts composer-install
composer-install-production:
	echo "$(EMOJI_package) Installing composer dependencies (without dev)"
	docker-compose exec php composer install --no-dev -ao

## Install mkcert on this computer, skips installation if already present
install-mkcert:
	if [[ "$$OSTYPE" == "linux-gnu" ]]; then \
		if [[ "$$(command -v certutil > /dev/null; echo $$?)" -ne 0 ]]; then sudo apt install libnss3-tools; fi; \
		if [[ "$$(command -v mkcert > /dev/null; echo $$?)" -ne 0 ]]; then sudo curl -L https://github.com/FiloSottile/mkcert/releases/download/v1.4.1/mkcert-v1.4.1-linux-amd64 -o /usr/local/bin/mkcert; sudo chmod +x /usr/local/bin/mkcert; fi; \
	elif [[ "$$OSTYPE" == "darwin"* ]]; then \
	    BREW_LIST=$$(brew ls); \
		if [[ ! $$BREW_LIST == *"mkcert"* ]]; then brew install mkcert; fi; \
		if [[ ! $$BREW_LIST == *"nss"* ]]; then brew install nss; fi; \
	fi;
	mkcert -install > /dev/null

## Create SSL certificates for dinghy and starting project
create-certificate: install-mkcert
	echo "$(EMOJI_secure) Creating SSL certificates for dinghy http proxy"
	mkdir -p $(HOME)/.dinghy/certs/
	PROJECT=$$(echo "$${PWD##*/}" | tr -d '.'); \
	if [[ ! -f $(HOME)/.dinghy/certs/$$PROJECT.docker.key ]]; then mkcert -cert-file $(HOME)/.dinghy/certs/$$PROJECT.docker.crt -key-file $(HOME)/.dinghy/certs/$$PROJECT.docker.key "*.$$PROJECT.docker"; fi;
	if [[ ! -f $(HOME)/.dinghy/certs/${HOST}.key ]]; then mkcert -cert-file $(HOME)/.dinghy/certs/${HOST}.crt -key-file $(HOME)/.dinghy/certs/${HOST}.key ${HOST}; fi;
	if [[ ! -f $(HOME)/.dinghy/certs/${MAIL}.key ]]; then mkcert -cert-file $(HOME)/.dinghy/certs/${MAIL}.crt -key-file $(HOME)/.dinghy/certs/${MAIL}.key ${MAIL}; fi;

## Choose the right docker-compose file for your environment
link-compose-file:
	echo "$(EMOJI_triangular_ruler) Linking the OS specific compose file"
ifeq ($(shell uname -s), Darwin)
	ln -snf .project/docker/docker-compose.darwin.yml docker-compose.yml
else
	ln -snf .project/docker/docker-compose.unix.yml docker-compose.yml
endif

## Install Frontend Build Tool Chain dependencies
npm-install:
	echo "$(EMOJI_explodinghead) Installing Frontend Build Toolchain (this might take a while)"
	docker-compose exec -u node -w /home/node/app/Resources/Private/ node npm ci

## Start watch on node-container
npm-watch:
	docker-compose exec -u node -w /home/node/app/Resources/Private/ node npm run watch:all

## Stop the node container
npm-stop:
	docker-compose stop node

## Initialize the docker setup
init-docker: create-dirs create-certificate
	echo "$(EMOJI_rocket) Initializing docker environment"
	docker-compose pull
	docker-compose up -d --build
	docker-compose exec -u root php chown -R app:app /app/$(WEBROOT)/$(TYPO3_CACHE_DIR)/;

## Copies the TYPO3 site configuration
typo3-add-site:
	echo "$(EMOJI_triangular_flag) Copying the TYPO3 site configuration"
	mkdir -p config/sites/main/
	cp -f .project/TYPO3/config.yaml config/sites/main/config.yaml

## Copies the Additional/DockerConfiguration.php to the correct directory
typo3-add-dockerconfig:
	echo "$(EMOJI_plug) Copying the docker specific configuration for TYPO3"
	mkdir -p $(WEBROOT)/typo3conf/AdditionalConfiguration
	cp -f .project/TYPO3/DockerConfiguration.php $(WEBROOT)/typo3conf/AdditionalConfiguration.php

## Runs the TYPO3 Database Compare
typo3-comparedb:
	echo "$(EMOJI_leftright) Running database:updateschema"
	docker-compose exec php ./.Build/bin/typo3cms database:updateschema

## Starts the TYPO3 setup process
typo3-setupinstall:
	echo "$(EMOJI_upright) Running install:setup"
	docker-compose exec php ./.Build/bin/typo3cms install:setup

## Clears TYPO3 caches via typo3-console
typo3-clearcache:
	echo "$(EMOJI_broom) Clearing TYPO3 caches"
	docker-compose exec php ./.Build/bin/typo3cms cache:flush

## Downloads the dynamicReturnTypeMeta.json for the PhpStorm dynamic return type plugin
typo3-install-autocomplete:
	echo "$(EMOJI_crystal_ball) Installing TYPO3 autocompletion"
	curl -sLO https://raw.githubusercontent.com/TYPO3/TYPO3.CMS/master/dynamicReturnTypeMeta.json

## Checkout LFS files
lfs-fetch:
	echo "$(EMOJI_milky_way) Fetching git LFS content"
	git lfs fetch
	git lfs checkout

## To start an existing project incl. rsync from fileadmin, uploads and database dump
install-project: lfs-fetch link-compose-file destroy add-hosts-entry init-docker composer-install npm-install typo3-add-site typo3-add-dockerconfig typo3-install-autocomplete typo3-setupinstall mysql-restore typo3-clearcache typo3-comparedb
	echo "---------------------"
	echo ""
	echo "The project is online $(EMOJI_thumbsup)"
	echo ""
	echo 'Stop the project with "make stop"'
	echo ""
	echo "---------------------"
	make urls

## To start an new project
new-project: destroy add-hosts-entry init-docker composer-install npm-install typo3-add-site typo3-add-dockerconfig typo3-install-autocomplete typo3-setupinstall typo3-comparedb
	echo "---------------------"
	echo ""
	echo "The project is online $(EMOJI_thumbsup)"
	echo ""
	echo 'Stop the project with "make stop"'
	echo ""
	echo "---------------------"
	make urls

## Print Project URIs
urls:
	PROJECT=$$(echo "$${PWD##*/}" | tr -d '.'); \
	SERVICES=$$(docker-compose ps --services | grep '$(SERVICELIST)'); \
	LONGEST=$$(($$(echo -e "$$SERVICES\nFrontend:" | wc -L 2> /dev/null || echo 15)+2)); \
	echo "$(EMOJI_telescope) Project URLs:"; \
	echo ''; \
	printf "  %-$${LONGEST}s %s\n" "Frontend:" "https://$(HOST)/"; \
	printf "  %-$${LONGEST}s %s\n" "Backend:" "https://$(HOST)/typo3/"; \
	printf "  %-$${LONGEST}s %s\n" "Mail:" "https://$(MAIL)/"; \
	for service in $$SERVICES; do \
		printf "  %-$${LONGEST}s %s\n" "$$service:" "https://$$service.$$PROJECT.docker/"; \
	done;

## Create the hosts entry for the custom project URL (non-dinghy convention)
add-hosts-entry:
	echo "$(EMOJI_monkey) Creating Hosts Entry (if not set yet)"
	SERVICES=$$(command -v getent > /dev/null && echo "getent ahostsv4" || echo "dscacheutil -q host -a name"); \
	if [ ! "$$($$SERVICES $(HOST) | grep 127.0.0.1 > /dev/null; echo $$?)" -eq 0 ]; then sudo bash -c 'echo "127.0.0.1 $(HOST)" >> /etc/hosts; echo "Entry was added"'; else echo 'Entry already exists'; fi;\
	if [ ! "$$($$SERVICES $(MAIL) | grep 127.0.0.1 > /dev/null; echo $$?)" -eq 0 ]; then sudo bash -c 'echo "127.0.0.1 $(MAIL)" >> /etc/hosts; echo "Entry was added"'; else echo 'Entry already exists'; fi;

## Log into the PHP container
login-php:
	echo "$(EMOJI_elephant) Logging into the PHP container"
	docker-compose exec php bash

## Log into the mysql container
login-mysql:
	echo "$(EMOJI_dolphin) Logging into MySQL Container"
	docker-compose exec mysql bash

include .env

# SETTINGS
TARGET_MAX_CHAR_NUM := 25
MAKEFLAGS += --silent
SHELL := /bin/bash
VERSION := 1.0.0

# COLORS
GREEN  := $(shell tput -Txterm setaf 2)
YELLOW := $(shell tput -Txterm setaf 3)
BLUE   := $(shell tput -Txterm setaf 4)
WHITE  := $(shell tput -Txterm setaf 7)
RESET  := $(shell tput -Txterm sgr0)

# EMOJIS (some are padded right with whitespace for text alignment)
EMOJI_litter := "🚮️"
EMOJI_interrobang := "⁉️ "
EMOJI_floppy_disk := "💾️"
EMOJI_dividers := "🗂️ "
EMOJI_up := "🆙️"
EMOJI_receive := "📥️"
EMOJI_robot := "🤖️"
EMOJI_stop := "🛑️"
EMOJI_package := "📦️"
EMOJI_secure := "🔐️"
EMOJI_explodinghead := "🤯️"
EMOJI_rocket := "🚀️"
EMOJI_plug := "🔌️"
EMOJI_leftright := "↔️ "
EMOJI_upright := "↗️ "
EMOJI_thumbsup := "👍️"
EMOJI_telescope := "🔭️"
EMOJI_monkey := "🐒️"
EMOJI_elephant := "🐘️"
EMOJI_dolphin := "🐬️"
EMOJI_helicopter := "🚁️"
EMOJI_broom := "🧹"
EMOJI_nutandbolt := "🔩"
EMOJI_crystal_ball := "🔮"
EMOJI_triangular_ruler := "📐"
EMOJI_milky_way := "🌌"
EMOJI_triangular_flag := "🚩"
