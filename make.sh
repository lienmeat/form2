#!/bin/bash
#This make file is geared toward lamp setups currently
#but could be changed easily for other setups


#first arg is name of environ to create
ENVIRON_NAME=$1

IMAGE_REPOSITORY="lienmeat/php-dev"

./build_image.sh $IMAGE_REPOSITORY

#mysql server container name
MYSQL_CONTAINER_NAME=$ENVIRON_NAME"-mysql"
PHP_CONTAINER_NAME=$ENVIRON_NAME"-php"

./run_db.sh $MYSQL_CONTAINER_NAME

./run_webserver.sh $PHP_CONTAINER_NAME $MYSQL_CONTAINER_NAME $IMAGE_REPOSITORY

echo $ENVIRON_NAME" built and running"

exit