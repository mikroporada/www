# Deployment Guide

## Prerequisites

### Hardware Requirements
- CPU: 4 cores minimum
- RAM: 8GB minimum
- Storage: 100GB SSD
- Network: 1Gbps minimum

### Software Requirements
- Docker and Docker Compose
- Docker Registry (Docker Hub or private)
- SSL Certificate (Let's Encrypt recommended)
- Backup solution
- Monitoring tools

## Production Setup

### 1. Create Production Configuration

Create `docker-compose.prod.yml`:
```yaml
version: '3'

services:
  legatai:
    build: .
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./prod_data:/var/www/html
    container_name: legatai_php_prod
    restart: always
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
    depends_on:
      - db
      - llm

  llm:
    build: ./llm
    ports:
      - "8000:8000"
    container_name: llm_service_prod
    restart: always
    environment:
      - OLLAMA_MODEL=llama2
      - APP_ENV=production

  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: ${DB_PASSWORD}
      MYSQL_DATABASE: legatai
    volumes:
      - db_data_prod:/var/lib/mysql
    restart: always

  nginx:
    image: nginx:alpine
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./nginx/conf.d:/etc/nginx/conf.d
      - ./nginx/ssl:/etc/nginx/ssl
    depends_on:
      - legatai
    restart: always

volumes:
  db_data_prod:
    driver: local
```

### 2. Create SSL Configuration

Create SSL certificates using Let's Encrypt:
```bash
# Install certbot
apt-get install certbot python3-certbot-nginx

# Get certificate
certbot certonly --nginx -d mikroporada.pl -d www.mikroporada.pl
```

### 3. Configure Nginx

Create `nginx/conf.d/mikroporada.pl.conf`:
```nginx
server {
    listen 80;
    server_name mikroporada.pl www.mikroporada.pl;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl;
    server_name mikroporada.pl www.mikroporada.pl;

    ssl_certificate /etc/nginx/ssl/fullchain.pem;
    ssl_certificate_key /etc/nginx/ssl/privkey.pem;

    location / {
        proxy_pass http://legatai:80;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    location /api/ {
        proxy_pass http://legatai:80;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

### 4. Set Up Environment Variables

Create `.env.prod`:
```
OLLAMA_MODEL=llama2
DB_HOST=db
DB_USER=root
DB_PASSWORD=your_secure_password
DB_NAME=legatai
APP_ENV=production
APP_DEBUG=false
```

## Deployment Process

### 1. Build and Push Images

```bash
# Build images
make build

# Push to registry
make push
```

### 2. Deploy to Production

```bash
# Pull latest images
docker-compose -f docker-compose.prod.yml pull

# Start services
docker-compose -f docker-compose.prod.yml up -d
```

### 3. Verify Deployment

```bash
# Check container status
docker-compose -f docker-compose.prod.yml ps

# Check logs
docker-compose -f docker-compose.prod.yml logs -f

# Verify API
curl -k https://mikroporada.pl/api/health
```

## Maintenance

### Backup

```bash
# Database backup
docker-compose -f docker-compose.prod.yml exec db sh -c 'exec mysqldump -u root --password=$DB_PASSWORD legatai > /backup/legatai.sql'

# PDF backup
docker cp llm_service_prod:/app/tmp/ ./backup/pdfs/
```

### Update

```bash
# Pull latest images
docker-compose -f docker-compose.prod.yml pull

# Update services
docker-compose -f docker-compose.prod.yml up -d
```

### Rollback

```bash
# Rollback to previous version
docker-compose -f docker-compose.prod.yml rollback
```

## Monitoring

### Set Up Monitoring

```bash
# Install monitoring tools
apt-get install prometheus node_exporter

# Configure alerts
prometheus --config.file=/etc/prometheus/prometheus.yml
```

### Monitoring Dashboard

- CPU and Memory usage
- Response times
- Error rates
- Database performance
- Disk usage
- Network traffic

## Security

### Firewall Rules

```bash
# Allow only necessary ports
ufw allow 80
ufw allow 443
ufw allow 22
ufw deny 3306
```

### Regular Security Checks

```bash
# Run security scans
apt-get install lynis
lynis audit system
```

## Troubleshooting

### Common Issues

1. Service not starting:
```bash
docker-compose -f docker-compose.prod.yml logs -f
```

2. Database connection:
```bash
docker-compose -f docker-compose.prod.yml exec db mysql -u root -p
```

3. Missing dependencies:
```bash
docker-compose -f docker-compose.prod.yml exec legatai_php composer install
```

4. Permission issues:
```bash
docker-compose -f docker-compose.prod.yml exec legatai_php chown -R www-data:www-data /var/www/html
```

## Disaster Recovery

### Recovery Process

1. Restore Database
```bash
docker-compose -f docker-compose.prod.yml exec db sh -c 'exec mysql -u root --password=$DB_PASSWORD legatai < /backup/legatai.sql'
```

2. Restore PDFs
```bash
docker cp ./backup/pdfs/ llm_service_prod:/app/tmp/
```

3. Restart Services
```bash
docker-compose -f docker-compose.prod.yml up -d
```

### Verification

- Check database integrity
- Verify PDF generation
- Test API endpoints
- Monitor system performance
