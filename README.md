uh.cx url shortener
===================

[![Build Status](https://travis-ci.org/jeboehm/uh.cx.svg?branch=master)](https://travis-ci.org/jeboehm/uh.cx)

This is a link shortener based on Symfony 3 and PHP 7.1. It brings its own Docker environment
so you can start immediately.
See [uh.cx](https://uh.cx/) for a demonstration, get the source code on [github](https://github.com/jeboehm/uh.cx).

Features
--------
- JSON API
- Google Analytics integration
- EU cookie warning
- RSS Feed about the generated links
- Administration interface

Installation
------------
Execute the following command to start the container.
Keep in mind that you have to change the MySQL credentials.

```
docker run \
    -d --name=uhcx \
    -e MYSQL_HOST=yourmysql.host \
    -e MYSQL_DB=mysqlDatabaseName \
    -e MYSQL_USER=username \
    -e MYSQL_PASSWORD=secret \
    -e VIRTUAL_HOST=uh.cx,preview.uh.cx \
    --restart=always \
    -m 128M \
    jeboehm/uh.cx:latest
```

In addition you need a reverse proxy in front of the app. I recommend
[jwilder/nginx-proxy](https://github.com/jwilder/nginx-proxy). 

Set up the default site by issuing the following command:
```
docker exec -it uhcx bin/console site:create YourSitesName uh.cx preview.uh.cx
```

### Security
The following uri's should be protected by your webserver configuration:

- /admin
- /feed


Configuration
----------------
### Google Analytics
The app supports Google Analytics. To enable the integration pass the environment
variable `GA_TRACKING=UA-XXXXX-1` to the container.

### Enable / disable EU cookie warning
If you need the EU cookie warning feature, set the environment variable
`COOKIE_WARNING` to `true`.

Development
-----------
**Requirements:**
- Docker
- NPM
- PHP
- docker-sync

The development environment can be set up by using `make`:

```
make dev # Build the development image (contains Xdebug), start up MySQL + App, start docker-sync.
make clean # Completely delete the development environment.
```

**Other commands**
```
make build # Build Docker image for production from scratch.
make test # Start up an test environment, execute PHPUnit and delete the environment afterwards.
make coverage # Start up an test environment, execute PHPUnit and build code coverage reports.
make assets # Execute NPM and Grunt to build the public asset files.
make commit # Format the source code.
```
