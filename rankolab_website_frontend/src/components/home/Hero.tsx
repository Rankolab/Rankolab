import React from 'react';
import { Link } from 'react-router-dom';
import { ArrowRight } from 'lucide-react';
import Container from '../ui/Container';
import Button from '../ui/Button';

const Hero: React.FC = () => {
  return (
    <section className="relative bg-gradient-to-b from-white to-gray-50 pt-24 pb-32">
      {/* Background pattern (dots grid) */}
      <div className="absolute inset-0 overflow-hidden">
        <div className="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-bl from-primary-50/50 to-transparent"></div>
        <div className="absolute bottom-0 left-0 w-1/3 h-1/2 bg-gradient-to-tr from-secondary-50/50 to-transparent rounded-full blur-3xl"></div>
      </div>

      <Container>
        <div className="relative z-10 flex flex-col items-center text-center">
          <div className="inline-flex items-center px-3 py-1 rounded-full bg-primary-50 text-primary-600 text-sm font-medium mb-6 border border-primary-100">
            <span className="animate-pulse inline-block w-2 h-2 rounded-full bg-primary-500 mr-2"></span>
            <span>Discover the new Rankolab v3.0</span>
          </div>
          
          <h1 className="text-4xl sm:text-5xl md:text-6xl font-bold text-gray-900 mb-6 max-w-5xl">
            <span className="block">Track Your SEO Rankings with</span>
            <span className="relative">
              <span className="relative z-10 text-transparent bg-clip-text bg-gradient-to-r from-primary-500 to-accent-500">Precision and Clarity</span>
              <span className="absolute bottom-0 left-0 w-full h-3 bg-secondary-100 -z-10 transform translate-y-2 skew-x-3"></span>
            </span>
          </h1>
          
          <p className="text-xl text-gray-600 max-w-3xl mb-10">
            Monitor your search rankings, analyze competitors, and grow your organic traffic with
            our powerful SEO platform. Get insights that drive results.
          </p>
          
          <div className="flex flex-col sm:flex-row items-center justify-center gap-4 mb-12">
            <Link to="/register">
              <Button 
                size="lg" 
                className="px-8"
                icon={<ArrowRight size={18} />}
                iconPosition="right"
              >
                Start Free Trial
              </Button>
            </Link>
            <Link to="/demo">
              <Button 
                variant="outline" 
                size="lg" 
                className="px-8"
              >
                Request Demo
              </Button>
            </Link>
          </div>
          
          <div className="text-gray-500 text-sm flex items-center gap-3">
            <span>✓ No credit card required</span>
            <span className="h-1.5 w-1.5 rounded-full bg-gray-300"></span>
            <span>✓ 14-day free trial</span>
            <span className="h-1.5 w-1.5 rounded-full bg-gray-300"></span>
            <span>✓ Cancel anytime</span>
          </div>
          
          {/* Hero Image */}
          <div className="mt-16 relative w-full max-w-5xl">
            <div className="absolute inset-0 -m-6 bg-gradient-to-r from-primary-500/10 to-accent-500/10 rounded-xl blur-xl"></div>
            <div className="relative shadow-xl rounded-xl overflow-hidden border border-gray-200 bg-white">
              <img 
                src="https://images.pexels.com/photos/7947941/pexels-photo-7947941.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2" 
                alt="Rankolab Dashboard" 
                className="w-full h-auto"
                loading="lazy"
              />
            </div>
          </div>
        </div>
      </Container>
    </section>
  );
};

export default Hero;