export BASE_NAME:=$(shell basename ${PWD})
export IMAGE_BASE_NAME:=smlbeltran/$(shell basename ${PWD})
export NETWORK:=${BASE_NAME}-network

default: help

help: ## Prints help for targets with comments
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' ${MAKEFILE_LIST} | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-40s\033[0m %s\n", $$1, $$2}'
	@echo ""

build: build-fpm build-nginx ## Build all images

composer-update: ## Update dependencies
	@docker run --rm \
		-v ${PWD}:${PWD} \
		-w ${PWD} \
		${IMAGE_BASE_NAME}-composer:latest \
		composer update

# An example to run docker container individually.

network-create: ## Create docker network
	@docker network create ${NETWORK}

build-composer: ## Build composer image
	@docker build \
		--build-arg GITHUB_TOKEN \
		--target deps \
		-t ${IMAGE_BASE_NAME}-composer:latest \
		-f docker/fpm/Dockerfile .

build-fpm: ## Build FPM image
	@docker build \
		--build-arg GITHUB_TOKEN \
		--target fpm \
		-t ${IMAGE_BASE_NAME}-fpm:latest \
		-f docker/fpm/Dockerfile .

build-nginx: ## Build Nginx image
	@docker build \
		-t ${IMAGE_BASE_NAME}-nginx:latest \
		-f docker/nginx/Dockerfile .


# Docker compose approach.define

compose:
	@docker-compose ${COMPOSE} \
		-p ${BASE_NAME} \
		up --force-recreate --remove-orphans --abort-on-container-exit # --build

attach:
	@docker-compose ${COMPOSE} \
		-p ${BASE_NAME} \
		logs -f

stop:
	@docker-compose ${COMPOSE} \
		-p ${BASE_NAME} \
		down

up: ## Start the example
	@COMPOSE=" -f docker-compose.yml" make compose

logs: ## Attach to the running containers (tail the logs)
	@COMPOSE=" -f docker-compose.yml" make attach

down: ## Stop the example
	@COMPOSE=" -f docker-compose.yml" make stop

test: ## Run tests
	@COMPOSE=" -f docker-compose.yml -f test.yml" make compose

###############
# Danger Zone #
###############

reset: ## Cleanup
	@docker stop $(shell docker ps -aq) || true
	@docker system prune || true
	@docker volume rm $(shell docker volume ls -q) || true
	@docker rmi -f ${IMAGE_BASE_NAME}-fpm:latest || true
	@docker rmi -f ${IMAGE_BASE_NAME}-test:latest || true
	@docker rmi -f ${IMAGE_BASE_NAME}-nginx:latest || true
	@docker rmi -f ${IMAGE_BASE_NAME}-composer:latest || true
