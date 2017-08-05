docker-image     = jeboehm/uh.cx:latest
docker-image-dev = jeboehm/uh.cx:latest-dev

.PHONY: build
build: vendor assets container

.PHONY: dev
dev: build container-dev docker-sync-stack

.PHONY: test
test: build container-dev phpunit

.PHONY: coverage
coverage: build container-dev phpunit-coverage

.PHONY: commit
commit: php-cs-fixer

.PHONY: ci
ci: test

.PHONY: container
container:
	docker build -t $(docker-image) .

.PHONY: container-dev
container-dev:
	docker build -t $(docker-image-dev) -f Dockerfile.dev .

.PHONY: vendor
vendor:
	if [ `which compsoser` ]; then make local-vendor; else make docker-vendor; fi

.PHONY: local-vendor
local-vendor:
	composer install --no-scripts --optimize-autoloader

.PHONY: docker-vendor
docker-vendor:
	docker run --rm -it -v $(CURDIR):/var/www/html -w /var/www/html composer install --no-scripts --optimize-autoloader

.PHONY: assets
assets:
	if [ `which npm` ]; then make local-assets; else make docker-assets; fi

.PHONY: local-assets
local-assets:
	npm install
	node_modules/.bin/bower --allow-root install
	node_modules/.bin/grunt

.PHONY: docker-assets
docker-assets:
	docker run --rm -it -v $(CURDIR):/var/www/html -w /var/www/html node make assets

.PHONY: phpunit
phpunit:
	docker-compose -p test -f docker-compose.yml -f docker-compose-test.yml up -d app db
	docker-compose -p test -f docker-compose.yml -f docker-compose-test.yml run --rm app wait-mysql.sh
	docker-compose -p test -f docker-compose.yml -f docker-compose-test.yml run --rm app vendor/bin/phpunit
	docker-compose -p test -f docker-compose.yml -f docker-compose-test.yml down -v

.PHONY: phpunit-coverage
phpunit-coverage:
	docker-compose -p test -f docker-compose.yml -f docker-compose-test.yml up -d app db
	docker-compose -p test -f docker-compose.yml -f docker-compose-test.yml run --rm coverage wait-mysql.sh
	docker-compose -p test -f docker-compose.yml -f docker-compose-test.yml run --rm coverage vendor/bin/phpunit --coverage-html=/coverage
	docker-compose -p test -f docker-compose.yml -f docker-compose-test.yml down -v

.PHONY: php-cs-fixer
php-cs-fixer:
	vendor/bin/php-cs-fixer fix --allow-risky yes

.PHONY: clean
clean: clean-assets clean-dev

.PHONY: clean-assets
clean-assets:
	rm -rf vendor/ node_modules/ app/Resources/assets/vendor/ web/css/ web/fonts/ web/js/

.PHONY: clean-dev
clean-dev:
	docker-compose -f docker-compose.yml -f docker-compose-dev.yml down -v
	docker-sync clean

.PHONY: docker-sync-stack
docker-sync-stack:
	docker-sync-stack start

.PHONY: push
push: build
	docker push $(docker-image)
