
# Our project name
PROJECT_NAME=elevendemo

# Execute a bash script into the app container
PROJECT_BASH=docker exec -ti $(PROJECT_NAME)_app_1 bash

# Address of the Docker daemon
DOCKER_IP=`docker/docker-ip.sh`

ARGS=$@

.PHONY: help start dev test bash stop

help:
	@echo "Please use \`make <target>' where <target> is one of"
	@echo "  run         run docker compose"
	@echo "  dev         prepare devlopment environment"

start:
	@docker-compose -p $(PROJECT_NAME) up -d

dev: start
	@$(PROJECT_BASH) -c 'cp .env.dist .env'
	@$(PROJECT_BASH) -c 'docker/wait-for-it.sh mysql.local:3306 -- echo "mysql is up"'
	@$(PROJECT_BASH) -c 'app/console doctrine:database:create -n'
	@$(PROJECT_BASH) -c 'app/console doctrine:migrations:migrate -n'
	@$(PROJECT_BASH) -c 'app/console doctrine:fixtures:load -n'

stop:
	@docker-compose -p $(PROJECT_NAME) stop

doc:
	@python -m webbrowser "http://$(DOCKER_IP):8000/#/?import=/swagger/service-desk/v1.yml"

test:
	@$(PROJECT_BASH) -c 'bin/phpunit -c app'

bash:
	@$(PROJECT_BASH)

console:
	@$(PROJECT_BASH) -c 'app/console $(CMD)'