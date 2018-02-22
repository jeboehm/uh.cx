FROM node:9 AS node

WORKDIR /var/www/html
COPY . /var/www/html/

RUN npm install && \
    npm run build

FROM jeboehm/php-nginx-base:7.1
LABEL maintainer="jeff@ressourcenkonflikt.de"

ENV MYSQL_HOST=db \
    MYSQL_USER=root \
    MYSQL_PASSWORD=root \
    MYSQL_DB=app \
    SYMFONY_DEBUG=0 \
    APP_SECRET=changeme

COPY nginx.conf /etc/nginx/sites-enabled/10-docker.conf
COPY . /var/www/html/
COPY --from=node /var/www/html/public/build /var/www/html/public/build

RUN composer install --no-dev --prefer-dist -o --apcu-autoloader && \
    bin/console cache:clear --no-warmup --env=prod && \
    bin/console cache:warmup --env=prod && \
    bin/console assets:install public --env=prod && \
    rm -f nginx.conf

HEALTHCHECK CMD curl --fail http://localhost/ || exit 1

CMD ["/var/www/html/entrypoint.sh"]
