import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Layout from './components/layout/Layout';
import HomePage from './components/home/Hero';
import DashboardPage from './components/dashboard/DashboardHeader';
import LoginForm from './components/auth/LoginForm';
import RegisterForm from './components/auth/RegisterForm';
import ContentPlanner from './components/dashboard/ContentPlanner';
import { AuthProvider } from './lib/authContext';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider, useAuth } from './lib/authContext';

// Layout
import Layout from './components/layout/Layout';
import DashboardHeader from './components/dashboard/DashboardHeader';

// Home page components
import Hero from './components/home/Hero';
import Features from './components/home/Features';
import Testimonials from './components/home/Testimonials';
import Pricing from './components/home/Pricing';
import FAQ from './components/home/FAQ';
import CTASection from './components/home/CTASection';

// Auth components
import LoginForm from './components/auth/LoginForm';
import RegisterForm from './components/auth/RegisterForm';

// Blog components
import FeaturedPost from './components/blog/FeaturedPost';
import BlogCard from './components/blog/BlogCard';
import BlogDetail from './components/blog/BlogDetail';
import BlogList from './components/blog/BlogList';
import ContentPlanner from './components/dashboard/ContentPlanner';

// Import mock data
import { blogPosts } from './lib/mockData';

// Pages
const HomePage = () => (
  <Layout>
    <Hero />
    <Features />
    <Testimonials />
    <Pricing />
    <FAQ />
    <CTASection />
  </Layout>
);

const LoginPage = () => (
  <Layout>
    <div className="flex items-center justify-center min-h-[80vh] py-12 px-4 sm:px-6 lg:px-8">
      <LoginForm />
    </div>
  </Layout>
);

const RegisterPage = () => (
  <Layout>
    <div className="flex items-center justify-center min-h-[80vh] py-12 px-4 sm:px-6 lg:px-8">
      <RegisterForm />
    </div>
  </Layout>
);

const BlogPage = () => {
  const featuredPost = blogPosts[0];
  const regularPosts = blogPosts.slice(1);

  return (
    <Layout>
      <FeaturedPost post={featuredPost} />
      <div className="py-16 bg-white">
        <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
          <h2 className="text-3xl font-bold text-gray-900 mb-8">Latest Articles</h2>
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {regularPosts.map((post) => (
              <BlogCard key={post.id} post={post} />
            ))}
          </div>
        </div>
      </div>
    </Layout>
  );
};

const BlogDetailPage = () => (
  <Layout>
    <BlogDetail />
  </Layout>
);

// Dashboard pages
const DashboardLayout = ({ children }: { children: React.ReactNode }) => {
  return (
    <div className="min-h-screen bg-gray-50">
      <DashboardHeader />
      <div className="pt-16 pb-12">
        {children}
      </div>
    </div>
  );
};

const DashboardPage = () => (
  <DashboardLayout>
    <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <h1 className="text-2xl font-bold text-gray-900 mb-6">Dashboard</h1>
      <div className="bg-white rounded-lg shadow p-6">
        <p>Welcome to your Rankolab dashboard. Start tracking your SEO performance now.</p>
      </div>
    </div>
  </DashboardLayout>
);

// Protected route component
const ProtectedRoute = ({ children }: { children: React.ReactNode }) => {
  const { isAuthenticated, isLoading } = useAuth();

  if (isLoading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary-500"></div>
      </div>
    );
  }

  if (!isAuthenticated) {
    return <Navigate to="/login" replace />;
  }

  return <>{children}</>;
};

function App() {
  return (
    <AuthProvider>
      <Router>
        <Routes>
          <Route path="/" element={<HomePage />} />
          <Route path="/login" element={<LoginPage />} />
          <Route path="/register" element={<RegisterPage />} />
          <Route path="/blog" element={<BlogPage />} />
          <Route path="/blog/:slug" element={<BlogDetailPage />} />
          <Route path="/dashboard/content-planner" element={<ContentPlanner />} />

          {/* Protected Routes */}
          <Route path="/dashboard" element={
            <ProtectedRoute>
              <DashboardPage />
            </ProtectedRoute>
          } />

          {/* 404 Route */}
          <Route path="*" element={
            <Layout>
              <div className="min-h-[60vh] flex flex-col items-center justify-center">
                <h1 className="text-4xl font-bold text-gray-900 mb-4">404</h1>
                <p className="text-xl text-gray-600 mb-8">Page not found</p>
                <a href="/" className="text-primary-500 hover:underline">
                  Return to homepage
                </a>
              </div>
            </Layout>
          } />
        </Routes>
      </Router>
    </AuthProvider>
  );
}

export default App;