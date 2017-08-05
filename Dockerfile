FROM jeboehm/php-nginx-base:latest
MAINTAINER Jeffrey Boehm "jeff@ressourcenkonflikt.de"

ENV MYSQL_HOST=db \
    MYSQL_USER=root \
    MYSQL_PASSWORD=root \
    MYSQL_DB=app \
    SYMFONY_DEBUG=0 \
    APP_SECRET=changeme

COPY nginx.conf /etc/nginx/sites-enabled/10-docker.conf
COPY . /var/www/html/

RUN bin/console cache:clear --no-warmup --env=prod && \
    bin/console cache:warmup --env=prod && \
    bin/console cache:clear --no-warmup --env=prod_admin && \
    bin/console cache:warmup --env=prod_admin && \
    bin/console assets:install web --env=prod_admin && \
    rm -f nginx.conf

HEALTHCHECK CMD curl --fail http://localhost/ || exit 1

CMD ["/var/www/html/entrypoint.sh"]
