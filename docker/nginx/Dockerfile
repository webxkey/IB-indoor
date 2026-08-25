ARG LARAVEL_IMAGE=sportynix-laravel:c4b9a15
FROM ${LARAVEL_IMAGE} AS laravel

FROM nginx:1.27-alpine

COPY --from=laravel /var/www/html/public /var/www/html/public
COPY default.conf /etc/nginx/conf.d/default.conf
