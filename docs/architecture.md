# System Architecture

## Overview

The mikroporada.pl system is built as a modular, microservices-based architecture with clear separation of concerns:

```
+-------------------+
|    Frontend       |
|                   |
|  - Chat Interface |
|  - HTML Preview   |
|  - PDF Viewer     |
+-------------------+
          |
          v
+-------------------+
|    API Layer      |
|                   |
|  - LLM Service    |
|  - PDF Generator  |
|  - Session Mgmt   |
+-------------------+
          |
          v
+-------------------+
|    Core Services  |
|                   |
|  - Ollama         |
|  - MySQL          |
|  - Redis (Cache)  |
+-------------------+
```

## Components

### Frontend
- Modern JavaScript-based chat interface
- Real-time updates using WebSocket
- HTML preview editor
- PDF viewer integration
- Responsive design
- Progressive Web App (PWA) capabilities

### API Layer
- PHP-based RESTful API
- Session management
- Document generation
- LLM integration
- Rate limiting
- Input validation
- Error handling

### Core Services
- Ollama for LLM processing
- MySQL for data persistence
- Redis for caching
- Docker for containerization
- Nginx for reverse proxy

## Data Flow

1. User interacts with chat interface
2. Messages are sent to API layer
3. API layer processes requests:
   - Chat messages → LLM service
   - HTML content → PDF generator
4. Responses are sent back to frontend
5. PDFs are generated and stored
6. Caching layer stores frequent responses

## Security

- Input validation
- Rate limiting
- Session management
- Secure file handling
- HTTPS required
- API authentication
- Database encryption
- Regular security audits

## Scalability

- Stateless API layer
- Horizontal scaling of services
- Load balancing
- Caching strategy
- Database sharding
- Auto-scaling containers

## Monitoring

- API metrics
- Service health checks
- Error tracking
- Performance monitoring
- Log aggregation
- Resource usage monitoring

## Backup & Recovery

- Database backups
- PDF storage backup
- Configuration backup
- Disaster recovery plan
- Point-in-time recovery
- Automated backup scheduling

## Deployment

- Docker-based deployment
- Environment separation
- Zero-downtime deployments
- Rollback capabilities
- Automated testing
- CI/CD pipeline integration

## Testing

- Unit tests
- Integration tests
- API tests
- Load testing
- Security testing
- Performance testing
- End-to-end testing

## Documentation

- API documentation
- Architecture documentation
- Development guide
- Deployment guide
- Troubleshooting guide
- Security documentation

## Data Flow

1. User interacts with chat interface
2. Messages are sent to API layer
3. API layer processes requests:
   - Chat messages → LLM service
   - HTML content → PDF generator
4. Responses are sent back to frontend
5. PDFs are generated and stored

## Security

- Input validation
- Rate limiting
- Session management
- Secure file handling
- HTTPS required

## Scalability

- Stateless API layer
- Horizontal scaling of services
- Load balancing
- Caching strategy

## Monitoring

- API metrics
- Service health checks
- Error tracking
- Performance monitoring
