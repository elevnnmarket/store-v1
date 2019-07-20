#!/bin/bash

# Build our docker images but don't run it
echo "$(pwd) >>>> Build"
docker-compose -f ./wordpress/docker-compose.yml up --no-start