#!/bin/bash

cd `dirname $0`/..

docker container stop $(docker ps -f name=musement_weather -q)

echo 'Containers has been successfully stopped'
