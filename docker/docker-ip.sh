#!/usr/bin/env bash

# Get the Docker Daemon IP address
# Handle docker-machine and docker stock install on Lunix machine
if [ -n "$DOCKER_MACHINE_NAME" ]; then 
	docker-machine ip $DOCKER_MACHINE_NAME
else 
	echo "127.0.0.1"
fi