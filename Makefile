.PHONY: build start stop restart test docs push

# Build and start containers
build:
	docker-compose build

start:
	docker-compose up -d

stop:
	docker-compose down

restart: stop start

# Run tests
TEST_COMMAND="composer run test"
test:
	docker-compose exec legatai_php sh -c "${TEST_COMMAND}"

# Generate documentation
docs:
	composer install
	docker-compose exec legatai_php sh -c "php -r 'require __DIR__ . \"/vendor/autoload.php\"; (new \\phpDocumentor\\Application())->run();'"

# Push containers to registry
push:
	docker-compose build
	docker tag legatai:latest mikroporada/www:latest
	docker push mikroporada/www:latest

# Clean up
.PHONY: clean
clean:
	docker-compose down -v
	docker volume prune -f
	docker image prune -f
