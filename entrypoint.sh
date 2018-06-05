#!/bin/sh
set -e

wait-mysql.sh
bin/console --env=prod \
    doctrine:schema:update --force

if ! [ -d /tmp/nginx_cache ]
then
    mkdir /tmp/nginx_cache
fi

/usr/bin/supervisord -c /etc/supervisord.conf
