# Project Management System - Backend

A Laravel-based backend API for a comprehensive project management system that handles user authentication, project management, task management, and role-based access control.

## Table of Contents

- [Features](#features)
- [Technology Stack](#technology-stack)
- [Project Structure](#project-structure)
- [Database Schema](#database-schema)
- [API Endpoints](#api-endpoints)
- [Authentication](#authentication)
- [User Roles](#user-roles)
- [Installation](#installation)
- [Configuration](#configuration)
- [Deployment](#deployment)
- [API Documentation](#api-documentation)

## Features

- **User Authentication**: JWT-based authentication with Laravel Sanctum
- **Role-Based Access Control**: Three user roles (Super Admin, Client, Developer)
- **Project Management**: Create, update, and manage projects with team assignments
- **Task Management**: Comprehensive task lifecycle with status tracking and approvals
- **Email Notifications**: Automated email notifications for task assignments and completions
- **File Attachments**: Support for task attachments with secure storage
- **Dashboard Analytics**: Real-time statistics and progress tracking
- **RESTful API**: Well-structured API endpoints following REST conventions

## Technology Stack

- **Framework**: Laravel 11.x
- **Language**: PHP 8.2+
- **Database**: MySQL/PostgreSQL/SQLite
- **Authentication**: Laravel Sanctum
- **Email**: Laravel Mail (configurable SMTP)
- **File Storage**: Laravel Storage (local/public disk)
- **Frontend Assets**: Vite + Tailwind CSS
- **Testing**: PHPUnit

## Project Structure

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── AuthController.php      # Authentication endpoints
│   │   │   │   ├── UserController.php      # User management (Super Admin only)
│   │   │   │   ├── ProjectController.php   # Project CRUD operations
│   │   │   │   ├── TaskController.php      # Task CRUD operations
│   │   │   │   └── DashboardController.php # Dashboard statistics
│   │   │   └── Auth/                       # Authentication controllers
│   │   ├── Middleware/                     # Custom middleware
│   │   ├── Requests/                       # Form request validation
│   │   └── Mail/                          # Email templates
│   ├── Models/
│   │   ├── User.php                       # User model with roles
│   │   ├── Project.php                    # Project model
│   │   └── Task.php                       # Task model
│   ├── Policies/                          # Authorization policies
│   └── Providers/                         # Service providers
├── database/
│   ├── migrations/                        # Database migrations
│   └── seeders/                          # Database seeders
├── routes/
│   ├── api.php                           # API routes
│   └── web.php                           # Web routes
├── resources/
│   ├── css/                              # Frontend styles
│   ├── js/                               # Frontend JavaScript
│   └── views/                            # Blade templates
├── storage/
│   ├── app/                              # File storage
│   └── logs/                             # Application logs
├── config/                               # Configuration files
├── public/                               # Public assets
└── tests/                                # Test files
```

## Database Schema

### Users Table
- `id` - Primary key
- `name` - User's full name
- `email` - Unique email address
- `password` - Hashed password
- `role` - User role (super_admin, client, developer)
- `email_verified_at` - Email verification timestamp
- `created_at`, `updated_at` - Timestamps

### Projects Table
- `id` - Primary key
- `name` - Project name
- `description` - Project description
- `start_date` - Project start date
- `end_date` - Project end date
- `status` - Project status (active, completed, on_hold)
- `progress` - Project completion percentage
- `created_at`, `updated_at` - Timestamps

### Tasks Table
- `id` - Primary key
- `project_id` - Foreign key to projects
- `assigned_user_id` - Foreign key to users (assigned developer)
- `title` - Task title
- `description` - Task description
- `attachment` - File attachment path
- `status` - Task status (pending, in_progress, completed, approved, rejected)
- `priority` - Task priority (normal, urgent, top_urgent)
- `start_date` - Task start date
- `due_date` - Task due date
- `hours` - Estimated hours
- `created_at`, `updated_at` - Timestamps

### Pivot Tables
- `project_user` - Many-to-many relationship between projects and users with roles
- `task_user` - Many-to-many relationship for task assignments

## API Endpoints

### Authentication Endpoints
- `POST /api/login` - User login
- `POST /api/register` - User registration
- `POST /api/logout` - User logout
- `GET /api/user` - Get authenticated user info

### Dashboard Endpoints
- `GET /api/dashboard/stats` - Get dashboard statistics

### User Management (Super Admin Only)
- `GET /api/users` - List all users
- `POST /api/users` - Create new user
- `GET /api/users/{id}` - Get user details
- `PUT /api/users/{id}` - Update user
- `DELETE /api/users/{id}` - Delete user

### Project Management
- `GET /api/projects` - List projects (filtered by user role)
- `POST /api/projects` - Create new project
- `GET /api/projects/{id}` - Get project details
- `PUT /api/projects/{id}` - Update project
- `DELETE /api/projects/{id}` - Delete project

### Task Management
- `GET /api/tasks` - List tasks (filtered by user role)
- `POST /api/tasks` - Create new task
- `GET /api/tasks/{id}` - Get task details
- `PUT /api/tasks/{id}` - Update task
- `DELETE /api/tasks/{id}` - Delete task
- `PATCH /api/tasks/{id}/status` - Update task status
- `PATCH /api/tasks/{id}/approve` - Approve completed task
- `PATCH /api/tasks/{id}/reject` - Reject completed task

## Authentication

The API uses Laravel Sanctum for authentication:

1. **Login**: Send POST request to `/api/login` with email and password
2. **Token**: Receive Bearer token in response
3. **Authorization**: Include token in `Authorization: Bearer {token}` header
4. **Logout**: Send POST request to `/api/logout` to invalidate token

## User Roles

### Super Admin
- Full access to all features
- Can manage users, projects, and tasks
- Can view all data across the system

### Client
- Can view projects they are assigned to
- Can view tasks within their projects
- Can approve or reject completed tasks
- Cannot create projects or tasks directly

### Developer
- Can create projects and tasks
- Can view and manage their assigned tasks
- Can update task status
- Can view projects they are assigned to

## Installation

1. **Clone the repository**:
   ```bash
   git clone <repository-url>
   cd backend
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Install Node dependencies**:
   ```bash
   npm install
   ```

4. **Environment setup**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database setup**:
   ```bash
   # Configure your database in .env file
   php artisan migrate
   php artisan db:seed  # Optional: seed with sample data
   ```

6. **Storage setup**:
   ```bash
   php artisan storage:link
   ```

7. **Build assets**:
   ```bash
   npm run build
   ```

## Configuration

### Environment Variables (.env)

```env
APP_NAME="Project Management System"
APP_ENV=production
APP_KEY=base64:your-app-key
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@domain.com
MAIL_FROM_NAME="${APP_NAME}"

SANCTUM_STATEFUL_DOMAINS=your-frontend-domain.com
```

### Queue Configuration (for email notifications)

```bash
# Configure queue driver in .env
QUEUE_CONNECTION=database

# Run queue worker
php artisan queue:work
```

## Deployment

### Production Deployment

1. **Server Requirements**:
   - PHP 8.2+
   - Composer
   - Node.js & npm
   - MySQL/PostgreSQL
   - Web server (Apache/Nginx)

2. **Deployment Steps**:
   ```bash
   # Install dependencies
   composer install --optimize-autoloader --no-dev
   npm install && npm run build

   # Environment setup
   cp .env.example .env
   php artisan key:generate
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache

   # Database migration
   php artisan migrate --force

   # Storage permissions
   chown -R www-data:www-data storage
   chown -R www-data:www-data bootstrap/cache
   ```

3. **Web Server Configuration**:

   **Nginx**:
   ```nginx
   server {
       listen 80;
       server_name your-domain.com;
       root /path/to/backend/public;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location ~ \.php$ {
           include fastcgi_params;
           fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
           fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
       }
   }
   ```

4. **SSL Configuration**:
   - Configure SSL certificate (Let's Encrypt recommended)
   - Update APP_URL to use HTTPS
   - Set SANCTUM_STATEFUL_DOMAINS for CORS

5. **Process Management**:
   ```bash
   # Queue worker (for email notifications)
   php artisan queue:work --daemon

   # Task scheduler (if needed)
   * * * * * cd /path/to/backend && php artisan schedule:run >> /dev/null 2>&1
   ```

## API Documentation

### Authentication Example

```javascript
// Login
const response = await fetch('/api/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    email: 'user@example.com',
    password: 'password'
  })
});

const data = await response.json();
const token = data.token;

// Use token for authenticated requests
const authResponse = await fetch('/api/user', {
  headers: {
    'Authorization': `Bearer ${token}`
  }
});
```

### Error Handling

The API returns standardized error responses:

```json
{
  "error": "Validation failed",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

### Rate Limiting

API endpoints are protected with rate limiting. Exceeding limits returns:

```json
{
  "message": "API rate limit exceeded."
}
```

## Security Features

- **CSRF Protection**: Enabled for web routes
- **SQL Injection Prevention**: Eloquent ORM with prepared statements
- **XSS Protection**: Blade templating with automatic escaping
- **Rate Limiting**: Configurable rate limits on API endpoints
- **File Upload Security**: Validated file types and size limits
- **Password Hashing**: Bcrypt hashing for all passwords

## Monitoring & Logging

- **Application Logs**: Stored in `storage/logs/`
- **Database Query Logging**: Configurable in `.env`
- **Error Tracking**: Laravel's exception handling
- **Performance Monitoring**: Database query optimization

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new features
5. Submit a pull request

## License

This project is licensed under the MIT License.
