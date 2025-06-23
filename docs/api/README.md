# API Documentation

## Table of Contents

- [Overview](#overview)
- [Authentication](#authentication)
- [Endpoints](#endpoints)
  - [Chat](#chat)
  - [PDF Generation](#pdf-generation)
  - [Health Check](#health-check)
- [Error Responses](#error-responses)
- [Examples](#examples)

## Overview

The mikroporada.pl API provides endpoints for interacting with the LLM service and generating PDF documents. All endpoints are RESTful and return JSON responses.

## Authentication

No authentication is required for the public endpoints. For protected endpoints, use the following header:

```bash
Authorization: Bearer YOUR_API_KEY
```

## Endpoints

### Chat

#### POST `/api/chat`

Send a message to the LLM service and get a response.

**Request**

```json
{
    "message": "string",
    "session_id": "string"
}
```

**Response**

```json
{
    "response": "string",
    "session_id": "string"
}
```

### PDF Generation

#### POST `/api/generate-pdf`

Generate a PDF from HTML content.

**Request**

```json
{
    "html_content": "string",
    "session_id": "string"
}
```

**Response**

```json
{
    "pdf_path": "string"
}
```

### Health Check

#### GET `/api/health`

Check the health status of the service.

**Response**

```json
{
    "status": "string",
    "ollama_version": "string"
}
```

## Error Responses

All endpoints may return the following error responses:

```json
{
    "error": {
        "code": "string",
        "message": "string",
        "details": "string"
    }
}
```

Common error codes:
- `INVALID_REQUEST`: Invalid request format
- `SERVICE_UNAVAILABLE`: Service is not available
- `INVALID_SESSION`: Invalid session ID
- `GENERATION_ERROR`: Error during PDF generation

## Examples

### Chat Example

```bash
curl -X POST http://localhost:8000/api/chat \
-H "Content-Type: application/json" \
-d '{
    "message": "Hello",
    "session_id": "unique-session-id"
}'
```

### PDF Generation Example

```bash
curl -X POST http://localhost:8000/api/generate-pdf \
-H "Content-Type: application/json" \
-d '{
    "html_content": "<h1>Hello</h1>",
    "session_id": "unique-session-id"
}'
```

## Rate Limiting

- 100 requests per minute per IP
- 1000 requests per hour per IP
- Rate limiting is applied to all endpoints

## Versioning

The API uses semantic versioning. The current version is 1.0.0.

## Support

For support, please contact support@mikroporada.com
