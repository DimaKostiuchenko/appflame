## API Authentication

This application uses a custom API token authentication system. 

### API Token Configuration

The API token is configured in the `.env` file:

```
API_TOKEN=your-api-token-here
```

### Generating a New API Token

To generate a new UUID v4 API token, use Laravel Tinker:

```bash
php artisan tinker --execute="echo \Illuminate\Support\Str::uuid()->toString();"
```

Copy the generated UUID and add it to your `.env` file:

```
API_TOKEN=4700f3c9-3190-4716-a768-0255a124f3aa
```

### Using the API Token

All API requests must include the token in the `Authorization` header:

```bash
curl -X GET http://localhost:8000/api/test-auth \
  -H "Authorization: Bearer YOUR_API_TOKEN_HERE"
```

Or in JavaScript/frontend:

```javascript
fetch('http://localhost:8000/api/test-auth', {
  headers: {
    'Authorization': 'Bearer YOUR_API_TOKEN_HERE',
    'Content-Type': 'application/json'
  }
})
```

### Testing Authentication

A test route is available at `/api/test-auth` to verify authentication is working:

```bash
# Without token (should return 401)
curl http://localhost:8000/api/test-auth

# With valid token (should return 200)
curl -H "Authorization: Bearer YOUR_API_TOKEN_HERE" http://localhost:8000/api/test-auth
```
