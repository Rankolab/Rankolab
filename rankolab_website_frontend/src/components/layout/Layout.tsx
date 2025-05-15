import React from 'react';
import Header from './Header';
import Footer from './Footer';

interface LayoutProps {
  children: React.ReactNode;
  withPadding?: boolean;
}

const Layout: React.FC<LayoutProps> = ({ children, withPadding = true }) => {
  return (
    <div className="flex flex-col min-h-screen">
      <Header />
      <main className={`flex-grow ${withPadding ? 'pt-24' : ''}`}>
        {children}
      </main>
      <Footer />
    </div>
  );
};

export default Layout;