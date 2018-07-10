# Variables
#################
docker-image     = jeboehm/uh.cx:latest
docker-image-dev = jeboehm/uh.cx:latest-dev
travis           = $(TRAVIS)
travis-job-id    = $(TRAVIS_JOB_ID)

# Public targets
#################
.PHONY: build
build: image

.PHONY: dev
dev: build image-dev container-dev

.PHONY: test
test: build image-dev phpunit

.PHONY: coverage
coverage: build image-dev phpunit-coverage

.PHONY: commit
commit: php-cs-fixer

.PHONY: clean
clean: clean-assets clean-dev clean-logstash clean-test clean-test

.PHONY: ci
ci: build image-dev phpunit-coverage coveralls clean-test

.PHONY: logstash
logstash: image image-dev phpunit
	docker-compose -p test -f docker-compose.yml -f docker-compose-test.yml -f resources/stats/docker-compose.yml up -d kibana
	docker-compose -p test -f docker-compose.yml -f docker-compose-test.yml -f resources/stats/docker-compose.yml up -d elasticsearch
	docker-compose -p test -f docker-compose.yml -f docker-compose-test.yml -f resources/stats/docker-compose.yml up logstash_links
	docker-compose -p test -f docker-compose.yml -f docker-compose-test.yml -f resources/stats/docker-compose.yml up logstash_hits

# Private targets
#################
.PHONY: image
image:
	docker build -t $(docker-image) .

.PHONY: image-dev
image-dev:
	docker build -t $(docker-image-dev) -f Dockerfile.dev .

.PHONY: phpunit
phpunit:
	docker-compose -p test -f docker-compose.yml -f docker-compose-test.yml up -d app db
	docker-compose -p test -f docker-compose.yml -f docker-compose-test.yml run --rm app wait-mysql.sh
	docker-compose -p test -f docker-compose.yml -f docker-compose-test.yml run --rm app vendor/bin/phpunit

.PHONY: phpunit-coverage
phpunit-coverage:
	mkdir -p build/logs
	docker-compose -p test -f docker-compose.yml -f docker-compose-test.yml up -d app db
	docker-compose -p test -f docker-compose.yml -f docker-compose-test.yml run --rm coverage wait-mysql.sh
	docker-compose -p test -f docker-compose.yml -f docker-compose-test.yml run --rm coverage vendor/bin/phpunit --coverage-clover=build/logs/clover.xml --coverage-html=build/logs/html

.PHONY: container-dev
container-dev:
	docker-compose -f docker-compose.yml -f docker-compose-dev.yml up -d

.PHONY: php-cs-fixer
php-cs-fixer:
	vendor/bin/php-cs-fixer fix --allow-risky yes

.PHONY: clean-assets
clean-assets:
	rm -rf vendor/ node_modules/ public/build/

.PHONY: clean-dev
clean-dev:
	docker-compose -f docker-compose.yml -f docker-compose-dev.yml down -v

.PHONY: clean-test
clean-test:
	docker-compose -p test -f docker-compose.yml -f docker-compose-test.yml down -v

.PHONY: clean-logstash
clean-logstash:
	docker-compose -p test -f docker-compose.yml -f docker-compose-test.yml -f resources/stats/docker-compose.yml down -v

.PHONY: push
push:
	docker push $(docker-image)

.PHONY: watch
watch: node_modules
	npm run watch

.PHONY: node_modules
node_modules:
	npm install

.PHONY: coveralls
coveralls:
	docker-compose -p test -f docker-compose.yml -f docker-compose-test.yml run -e TRAVIS=$(travis) -e TRAVIS_JOB_ID=$(travis-job-id) --rm coveralls vendor/bin/coveralls -v
