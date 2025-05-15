import React, { useState } from 'react';
import { Check } from 'lucide-react';
import Container from '../ui/Container';
import Button from '../ui/Button';
import { Link } from 'react-router-dom';
import { pricingPlans } from '../../lib/mockData';

const Pricing: React.FC = () => {
  const [billingPeriod, setBillingPeriod] = useState<'monthly' | 'yearly'>('monthly');

  const toggleBillingPeriod = () => {
    setBillingPeriod(billingPeriod === 'monthly' ? 'yearly' : 'monthly');
  };

  // Calculate yearly prices (20% discount)
  const getPrice = (basePrice: number) => {
    if (billingPeriod === 'yearly') {
      return Math.round(basePrice * 12 * 0.8);
    }
    return basePrice;
  };

  return (
    <section className="py-24 bg-white" id="pricing">
      <Container>
        <div className="text-center max-w-3xl mx-auto mb-12">
          <h2 className="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            Simple, Transparent Pricing
          </h2>
          <p className="text-lg text-gray-600 mb-8">
            Choose the plan that's right for you and start improving your SEO today.
          </p>

          {/* Billing toggle */}
          <div className="flex items-center justify-center mb-8">
            <span className={`text-sm font-medium ${billingPeriod === 'monthly' ? 'text-gray-900' : 'text-gray-500'}`}>
              Monthly
            </span>
            <button
              onClick={toggleBillingPeriod}
              className="relative inline-flex items-center mx-4 h-6 w-12 rounded-full"
              aria-pressed={billingPeriod === 'yearly'}
            >
              <span className="sr-only">Toggle billing period</span>
              <span
                className={`absolute h-6 w-12 rounded-full transition ${
                  billingPeriod === 'yearly' ? 'bg-primary-500' : 'bg-gray-300'
                }`}
              />
              <span
                className={`absolute h-4 w-4 translate-x-1 transform rounded-full bg-white transition-transform ${
                  billingPeriod === 'yearly' ? 'translate-x-7' : ''
                }`}
              />
            </button>
            <span className={`text-sm font-medium ${billingPeriod === 'yearly' ? 'text-gray-900' : 'text-gray-500'}`}>
              Yearly <span className="text-success-600 font-semibold ml-1">Save 20%</span>
            </span>
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          {pricingPlans.map((plan) => (
            <div
              key={plan.id}
              className={`relative rounded-xl border ${
                plan.mostPopular
                  ? 'border-primary-200 shadow-primary'
                  : 'border-gray-200 shadow-soft'
              } bg-white overflow-hidden transition-all duration-300 hover:shadow-medium`}
            >
              {plan.mostPopular && (
                <div className="absolute top-0 right-0 left-0 bg-primary-500 text-white text-xs font-bold py-1 text-center">
                  MOST POPULAR
                </div>
              )}
              <div className={`p-6 ${plan.mostPopular ? 'pt-8' : ''}`}>
                <h3 className="text-xl font-bold text-gray-900 mb-2">{plan.name}</h3>
                <p className="text-gray-600 text-sm mb-4">{plan.description}</p>
                <div className="mt-6 mb-6">
                  <span className="text-4xl font-bold text-gray-900">${getPrice(plan.price)}</span>
                  <span className="text-gray-600 ml-2">
                    {billingPeriod === 'monthly' ? '/month' : '/year'}
                  </span>
                </div>
                <Link to={`/register?plan=${plan.id}`}>
                  <Button
                    variant={plan.mostPopular ? 'primary' : 'outline'}
                    fullWidth
                  >
                    {plan.id === 'free' ? 'Sign Up Free' : 'Start Free Trial'}
                  </Button>
                </Link>
              </div>
              <div className="border-t border-gray-100 p-6">
                <ul className="space-y-3">
                  {plan.features.map((feature, index) => (
                    <li key={index} className="flex items-start">
                      <span className="mr-2 mt-0.5 text-success-500">
                        <Check size={16} className="flex-shrink-0" />
                      </span>
                      <span className="text-gray-700 text-sm">{feature}</span>
                    </li>
                  ))}
                </ul>
              </div>
            </div>
          ))}
        </div>

        <div className="mt-16 text-center bg-gray-50 rounded-xl p-8 max-w-3xl mx-auto">
          <h3 className="text-2xl font-bold text-gray-900 mb-3">
            Enterprise Solutions
          </h3>
          <p className="text-gray-600 mb-6">
            Need a custom solution for your large organization or agency? We offer tailored plans with dedicated support, custom integrations, and more.
          </p>
          <Link to="/contact">
            <Button variant="primary">Contact Sales</Button>
          </Link>
        </div>
      </Container>
    </section>
  );
};

export default Pricing;