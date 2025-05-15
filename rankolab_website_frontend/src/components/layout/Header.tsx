import React, { useState, useEffect } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { Menu, X, ChevronDown, User, LogOut } from 'lucide-react';
import Button from '../ui/Button';
import Container from '../ui/Container';
import { useAuth } from '../../lib/authContext';

import rankolabLogo from \'../../assets/rankolab-logo.jpeg\';

const Header: React.FC = () => {
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const [isScrolled, setIsScrolled] = useState(false);
  const [isProfileOpen, setIsProfileOpen] = useState(false);
  const location = useLocation();
  const { isAuthenticated, user, logout } = useAuth();

  // Check if current path matches the link
  const isActive = (path: string) => location.pathname === path;

  // Toggle mobile menu
  const toggleMenu = () => setIsMenuOpen(!isMenuOpen);
  
  // Toggle profile dropdown
  const toggleProfile = () => setIsProfileOpen(!isProfileOpen);

  // Handle scroll events for header styling
  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 10);
    };

    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  // Close mobile menu when clicking a link
  const handleNavLinkClick = () => {
    setIsMenuOpen(false);
  };

  return (
    <header 
      className={`fixed top-0 left-0 right-0 z-50 transition-all duration-300 ${
        isScrolled ? 'bg-white shadow-sm py-3' : 'bg-transparent py-5'
      }`}
    >
      <Container>
        <nav className="flex items-center justify-between">
          {/* Logo */}
          <Link 
            to="/" 
            className="flex items-center text-primary-500"
            onClick={handleNavLinkClick}
          >
            <img src={rankolabLogo} alt="Rankolab Logo" className="w-8 h-8" />
            <span className="ml-2 text-xl font-bold">Rankolab</span>
          </Link>

          {/* Desktop Navigation */}
          <div className="hidden md:flex items-center space-x-1">
            <Link 
              to="/features" 
              className={`px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200 hover:bg-gray-100 ${
                isActive('/features') ? 'text-primary-500' : 'text-gray-700'
              }`}
            >
              Features
            </Link>
            <Link 
              to="/pricing" 
              className={`px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200 hover:bg-gray-100 ${
                isActive('/pricing') ? 'text-primary-500' : 'text-gray-700'
              }`}
            >
              Pricing
            </Link>
            <Link 
              to="/blog" 
              className={`px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200 hover:bg-gray-100 ${
                isActive('/blog') ? 'text-primary-500' : 'text-gray-700'
              }`}
            >
              Blog
            </Link>
            <Link 
              to="/docs" 
              className={`px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200 hover:bg-gray-100 ${
                isActive('/docs') ? 'text-primary-500' : 'text-gray-700'
              }`}
            >
              Documentation
            </Link>
            <Link 
              to="/contact" 
              className={`px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200 hover:bg-gray-100 ${
                isActive('/contact') ? 'text-primary-500' : 'text-gray-700'
              }`}
            >
              Contact
            </Link>
          </div>

          {/* Authentication Buttons or User Profile */}
          <div className="hidden md:flex items-center space-x-4">
            {isAuthenticated ? (
              <div className="relative">
                <button
                  onClick={toggleProfile}
                  className="flex items-center text-sm px-3 py-2 rounded-md font-medium text-gray-700 hover:bg-gray-100 transition-colors duration-200"
                >
                  <span className="mr-1">{user?.name}</span>
                  <ChevronDown size={16} />
                </button>
                
                {isProfileOpen && (
                  <div className="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 border border-gray-200 animate-fade-in">
                    <Link
                      to="/dashboard"
                      className="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200"
                      onClick={() => setIsProfileOpen(false)}
                    >
                      <div className="flex items-center">
                        <User size={16} className="mr-2" />
                        Dashboard
                      </div>
                    </Link>
                    <button
                      onClick={() => {
                        logout();
                        setIsProfileOpen(false);
                      }}
                      className="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200"
                    >
                      <div className="flex items-center text-error-600">
                        <LogOut size={16} className="mr-2" />
                        Logout
                      </div>
                    </button>
                  </div>
                )}
              </div>
            ) : (
              <>
                <Link to="/login">
                  <Button variant="outline" size="sm">Log In</Button>
                </Link>
                <Link to="/register">
                  <Button variant="primary" size="sm">Sign Up Free</Button>
                </Link>
              </>
            )}
          </div>

          {/* Mobile Menu Button */}
          <div className="md:hidden">
            <button
              onClick={toggleMenu}
              className="text-gray-700 hover:text-primary-500 focus:outline-none transition-colors duration-200"
            >
              {isMenuOpen ? <X size={24} /> : <Menu size={24} />}
            </button>
          </div>
        </nav>

        {/* Mobile Menu */}
        {isMenuOpen && (
          <div className="md:hidden pt-4 pb-3 border-t border-gray-200 mt-3 animate-slide-down">
            <div className="space-y-1">
              <Link
                to="/features"
                className={`block px-3 py-2 rounded-md text-base font-medium ${
                  isActive('/features') ? 'text-primary-500 bg-gray-50' : 'text-gray-700 hover:bg-gray-50'
                }`}
                onClick={handleNavLinkClick}
              >
                Features
              </Link>
              <Link
                to="/pricing"
                className={`block px-3 py-2 rounded-md text-base font-medium ${
                  isActive('/pricing') ? 'text-primary-500 bg-gray-50' : 'text-gray-700 hover:bg-gray-50'
                }`}
                onClick={handleNavLinkClick}
              >
                Pricing
              </Link>
              <Link
                to="/blog"
                className={`block px-3 py-2 rounded-md text-base font-medium ${
                  isActive('/blog') ? 'text-primary-500 bg-gray-50' : 'text-gray-700 hover:bg-gray-50'
                }`}
                onClick={handleNavLinkClick}
              >
                Blog
              </Link>
              <Link
                to="/docs"
                className={`block px-3 py-2 rounded-md text-base font-medium ${
                  isActive('/docs') ? 'text-primary-500 bg-gray-50' : 'text-gray-700 hover:bg-gray-50'
                }`}
                onClick={handleNavLinkClick}
              >
                Documentation
              </Link>
              <Link
                to="/contact"
                className={`block px-3 py-2 rounded-md text-base font-medium ${
                  isActive('/contact') ? 'text-primary-500 bg-gray-50' : 'text-gray-700 hover:bg-gray-50'
                }`}
                onClick={handleNavLinkClick}
              >
                Contact
              </Link>
              
              {isAuthenticated ? (
                <>
                  <Link
                    to="/dashboard"
                    className="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50"
                    onClick={handleNavLinkClick}
                  >
                    Dashboard
                  </Link>
                  <button
                    onClick={() => {
                      logout();
                      handleNavLinkClick();
                    }}
                    className="w-full text-left block px-3 py-2 rounded-md text-base font-medium text-error-600 hover:bg-gray-50"
                  >
                    Logout
                  </button>
                </>
              ) : (
                <div className="mt-4 space-y-2 pt-3 border-t border-gray-200">
                  <Link 
                    to="/login"
                    onClick={handleNavLinkClick}
                  >
                    <Button variant="outline" fullWidth>Log In</Button>
                  </Link>
                  <Link 
                    to="/register"
                    onClick={handleNavLinkClick}
                  >
                    <Button variant="primary" fullWidth>Sign Up Free</Button>
                  </Link>
                </div>
              )}
            </div>
          </div>
        )}
      </Container>
    </header>
  );
};

export default Header;