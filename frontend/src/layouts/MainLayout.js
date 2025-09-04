import React from 'react';
import Sidebar from '../components/Sidebar';

const MainLayout = ({ children }) => {
  return (
    <div className="min-h-screen bg-gray-50 dark:bg-gray-900">
      <Sidebar />

      {/* Main Content */}
      <div className="lg:ml-64">
        {/* Page Content */}
        <main className="p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
          {children}
        </main>
      </div>
    </div>
  );
};

export default MainLayout;