#!/bin/bash

# Deploy docker to staging
echo "$(pwd) >>>>> Deploy"

# File paths and secrets from CI server, do not put these in source control
ENV_FILE=$ENV_LOCATION
STAGING_DIR=$DEPLOY_LOCATION
STAGING_DOCKER_PATH=$STAGING_DIR/wordpress/docker-compose.yml
SECRET_FILE_DOCKER_LOCATION=$STAGING_DIR/wordpress/wordpress.env

echo "Staging directory: $STAGING_DIR"
echo "Taking down Staging ($STAGING_DOCKER_PATH)"
[ -f $STAGING_DOCKER_PATH ] && docker-compose -f $STAGING_DOCKER_PATH down

echo "Deleting Staging ($STAGING_DIR)"
rm -rf "$STAGING_DIR"

echo "Copying to staging ($STAGING_DIR)"
mkdir -p "$STAGING_DIR/"
cp -a . "$STAGING_DIR"

echo "Copying private files"
cp "$ENV_FILE" "$SECRET_FILE_DOCKER_LOCATION"


echo "Starting up docker at $STAGING_DOCKER_PATH"
docker-compose -f $STAGING_DOCKER_PATH up -d

echo "Update permissions for installing plugins"
docker exec -d elevnn-staging-wp chmod 777 .