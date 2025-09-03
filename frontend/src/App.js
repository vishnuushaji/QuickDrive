import React from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { Toaster } from 'react-hot-toast';
import { AuthProvider, useAuth } from './contexts/AuthContext';
import MainLayout from './layouts/MainLayout';

// Pages
import Login from './pages/Login';
import Register from './pages/Register';
import Dashboard from './pages/Dashboard';
import Projects from './pages/Projects';
import CreateProject from './pages/CreateProject';
import EditProject from './pages/EditProject';
import ProjectDetails from './pages/ProjectDetails';
import Tasks from './pages/Tasks';
import CreateTask from './pages/CreateTask';
import EditTask from './pages/EditTask';
import TaskDetails from './pages/TaskDetails';
import Users from './pages/Users';
import CreateUser from './pages/CreateUser';
import EditUser from './pages/EditUser';

// Protected Route Component
const ProtectedRoute = ({ children, allowedRoles = [] }) => {
  const { isAuthenticated, loading, user } = useAuth();

  if (loading) {
    return <div className="flex items-center justify-center h-screen">Loading...</div>;
  }

  if (!isAuthenticated) {
    return <Navigate to="/login" />;
  }

  if (allowedRoles.length > 0 && !allowedRoles.includes(user?.role)) {
    return <Navigate to="/dashboard" />;
  }

  return children;
};

// Layout wrapper for protected routes
const ProtectedLayout = ({ children, allowedRoles }) => (
  <ProtectedRoute allowedRoles={allowedRoles}>
    <MainLayout>{children}</MainLayout>
  </ProtectedRoute>
);

function App() {
  return (
    <Router>
      <AuthProvider>
        <Toaster position="top-right" />
        <Routes>
          {/* Public Routes */}
          <Route path="/login" element={<Login />} />
          <Route path="/register" element={<Register />} />
          <Route path="/" element={<Navigate to="/dashboard" />} />

          {/* Protected Routes */}
          <Route path="/dashboard" element={
            <ProtectedLayout>
              <Dashboard />
            </ProtectedLayout>
          } />

          {/* Project Routes */}
          <Route path="/projects" element={
            <ProtectedLayout>
              <Projects />
            </ProtectedLayout>
          } />
          <Route path="/projects/create" element={
            <ProtectedLayout allowedRoles={['super_admin']}>
              <CreateProject />
            </ProtectedLayout>
          } />
          <Route path="/projects/:id" element={
            <ProtectedLayout>
              <ProjectDetails />
            </ProtectedLayout>
          } />
          <Route path="/projects/:id/edit" element={
            <ProtectedLayout allowedRoles={['super_admin']}>
              <EditProject />
            </ProtectedLayout>
          } />

          {/* Task Routes */}
          <Route path="/tasks" element={
            <ProtectedLayout>
              <Tasks />
            </ProtectedLayout>
          } />
          <Route path="/tasks/create" element={
            <ProtectedLayout allowedRoles={['super_admin', 'developer']}>
              <CreateTask />
            </ProtectedLayout>
          } />
          <Route path="/tasks/:id" element={
            <ProtectedLayout>
              <TaskDetails />
            </ProtectedLayout>
          } />
          <Route path="/tasks/:id/edit" element={
            <ProtectedLayout allowedRoles={['super_admin', 'developer']}>
              <EditTask />
            </ProtectedLayout>
          } />

          {/* User Routes (Super Admin only) */}
          <Route path="/users" element={
            <ProtectedLayout allowedRoles={['super_admin']}>
              <Users />
            </ProtectedLayout>
          } />
          <Route path="/users/create" element={
            <ProtectedLayout allowedRoles={['super_admin']}>
              <CreateUser />
            </ProtectedLayout>
          } />
          <Route path="/users/:id/edit" element={
            <ProtectedLayout allowedRoles={['super_admin']}>
              <EditUser />
            </ProtectedLayout>
          } />
        </Routes>
      </AuthProvider>
    </Router>
  );
}

export default App;