import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { 
  Bell, Settings, User, HelpCircle, LogOut, 
  ChevronDown, Menu, X 
} from 'lucide-react';
import { useAuth } from '../../lib/authContext';

const DashboardHeader: React.FC = () => {
  const [isProfileOpen, setIsProfileOpen] = useState(false);
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
  const { user, logout } = useAuth();

  const toggleProfile = () => {
    setIsProfileOpen(!isProfileOpen);
  };

  const toggleMobileMenu = () => {
    setIsMobileMenuOpen(!isMobileMenuOpen);
  };

  return (
    <header className="bg-white border-b border-gray-200 sticky top-0 z-30">
      <div className="px-4 sm:px-6 lg:px-8">
        <div className="flex h-16 items-center justify-between">
          {/* Logo and mobile menu button */}
          <div className="flex items-center">
            <button
              type="button"
              className="md:hidden inline-flex items-center justify-center rounded-md p-2 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500"
              onClick={toggleMobileMenu}
            >
              <span className="sr-only">Open main menu</span>
              {isMobileMenuOpen ? (
                <X className="block h-6 w-6" aria-hidden="true" />
              ) : (
                <Menu className="block h-6 w-6" aria-hidden="true" />
              )}
            </button>
            
            <Link to="/" className="flex items-center text-primary-500 ml-4 md:ml-0">
              <svg viewBox="0 0 24 24" className="w-8 h-8" fill="currentColor">
                <path d="M12,2 L20,7 L20,17 L12,22 L4,17 L4,7 L12,2 Z M12,4.441 L6,8.267 L6,15.733 L12,19.559 L18,15.733 L18,8.267 L12,4.441 Z M12,8 C14.209,8 16,9.791 16,12 C16,14.209 14.209,16 12,16 C9.791,16 8,14.209 8,12 C8,9.791 9.791,8 12,8 Z M12,10 C10.895,10 10,10.895 10,12 C10,13.105 10.895,14 12,14 C13.105,14 14,13.105 14,12 C14,10.895 13.105,10 12,10 Z" />
              </svg>
              <span className="ml-2 text-xl font-bold hidden md:block">Rankolab</span>
            </Link>
          </div>

          {/* Navigation - Desktop */}
          <nav className="hidden md:flex space-x-8">
            <Link
              to="/dashboard"
              className="text-gray-700 hover:text-primary-500 px-3 py-2 text-sm font-medium"
            >
              Dashboard
            </Link>
            <Link
              to="/dashboard/keywords"
              className="text-gray-700 hover:text-primary-500 px-3 py-2 text-sm font-medium"
            >
              Keywords
            </Link>
            <Link
              to="/dashboard/competitors"
              className="text-gray-700 hover:text-primary-500 px-3 py-2 text-sm font-medium"
            >
              Competitors
            </Link>
            <Link
              to="/dashboard/reports"
              className="text-gray-700 hover:text-primary-500 px-3 py-2 text-sm font-medium"
            >
              Reports
            </Link>
          </nav>

          {/* Right actions: notifications, settings, profile */}
          <div className="flex items-center space-x-4">
            <button
              type="button"
              className="rounded-full p-1.5 text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500"
            >
              <span className="sr-only">View notifications</span>
              <Bell className="h-5 w-5" />
            </button>
            
            <Link
              to="/dashboard/settings"
              className="rounded-full p-1.5 text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500"
            >
              <span className="sr-only">Settings</span>
              <Settings className="h-5 w-5" />
            </Link>
            
            {/* Profile dropdown */}
            <div className="relative">
              <button
                type="button"
                className="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-primary-500"
                id="user-menu-button"
                aria-expanded={isProfileOpen}
                aria-haspopup="true"
                onClick={toggleProfile}
              >
                <span className="sr-only">Open user menu</span>
                <div className="h-8 w-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center mr-2">
                  {user?.name.charAt(0)}
                </div>
                <span className="hidden md:flex items-center">
                  <span className="text-sm font-medium text-gray-700">{user?.name}</span>
                  <ChevronDown size={16} className="ml-1 text-gray-500" />
                </span>
              </button>
              
              {/* Dropdown menu */}
              {isProfileOpen && (
                <div
                  className="absolute right-0 mt-2 w-48 rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-10"
                  role="menu"
                  aria-orientation="vertical"
                  aria-labelledby="user-menu-button"
                >
                  <Link
                    to="/dashboard/profile"
                    className="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    role="menuitem"
                    onClick={() => setIsProfileOpen(false)}
                  >
                    <User size={16} className="mr-2" />
                    Your Profile
                  </Link>
                  <Link
                    to="/dashboard/settings"
                    className="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    role="menuitem"
                    onClick={() => setIsProfileOpen(false)}
                  >
                    <Settings size={16} className="mr-2" />
                    Settings
                  </Link>
                  <Link
                    to="/dashboard/help"
                    className="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    role="menuitem"
                    onClick={() => setIsProfileOpen(false)}
                  >
                    <HelpCircle size={16} className="mr-2" />
                    Help & Support
                  </Link>
                  <button
                    className="flex w-full items-center px-4 py-2 text-sm text-error-600 hover:bg-gray-100"
                    role="menuitem"
                    onClick={() => {
                      logout();
                      setIsProfileOpen(false);
                    }}
                  >
                    <LogOut size={16} className="mr-2" />
                    Sign out
                  </button>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>

      {/* Mobile menu */}
      {isMobileMenuOpen && (
        <div className="md:hidden bg-white border-b border-gray-200">
          <div className="space-y-1 px-4 py-3">
            <Link
              to="/dashboard"
              className="block text-gray-700 hover:bg-gray-100 px-3 py-2 rounded-md text-base font-medium"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              Dashboard
            </Link>
            <Link
              to="/dashboard/keywords"
              className="block text-gray-700 hover:bg-gray-100 px-3 py-2 rounded-md text-base font-medium"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              Keywords
            </Link>
            <Link
              to="/dashboard/competitors"
              className="block text-gray-700 hover:bg-gray-100 px-3 py-2 rounded-md text-base font-medium"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              Competitors
            </Link>
            <Link
              to="/dashboard/reports"
              className="block text-gray-700 hover:bg-gray-100 px-3 py-2 rounded-md text-base font-medium"
              onClick={() => setIsMobileMenuOpen(false)}
            >
              Reports
            </Link>
          </div>
        </div>
      )}
    </header>
  );
};

export default DashboardHeader;