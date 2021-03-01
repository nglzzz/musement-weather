FROM node:15.10.0-alpine3.10 as build

WORKDIR /app

COPY ./package*.json ./yarn.lock ./webpack.config.js ./
RUN yarn install

COPY ./assets ./assets/

RUN yarn encore production

FROM nginx:1.19.0-alpine

WORKDIR /app

COPY ./docker/prod/etc/nginx/nginx.conf /etc/nginx/conf.d/default.conf
COPY ./public ./public
COPY --from=build /app/public/build ./public/build
