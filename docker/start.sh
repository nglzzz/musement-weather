#!/bin/bash

cd `dirname $0`/..

# Import .env variables
if [[ -f .env.local ]]; then
  source <(grep -v '^#' .env.local | sed -E 's|^(.+)=(.*)$|: ${\1=\2}; export \1|g')
fi

# Copy php.ini if not exists
cp -u docker/dev/etc/php/php.ini.example docker/dev/etc/php/php.ini
cp -u docker/prod/etc/php/php.ini.example docker/prod/etc/php/php.ini

# Build required containers
docker build -f docker/prod/containers/web/Dockerfile -t musement_weather/web:latest .
docker build -f docker/prod/containers/app/Dockerfile -t musement_weather/app:latest .

# Build dev containers
docker build -f docker/dev/containers/web/Dockerfile --build-arg=UID=$(id -u) --build-arg=GID=$(id -g) -t musement_weather/web:local .
docker build -f docker/dev/containers/app/Dockerfile --build-arg=UID=$(id -u) --build-arg=GID=$(id -g) -t musement_weather/app:local .

echo 'Starting development containers'
CURRENT_USER=$(id -u):$(id -g) docker-compose -p musement_weather up -d
