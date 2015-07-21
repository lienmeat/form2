#!/bin/bash
#1st arg is the name of the repository/build
#only build if the image doesnt already exist
#or 2nd param has something in it (force build)

exists=`(docker images | grep $1)`
if [ ! -n "$exists" ] || [ -n "$2" ]; then
	docker build -t $1 .
	echo "built $1"
fi
exit
