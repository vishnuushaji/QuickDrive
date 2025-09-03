# Project Management System

A comprehensive full-stack project management application built with Laravel (backend) and React (frontend). This system enables organizations to efficiently manage projects, tasks, and team collaboration with role-based access control.

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Project Structure](#project-structure)
- [Database Schema](#database-schema)
- [API Endpoints](#api-endpoints)
- [User Roles & Permissions](#user-roles--permissions)
- [Installation & Setup](#installation--setup)
- [Configuration](#configuration)
- [Development](#development)
- [Deployment](#deployment)
- [Contributing](#contributing)
- [License](#license)

## Overview

This project management system consists of two main components:

### Backend (Laravel)
- RESTful API built with Laravel 11.x
- JWT-based authentication using Laravel Sanctum
- Role-based access control (RBAC)
- Comprehensive project and task management
- Email notifications for task assignments and completions
- File attachment support

### Frontend (React)
- Modern React 19 application
- Responsive UI built with Tailwind CSS
- Role-based user interfaces
- Real-time dashboard with analytics
- Comprehensive project and task management interfaces
- Toast notifications and form validation

## Features

### Core Functionality
- **User Authentication**: Secure login/registration with JWT tokens
- **Role-Based Access Control**: Three user roles with different permissions
- **Project Management**: Create, edit, and manage projects with team assignments
- **Task Management**: Comprehensive task lifecycle with status tracking
- **Dashboard Analytics**: Real-time statistics and progress visualization
- **Email Notifications**: Automated notifications for task updates
- **File Attachments**: Support for task-related file uploads
- **Responsive Design**: Mobile-first approach for all devices

### Advanced Features
- **Progress Tracking**: Visual progress bars and completion percentages
- **Team Collaboration**: Multi-user project assignments
- **Task Approval Workflow**: Client approval/rejection system
- **Status Management**: Comprehensive task status lifecycle
- **Search & Filtering**: Advanced filtering options
- **Real-time Updates**: Live data synchronization
- **Audit Trail**: Complete activity logging

## Technology Stack

### Backend
- **Framework**: Laravel 11.x
- **Language**: PHP 8.2+
- **Database**: MySQL/PostgreSQL/SQLite
- **Authentication**: Laravel Sanctum
- **Email**: Laravel Mail (SMTP)
- **File Storage**: Laravel Storage
- **Testing**: PHPUnit

### Frontend
- **Framework**: React 19.1.1
- **Routing**: React Router DOM 7.8.2
- **Styling**: Tailwind CSS 3.3.0
- **State Management**: React Context API
- **HTTP Client**: Axios 1.11.0
- **Form Handling**: React Hook Form 7.62.0
- **Icons**: Heroicons 2.2.0
- **UI Components**: Headless UI 2.2.7
- **Notifications**: React Hot Toast 2.6.0

### DevOps & Tools
- **Version Control**: Git
- **Package Management**: Composer (PHP), npm (Node.js)
- **Build Tools**: Vite (Backend), Create React App (Frontend)
- **Code Quality**: ESLint, Prettier
- **Testing**: PHPUnit (Backend), Jest (Frontend)

## Project Structure

```
project-root/
├── backend/                          # Laravel Backend
│   ├── app/
│   │   ├── Http/Controllers/Api/     # API Controllers
│   │   ├── Models/                   # Eloquent Models
│   │   ├── Mail/                     # Email Templates
│   │   └── Policies/                 # Authorization Policies
│   ├── database/
│   │   ├── migrations/               # Database Migrations
│   │   └── seeders/                  # Database Seeders
│   ├── routes/
│   │   ├── api.php                   # API Routes
│   │   └── web.php                   # Web Routes
│   ├── resources/
│   │   ├── css/                      # Frontend Styles
│   │   ├── js/                       # Frontend Scripts
│   │   └── views/                    # Blade Templates
│   ├── storage/                      # File Storage
│   ├── config/                       # Configuration Files
│   ├── public/                       # Public Assets
│   ├── tests/                        # Test Files
│   ├── artisan                       # Laravel CLI
│   ├── composer.json                 # PHP Dependencies
│   ├── package.json                  # Node Dependencies
│   ├── .env.example                  # Environment Template
│   └── README.md                     # Backend Documentation
├── frontend/                         # React Frontend
│   ├── public/
│   │   ├── index.html                # Main HTML Template
│   │   └── favicon.ico               # App Icon
│   ├── src/
│   │   ├── components/               # Reusable Components
│   │   ├── contexts/                 # React Contexts
│   │   ├── layouts/                  # Layout Components
│   │   ├── pages/                    # Page Components
│   │   ├── services/                 # API Services
│   │   ├── hooks/                    # Custom Hooks
│   │   ├── utils/                    # Utility Functions
│   │   ├── App.js                    # Main App Component
│   │   └── index.js                  # App Entry Point
│   ├── package.json                  # Dependencies
│   ├── tailwind.config.js            # Tailwind Config
│   ├── .env.example                  # Environment Template
│   └── README.md                     # Frontend Documentation
├── .gitignore                        # Git Ignore Rules
└── README.md                         # Main Project Documentation
```

## Database Schema

### Core Tables

#### Users Table
```sql
- id (Primary Key)
- name (User's full name)
- email (Unique email address)
- password (Hashed password)
- role (super_admin, client, developer)
- email_verified_at (Email verification timestamp)
- created_at, updated_at (Timestamps)
```

#### Projects Table
```sql
- id (Primary Key)
- name (Project name)
- description (Project description)
- start_date (Project start date)
- end_date (Project end date)
- status (active, completed, on_hold)
- progress (Completion percentage)
- created_at, updated_at (Timestamps)
```

#### Tasks Table
```sql
- id (Primary Key)
- project_id (Foreign Key → projects)
- assigned_user_id (Foreign Key → users)
- title (Task title)
- description (Task description)
- attachment (File attachment path)
- status (pending, in_progress, completed, approved, rejected)
- priority (normal, urgent, top_urgent)
- start_date (Task start date)
- due_date (Task due date)
- hours (Estimated hours)
- created_at, updated_at (Timestamps)
```

#### Pivot Tables
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

## User Roles & Permissions

### Super Admin
- **Full System Access**: Complete access to all features
- **User Management**: Create, edit, and delete users
- **Project Oversight**: Manage all projects and tasks
- **System Configuration**: Access to all settings

### Client
- **Project Viewing**: View projects they are assigned to
- **Task Monitoring**: View tasks within their projects
- **Approval Workflow**: Approve or reject completed tasks
- **Progress Tracking**: Monitor project progress

### Developer
- **Project Creation**: Create new projects
- **Task Management**: Create, edit, and manage assigned tasks
- **Status Updates**: Update task status throughout lifecycle
- **Team Collaboration**: Work within assigned projects

## Installation & Setup

### Prerequisites
- **PHP**: 8.2 or higher
- **Node.js**: 16.x or higher
- **Composer**: Latest version
- **MySQL/PostgreSQL**: Database server
- **Git**: Version control system

### Backend Setup

1. **Clone and Navigate**:
   ```bash
   git clone <repository-url>
   cd backend
   ```

2. **Install PHP Dependencies**:
   ```bash
   composer install
   ```

3. **Install Node Dependencies**:
   ```bash
   npm install
   ```

4. **Environment Configuration**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database Setup**:
   ```bash
   # Configure database in .env file
   php artisan migrate
   php artisan db:seed  # Optional: seed sample data
   ```

6. **Storage Setup**:
   ```bash
   php artisan storage:link
   ```

7. **Build Assets**:
   ```bash
   npm run build
   ```

8. **Start Development Server**:
   ```bash
   php artisan serve
   ```

### Frontend Setup

1. **Navigate to Frontend**:
   ```bash
   cd ../frontend
   ```

2. **Install Dependencies**:
   ```bash
   npm install
   ```

3. **Environment Configuration**:
   ```bash
   cp .env.example .env
   # Configure REACT_APP_API_BASE_URL
   ```

4. **Start Development Server**:
   ```bash
   npm start
   ```

### Running Both Services

For development, you can run both services simultaneously:

```bash
# Terminal 1 - Backend
cd backend && php artisan serve

# Terminal 2 - Frontend
cd frontend && npm start
```

## Configuration

### Backend Environment Variables (.env)

```env
APP_NAME="Project Management System"
APP_ENV=production
APP_KEY=base64:your-generated-key
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

### Frontend Environment Variables (.env)

```env
REACT_APP_API_BASE_URL=http://localhost:8000/api
REACT_APP_APP_NAME="Project Management System"
REACT_APP_VERSION=1.0.0
```

## Development

### Backend Development

```bash
# Run development server
php artisan serve

# Run tests
php artisan test

# Run specific test
php artisan test --filter TestName

# Generate API documentation
php artisan api:generate
```

### Frontend Development

```bash
# Run development server
npm start

# Run tests
npm test

# Build for production
npm run build

# Run linting
npm run lint
```

### Code Quality

```bash
# Backend - Run PHP CS Fixer
./vendor/bin/php-cs-fixer fix

# Frontend - Run ESLint
npm run lint

# Frontend - Format code
npm run format
```

## Deployment

### Production Deployment Checklist

#### Backend Deployment
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Configure production database
- [ ] Set up SMTP email configuration
- [ ] Configure SSL certificate
- [ ] Set up file storage (S3, local, etc.)
- [ ] Run database migrations
- [ ] Set proper file permissions
- [ ] Configure web server (Apache/Nginx)
- [ ] Set up SSL/TLS
- [ ] Configure domain and DNS

#### Frontend Deployment
- [ ] Build production assets: `npm run build`
- [ ] Configure production API URL
- [ ] Set up hosting (Netlify, Vercel, etc.)
- [ ] Configure custom domain
- [ ] Set up SSL certificate
- [ ] Configure environment variables

### Docker Deployment

#### Backend Dockerfile
```dockerfile
FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev

# Copy application code
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/storage

# Generate application key
RUN php artisan key:generate

# Run database migrations
RUN php artisan migrate --force

# Expose port
EXPOSE 9000

CMD ["php-fpm"]
```

#### Frontend Dockerfile
```dockerfile
FROM node:16-alpine as build

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build

FROM nginx:alpine

COPY --from=build /app/build /usr/share/nginx/html

COPY nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
```

### Server Configuration

#### Nginx Configuration for Backend
```nginx
server {
    listen 80;
    server_name api.your-domain.com;
    root /path/to/backend/public;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

#### Nginx Configuration for Frontend
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/frontend/build;
    index index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }

    location /api {
        proxy_pass http://api.your-domain.com;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

## Security Features

- **Authentication**: JWT-based secure authentication
- **Authorization**: Role-based access control
- **CSRF Protection**: Laravel's built-in CSRF protection
- **SQL Injection Prevention**: Eloquent ORM with prepared statements
- **XSS Protection**: React's built-in sanitization and Laravel's Blade escaping
- **Rate Limiting**: Configurable API rate limits
- **File Upload Security**: Validated file types and size limits
- **Password Security**: Bcrypt hashing with salt
- **HTTPS Enforcement**: SSL/TLS encryption in production

## Performance Optimization

### Backend Optimizations
- **Caching**: Laravel's caching system
- **Database Optimization**: Query optimization and indexing
- **Asset Optimization**: Laravel Mix for asset compilation
- **Queue Processing**: Background job processing for emails

### Frontend Optimizations
- **Code Splitting**: React.lazy() for route-based splitting
- **Bundle Analysis**: Webpack bundle analyzer
- **Image Optimization**: Lazy loading and compression
- **Caching**: Service worker for static assets

## Monitoring & Logging

- **Application Logs**: Laravel's logging system
- **Database Query Logging**: Query performance monitoring
- **Error Tracking**: Exception handling and reporting
- **Performance Monitoring**: Response time tracking
- **User Activity Logging**: Audit trail for user actions

## Testing

### Backend Testing
```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test suite
php artisan test tests/Feature/
php artisan test tests/Unit/
```

### Frontend Testing
```bash
# Run all tests
npm test

# Run tests in CI mode
npm test -- --watchAll=false

# Generate coverage report
npm test -- --coverage
```

## Contributing

1. **Fork the Repository**
2. **Create a Feature Branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```
3. **Make Your Changes**
4. **Run Tests**
   ```bash
   # Backend
   php artisan test

   # Frontend
   npm test
   ```
5. **Commit Your Changes**
   ```bash
   git commit -m "Add your feature description"
   ```
6. **Push to Branch**
   ```bash
   git push origin feature/your-feature-name
   ```
7. **Create Pull Request**

### Development Guidelines

- Follow PSR-12 coding standards for PHP
- Use ESLint and Prettier for JavaScript/React
- Write comprehensive tests for new features
- Update documentation for API changes
- Use meaningful commit messages
- Keep pull requests focused and atomic

## Troubleshooting

### Common Issues

#### Backend Issues
- **Database Connection**: Check `.env` database configuration
- **Permission Issues**: Set proper file permissions for storage
- **Composer Issues**: Clear composer cache and reinstall
- **Migration Issues**: Check database schema and rollback if needed

#### Frontend Issues
- **API Connection**: Verify API base URL in environment variables
- **Build Issues**: Clear node_modules and reinstall dependencies
- **CORS Issues**: Configure CORS settings in Laravel
- **Routing Issues**: Check React Router configuration

### Debug Mode

#### Backend Debug
```bash
# Enable debug mode
APP_DEBUG=true

# Check logs
tail -f storage/logs/laravel.log
```

#### Frontend Debug
```bash
# Enable React DevTools
# Check browser console for errors
# Use React Developer Tools extension
```

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

For support and questions:
- Create an issue on GitHub
- Check the documentation
- Review the troubleshooting section
- Contact the development team

## Changelog

### Version 1.0.0
- Initial release
- Basic project and task management
- User authentication and authorization
- Role-based access control
- Email notifications
- File attachments
- Responsive UI

---

**Happy coding! 🚀**