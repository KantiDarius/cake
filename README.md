## Get Started

This guide will walk you through the steps needed to get this project up and running on your local machine.

### Prerequisites

Before you begin, ensure you have the following installed:

- Docker
- Docker Compose

### Building the Docker Environment

Build and start the containers:

```
docker-compose up -d --build
```

### Installing Dependencies

```
docker-compose exec app sh
composer install
```

### Database Setup

Set up the database:

```
bin/cake migrations migrate
```

### Accessing the Application

The application should now be accessible at http://localhost:34251

## How to check

### Authentication
Register User:
- Endpoint: /users/register.json
- Method: POST
- Body: {
  "email": "your_email",
  "password": "your_password",
  "password_confirmation": "your_password"
  }

Login: 
- Endpoint: /users/login.json
- Method: POST
- Body: {
  "email": "your_email",
  "password": "your_password"
  }
- Response: { token: "xxx", user: {...} }
- Add token to header Authorization: Bearer xxx

Update password:
- Require Authorization: Bearer xxx in Headers
- Endpoint: /users/change-password.json
- Method: PUT
- Body: {
  "old_password": "your_password",
  "new_password": "your_new_password"
  "new_password_confirmation": "your_new_password"
  }

Update Profile:
- Require Authorization: Bearer xxx in Headers
- Endpoint: /users/update.json
- Method: PUT
- Body: All fields in the users table except email and password

### Article Management
Get Articles:
- Endpoint: /articles.json
- Method: GET
- Params: {
  "title": "your_search",
  "limit": "your_per_page",
  }

Detail Article:
- Endpoint: /articles/{id}.json
- Method: GET

Create Article:
- Require Authorization: Bearer xxx in Headers
- Endpoint: /articles.json
- Method: POST
- Body: {
  "title": "your_title",
  "body": "your_body"
  }

Update Article:
- Require Authorization: Bearer xxx in Headers
- Endpoint: /articles/{id}.json
- Method: PUT
- Body: {
  "title": "your_title",
  "body": "your_body"
  }

Delete Article:
- Require Authorization: Bearer xxx in Headers
- Endpoint: /articles/{id}.json
- Method: DELETE

### Like Feature

Like Article:
- Require Authorization: Bearer xxx in Headers
- Endpoint: /articles/like/{id}.json
- Method: POST
