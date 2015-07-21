#!/bin/bash

#$1 = container name
#$2 = mysql server container name
#$3 = repository to create container from


#grab current path and assume there will be a ./www dir to map our volume to
dir=`(pwd)`

#see if this container is already been run
exists=`(docker ps -a | grep $1)`
if [ ! -n "$exists" ]; then	
	docker run -d --name $1 --link $2:mysql -v $dir/www:/project/www -p 80:80 $3
else	
	docker start $1
fi

echo "started web server: "$1
exit