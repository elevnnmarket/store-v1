#!/bin/bash

# Deploy docker to staging
echo "$(pwd) >>>>> Deploy"

STAGING_DIR=$DEPLOY_LOCATION
STAGING_DOCKER_PATH=$STAGING_DIR/wordpress/docker-compose.yml
echo "Staging directory: $STAGING_DIR"
echo "Taking down Staging ($STAGING_DOCKER_PATH)"
[ -f $STAGING_DOCKER_PATH ] && docker-compose -f $STAGING_DOCKER_PATH down

echo "Deleting Staging ($STAGING_DIR)"
rm -rf $STAGING_DIR

echo "Copying to staging ($STAGING_DIR)"
mkdir -p $STAGING_DIR/
cp -a . $STAGING_DIR

echo "Starting up docker at $STAGING_DOCKER_PATH"
docker-compose -f $STAGING_DOCKER_PATH up -d