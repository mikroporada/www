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
+-------------------+
```

## Components

### Frontend
- Modern JavaScript-based chat interface
- Real-time updates using WebSocket
- HTML preview editor
- PDF viewer integration

### API Layer
- PHP-based RESTful API
- Session management
- Document generation
- LLM integration

### Core Services
- Ollama for LLM processing
- MySQL for data persistence
- Docker for containerization

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
