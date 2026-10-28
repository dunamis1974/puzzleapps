FROM nginx:stable-alpine

WORKDIR /var/www/html

COPY . /var/www/html
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
