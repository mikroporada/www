# Development Guide

## Local Development Setup

1. Prerequisites:
   - Docker and Docker Compose
   - PHP 8.1 or higher
   - Composer

2. Clone the repository:
```bash
git clone https://github.com/mikroporada/www.git
cd www
```

3. Install dependencies:
```bash
composer install
```

4. Build and start containers:
```bash
make build
make start
```

## Code Structure

```
src/
├── Controllers/     # Application controllers
├── Services/        # Business logic
├── Models/          # Data models
├── Repositories/    # Data access
└── Utils/          # Helper classes

public/
├── assets/          # Static files
├── js/             # JavaScript
├── css/            # Stylesheets
└── index.php       # Entry point

storage/
└── pdfs/          # Generated PDFs
```

## Development Workflow

1. Create feature branch:
```bash
git checkout -b feature/your-feature
```

2. Make changes and commit:
```bash
git add .
git commit -m "Add your feature"
```

3. Run tests:
```bash
make test
```

4. Push changes:
```bash
git push origin feature/your-feature
```

## Coding Standards

- PSR-12 coding standard
- PHPStan for type checking
- PHPUnit for testing
- PHP-CS-Fixer for code formatting

## Testing

1. Unit tests:
```bash
phpunit tests/unit
```

2. Integration tests:
```bash
phpunit tests/integration
```

3. API tests:
```bash
phpunit tests/api
```

## Documentation

1. Generate API docs:
```bash
make docs
```

2. Update README.md
3. Update architecture documentation

## Deployment

1. Build Docker images:
```bash
docker-compose build
```

2. Push to registry:
```bash
docker-compose push
```

3. Deploy to production:
```bash
docker-compose -f docker-compose.prod.yml up -d
```

## Troubleshooting

### Common Issues

1. Docker build fails:
   - Check Dockerfile permissions
   - Clear Docker cache
   - Verify base images

2. API errors:
   - Check logs: `docker-compose logs`
   - Verify environment variables
   - Check database connections

3. Performance issues:
   - Monitor resource usage
   - Check query performance
   - Review code for bottlenecks

## Best Practices

1. Always write tests first
2. Keep functions small and focused
3. Use dependency injection
4. Follow SOLID principles
5. Document complex logic
6. Use meaningful variable names
7. Keep code DRY (Don't Repeat Yourself)
8. Write self-documenting code
