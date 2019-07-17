#!/bin/bash

# Build our docker images but don't run it
docker-compose -f ../wordpress/docker-compose.yml up --no-start