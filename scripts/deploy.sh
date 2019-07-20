#!/bin/bash

# Deploy docker to staging
echo "$(pwd) >>>>> Deploy"

STAGING_DIR=${ELEVNN_STAGING:="~/staging"}
echo "Staging directory: $STAGING_DIR"
echo "Deleting Staging ($STAGING_DIR)"
rm -rf $STAGING_DIR

echo "Copying to staging ($STAGING_DIR)"
cp -a ./. $STAGING_DIR/

STAGING_DOCKER_PATH=$STAGING_DIR/wordpress/docker-compose.yml
echo "Starting up docker at $STAGING_DOCKER_PATH"
docker-compose -f $STAGING_DOCKER_PATH up