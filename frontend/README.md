# Project Management System - Frontend

A modern React-based frontend application for a comprehensive project management system. Built with React 19, React Router, Tailwind CSS, and integrated with a Laravel backend API.

## Table of Contents

- [Features](#features)
- [Technology Stack](#technology-stack)
- [Project Structure](#project-structure)
- [Key Components](#key-components)
- [Pages Overview](#pages-overview)
- [Services & API Integration](#services--api-integration)
- [Authentication Flow](#authentication-flow)
- [Installation](#installation)
- [Configuration](#configuration)
- [Development](#development)
- [Build & Deployment](#build--deployment)
- [File Structure Details](#file-structure-details)

## Features

- **Modern UI/UX**: Clean, responsive design with Tailwind CSS
- **User Authentication**: Login/register with JWT token management
- **Role-Based Access**: Different interfaces for Super Admin, Client, and Developer roles
- **Dashboard Analytics**: Real-time statistics and project progress tracking
- **Project Management**: Create, edit, and manage projects with team assignments
- **Task Management**: Comprehensive task lifecycle with status updates and approvals
- **User Management**: Admin interface for managing system users
- **Responsive Design**: Mobile-first approach with responsive layouts
- **Toast Notifications**: User feedback with react-hot-toast
- **Form Validation**: Client-side validation with react-hook-form
- **Loading States**: Proper loading indicators and error handling

## Technology Stack

- **Framework**: React 19.1.1
- **Routing**: React Router DOM 7.8.2
- **Styling**: Tailwind CSS 3.3.0
- **State Management**: React Context API
- **HTTP Client**: Axios 1.11.0
- **Form Handling**: React Hook Form 7.62.0
- **Icons**: Heroicons 2.2.0
- **UI Components**: Headless UI 2.2.7
- **Notifications**: React Hot Toast 2.6.0
- **Query Management**: TanStack React Query 5.85.6
- **Build Tool**: Create React App
- **Testing**: Jest + React Testing Library

## Project Structure

```
frontend/
├── public/
│   ├── index.html                    # Main HTML template
│   ├── favicon.ico                   # App favicon
│   └── manifest.json                 # PWA manifest
├── src/
│   ├── components/
│   │   ├── Header.js                 # Main navigation header
│   │   ├── Sidebar.js                # Navigation sidebar
│   │   └── Footer.js                 # App footer
│   ├── contexts/
│   │   └── AuthContext.js            # Authentication context provider
│   ├── layouts/
│   │   └── MainLayout.js             # Main app layout wrapper
│   ├── pages/
│   │   ├── Login.js                  # User login page
│   │   ├── Register.js               # User registration page
│   │   ├── Dashboard.js              # Main dashboard with statistics
│   │   ├── Projects.js               # Projects listing page
│   │   ├── CreateProject.js          # Project creation form
│   │   ├── EditProject.js            # Project editing form
│   │   ├── ProjectDetails.js         # Individual project details
│   │   ├── Tasks.js                  # Tasks listing page
│   │   ├── CreateTask.js             # Task creation form
│   │   ├── EditTask.js               # Task editing form
│   │   ├── TaskDetails.js            # Individual task details
│   │   ├── Users.js                  # Users management (Admin only)
│   │   ├── CreateUser.js             # User creation form
│   │   └── EditUser.js               # User editing form
│   ├── services/
│   │   ├── api.js                    # Axios instance and base configuration
│   │   ├── projectService.js         # Project-related API calls
│   │   ├── taskService.js            # Task-related API calls
│   │   └── userService.js            # User-related API calls
│   ├── hooks/
│   │   └── useAuth.js                # Custom authentication hook
│   ├── utils/
│   │   └── helpers.js                # Utility functions
│   ├── App.js                        # Main app component with routing
│   ├── App.css                       # Global styles
│   ├── index.js                      # App entry point
│   └── index.css                     # Global CSS imports
├── package.json                      # Dependencies and scripts
├── tailwind.config.js                # Tailwind CSS configuration
└── .gitignore                        # Git ignore rules
```

## Key Components

### Layout Components

#### `MainLayout.js`
- Main application layout wrapper
- Includes Header, Sidebar, and main content area
- Handles responsive navigation
- Manages user authentication state

#### `Header.js`
- Top navigation bar
- User profile dropdown
- Logout functionality
- Responsive mobile menu toggle

#### `Sidebar.js`
- Left navigation sidebar
- Role-based menu items
- Active route highlighting
- Collapsible on mobile devices

### Context Providers

#### `AuthContext.js`
- Manages user authentication state
- Provides login/logout functions
- Handles token storage and validation
- Role-based access control helpers

## Pages Overview

### Authentication Pages

#### `Login.js`
- User login form with email/password
- Form validation and error handling
- Redirects to dashboard on success
- "Remember me" functionality

#### `Register.js`
- User registration form
- Role selection (Client/Developer)
- Password confirmation validation
- Automatic login after registration

### Dashboard

#### `Dashboard.js`
- Main dashboard with statistics cards
- Recent projects and tasks tables
- Project progress visualization
- Quick action buttons
- Role-specific content filtering

### Project Management

#### `Projects.js`
- List all accessible projects
- Search and filter functionality
- Project status indicators
- Create new project button (Admin/Developer)

#### `CreateProject.js`
- Project creation form
- Team member assignment
- Client and developer selection
- Form validation and error handling

#### `EditProject.js`
- Project editing interface
- Update project details and team
- Status management
- Admin-only access

#### `ProjectDetails.js`
- Detailed project view
- Task list within project
- Team member management
- Progress tracking

### Task Management

#### `Tasks.js`
- Comprehensive task listing
- Status-based filtering
- Priority indicators
- Bulk actions support

#### `CreateTask.js`
- Task creation form
- Project assignment
- User assignment
- File attachment support

#### `EditTask.js`
- Task editing interface
- Status updates
- Reassignment capabilities

#### `TaskDetails.js`
- Detailed task view
- Status change workflow
- Approval/rejection for clients
- File attachment display

### User Management (Admin Only)

#### `Users.js`
- User listing and management
- Role-based filtering
- User status management
- Bulk user operations

#### `CreateUser.js`
- User creation form
- Role assignment
- Password generation

#### `EditUser.js`
- User profile editing
- Role changes
- Password reset functionality

## Services & API Integration

### `api.js`
- Axios instance configuration
- Base URL and timeout settings
- Request/response interceptors
- Authentication token handling
- Error response handling

### `projectService.js`
- Project CRUD operations
- Team member management
- Project statistics
- Progress calculation

### `taskService.js`
- Task CRUD operations
- Status updates
- File attachment handling
- Approval/rejection workflows

### `userService.js`
- User management operations
- Role-based data filtering
- Authentication services

## Authentication Flow

1. **Login Process**:
   - User submits credentials
   - API validates and returns JWT token
   - Token stored in localStorage
   - User data stored in context

2. **Token Management**:
   - Automatic token attachment to requests
   - Token refresh handling
   - Logout clears stored data

3. **Route Protection**:
   - Protected routes check authentication
   - Role-based route access
   - Automatic redirects for unauthorized access

## Installation

1. **Prerequisites**:
   ```bash
   Node.js 16.x or higher
   npm or yarn package manager
   ```

2. **Clone and Install**:
   ```bash
   git clone <repository-url>
   cd frontend
   npm install
   ```

3. **Environment Setup**:
   ```bash
   cp .env.example .env
   # Configure API base URL and other environment variables
   ```

4. **Start Development Server**:
   ```bash
   npm start
   ```

## Configuration

### Environment Variables (.env)

```env
REACT_APP_API_BASE_URL=http://localhost:8000/api
REACT_APP_APP_NAME="Project Management System"
REACT_APP_VERSION=1.0.0
```

### Tailwind Configuration (tailwind.config.js)

```javascript
/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./src/**/*.{js,jsx,ts,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#eff6ff',
          500: '#3b82f6',
          600: '#2563eb',
          700: '#1d4ed8',
        }
      }
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
```

## Development

### Available Scripts

```bash
npm start          # Start development server
npm run build      # Build for production
npm test           # Run test suite
npm run eject      # Eject from Create React App
```

### Development Guidelines

- Use functional components with hooks
- Implement proper error boundaries
- Follow consistent naming conventions
- Use TypeScript for better type safety (optional)
- Implement comprehensive testing
- Follow React best practices

### Code Style

- Use ESLint for code linting
- Prettier for code formatting
- Consistent component structure
- Proper prop validation
- Clean, readable code

## Build & Deployment

### Production Build

```bash
npm run build
```

This creates an optimized production build in the `build` folder.

### Deployment Options

#### Static Hosting (Netlify, Vercel, etc.)

1. Build the application
2. Upload `build` folder contents
3. Configure environment variables
4. Set up custom domain (optional)

#### Docker Deployment

```dockerfile
FROM nginx:alpine
COPY build /usr/share/nginx/html
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
```

#### Apache/Nginx Configuration

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/build;
    index index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }
}
```

## File Structure Details

### Component Structure

Each component follows this pattern:
```javascript
import React from 'react';

const ComponentName = ({ props }) => {
  // State management
  // Event handlers
  // Effects

  return (
    // JSX structure
  );
};

export default ComponentName;
```

### Service Structure

API services follow this pattern:
```javascript
import api from './api';

export const serviceName = {
  async getData(params) {
    const response = await api.get('/endpoint', { params });
    return response.data;
  },

  async createData(data) {
    const response = await api.post('/endpoint', data);
    return response.data;
  }
};
```

### Context Structure

Context providers follow this pattern:
```javascript
import React, { createContext, useContext, useState, useEffect } from 'react';

const Context = createContext();

export const useContextName = () => useContext(Context);

export const ContextProvider = ({ children }) => {
  const [state, setState] = useState(initialState);

  // Context logic

  return (
    <Context.Provider value={{ state, actions }}>
      {children}
    </Context.Provider>
  );
};
```

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Performance Optimization

- Code splitting with React.lazy()
- Image optimization
- Bundle analysis
- Caching strategies
- Progressive Web App features

## Security Considerations

- XSS protection through React's built-in sanitization
- CSRF protection via Laravel backend
- Secure token storage
- Input validation and sanitization
- HTTPS enforcement in production

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new features
5. Ensure all tests pass
6. Submit a pull request

## License

This project is licensed under the MIT License.
