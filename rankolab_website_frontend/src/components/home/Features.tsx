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
    <div className="w-12 h-12 rounded-lg bg-primary-50 text-primary-500 flex items-center justify-center">
      {iconMap[name] || <BarChart className="w-6 h-6" />}
    </div>
  );
};

const Features: React.FC = () => {
  return (
    <section className="py-24 bg-white">
      <Container>
        <div className="text-center max-w-3xl mx-auto mb-16">
          <h2 className="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            Powerful Features for SEO Professionals
          </h2>
          <p className="text-lg text-gray-600">
            Rankolab offers comprehensive tools to help you monitor, analyze, and improve your search engine rankings.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          {features.map((feature) => (
            <div 
              key={feature.id} 
              className="bg-white p-6 rounded-xl border border-gray-100 shadow-soft hover:shadow-medium transition-shadow duration-300"
            >
              <FeatureIcon name={feature.icon} />
              <h3 className="text-xl font-semibold text-gray-900 mt-4 mb-2">
                {feature.title}
              </h3>
              <p className="text-gray-600">
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