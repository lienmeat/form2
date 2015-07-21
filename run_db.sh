#!/bin/bash
#$1 = mysql container name
#
#webserver/php container expects mysql-server to have a root pass = 123 because lazy
#Also map port 3306 to host so we can use mysqlworkbench and the like easily

#see if this container is already been run
exists=`(docker ps -a | grep $1)`
if [ ! -n "$exists" ]; then
	docker run --name $1 -e MYSQL_ROOT_PASSWORD=123 -d -p 3306:3306 mysql:5.6
else
	docker start $1
fi

echo "started mysql server: "$1
exit
