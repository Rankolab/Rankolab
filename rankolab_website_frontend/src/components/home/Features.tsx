import React from 'react';
import { 
  BarChart, Zap, Globe, Lock, Search, Users, Code, LineChart 
} from 'lucide-react';
import Container from '../ui/Container';
import { features } from '../../lib/mockData';

const FeatureIcon: React.FC<{ name: string }> = ({ name }) => {
  const iconMap: Record<string, React.ReactElement> = {
    'BarChart': <BarChart className="w-6 h-6" />,
    'Zap': <Zap className="w-6 h-6" />,
    'Globe': <Globe className="w-6 h-6" />,
    'Lock': <Lock className="w-6 h-6" />,
    'Search': <Search className="w-6 h-6" />,
    'Users': <Users className="w-6 h-6" />,
    'Code': <Code className="w-6 h-6" />,
    'LineChart': <LineChart className="w-6 h-6" />,
  };

  return (
    <div className="w-12 h-12 rounded-lg bg-primary-100 text-primary-500 flex items-center justify-center shadow-sm border border-primary-200">
      {iconMap[name] || <BarChart className="w-6 h-6" />}
    </div>
  );
};

const Features: React.FC = () => {
  return (
    <section className="py-24 bg-gray-50">
      <Container>
        <div className="text-center max-w-3xl mx-auto mb-16">
          <span className="text-sm font-semibold text-primary-600 uppercase tracking-wider">Core Capabilities</span>
          <h2 className="text-3xl md:text-4xl font-bold text-gray-900 mt-2 mb-4">
            Powerful Features for SEO Professionals
          </h2>
          <p className="text-lg text-gray-600">
            Rankolab offers comprehensive tools to help you monitor, analyze, and improve your search engine rankings.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {features.map((feature) => (
            <div 
              key={feature.id} 
              className="bg-white p-8 rounded-xl border border-gray-200 shadow-lg hover:shadow-primary/20 transition-all duration-300 transform hover:-translate-y-1"
            >
              <FeatureIcon name={feature.icon} />
              <h3 className="text-xl font-semibold text-gray-800 mt-5 mb-3">
                {feature.title}
              </h3>
              <p className="text-gray-500 text-sm leading-relaxed">
                {feature.description}
              </p>
            </div>
          ))}
        </div>
      </Container>
    </section>
  );
};

export default Features;