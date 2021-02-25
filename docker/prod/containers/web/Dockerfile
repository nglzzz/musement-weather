FROM nginx:1.19.0-alpine

WORKDIR /app

COPY ./docker/prod/etc/nginx/nginx.conf /etc/nginx/conf.d/default.conf
COPY ./public ./public
