#!/bin/sh
set -e

wait-mysql.sh
bin/console --env=prod \
    doctrine:schema:update --force

/usr/bin/supervisord -c /etc/supervisord.conf
