import React from 'react';
import { ArrowRight } from 'lucide-react';
import Container from '../ui/Container';
import Button from '../ui/Button';
import { Link } from 'react-router-dom';

const CTASection: React.FC = () => {
  return (
    <section className="py-20 bg-gradient-to-r from-primary-600 to-accent-700 text-white">
      <Container>
        <div className="flex flex-col md:flex-row items-center justify-between">
          <div className="mb-8 md:mb-0 md:mr-8">
            <h2 className="text-3xl md:text-4xl font-bold mb-4">
              Ready to improve your search rankings?
            </h2>
            <p className="text-white/80 text-lg max-w-xl">
              Join thousands of businesses using Rankolab to track rankings, analyze competitors, and grow organic traffic.
            </p>
          </div>
          <div className="flex flex-col sm:flex-row gap-4">
            <Link to="/register">
              <Button 
                variant="accent" 
                size="lg"
                className="bg-white text-primary-600 hover:bg-gray-100 px-8"
                icon={<ArrowRight size={16} />}
                iconPosition="right"
              >
                Start Free Trial
              </Button>
            </Link>
            <Link to="/demo">
              <Button 
                variant="outline" 
                size="lg"
                className="border-white text-white hover:bg-white/10 px-8"
              >
                Request Demo
              </Button>
            </Link>
          </div>
        </div>
      </Container>
    </section>
  );
};

export default CTASection;