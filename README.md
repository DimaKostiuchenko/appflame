# AppFlame

A full-stack web application built with Laravel (backend) and Nuxt.js (frontend), containerized with Docker.

## Project Structure

```
appflame/
├── backend/          # Laravel 12 API backend
├── frontend/         # Nuxt 3 frontend application
├── nginx/            # Nginx configuration
├── docker-compose.dev.yml    # Development environment
└── docker-compose.prod.yml   # Production environment
```

## Tech Stack

### Backend
- **Laravel 12** - PHP framework
- **PHP 8.2+** - Programming language
- **Composer** - Dependency management

### Frontend
- **Nuxt 3** - Vue.js framework
- **Vue 3** - JavaScript framework
- **Vite** - Build tool

### Infrastructure
- **Docker** - Containerization
- **Docker Compose** - Multi-container orchestration
- **Nginx** - Web server and reverse proxy

## Prerequisites

- Docker and Docker Compose installed
- Git


## API Authentication

This application uses a custom API token authentication system. 

### API Token Configuration

The API token is configured in the `.env` file, both in frontend and backend directiries

/backend
```
API_TOKEN=your-api-token-here
```
/frontend
```
NUXT_PUBLIC_API_TOKEN=your-api-token-here
```

### Generating a New API Token

To generate a new UUID v4 API token, use Laravel Tinker:

```bash
php artisan tinker --execute="echo \Illuminate\Support\Str::uuid()->toString();"
```

Copy the generated UUID and add it to your `.env` file:

/backend
```
API_TOKEN=4700f3c9-3190-4716-a768-0255a124f3aa
```
/frontend
```
NUXT_PUBLIC_API_TOKEN=4700f3c9-3190-4716-a768-0255a124f3aa
```

###Execute to run containers 
docker compose -f docker-compose.dev.yml up  

### Using the API Token

All API requests must include the token in the `Authorization` header:

```bash
curl -X GET http://localhost/api/test-auth \
  -H "Authorization: Bearer YOUR_API_TOKEN_HERE"
```



