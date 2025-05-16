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
    <section className="py-20 md:py-28 bg-gradient-to-b from-gray-50 to-white" id="pricing">
      <Container>
        <div className="text-center max-w-3xl mx-auto mb-16">
          <span className="text-sm font-semibold text-primary-600 uppercase tracking-wider">Pricing Plans</span>
          <h2 className="text-4xl md:text-5xl font-bold text-gray-900 mt-2 mb-5">
            Simple, Transparent Pricing
          </h2>
          <p className="text-lg text-gray-600 leading-relaxed">
            Choose the plan that fits your needs and budget. All plans come with a 14-day free trial, no credit card required.
          </p>
        </div>

        {/* Billing toggle */}
        <div className="flex items-center justify-center mb-12">
          <span className={`text-base font-medium transition-colors duration-300 ${billingPeriod === "monthly" ? "text-gray-900" : "text-gray-500"}`}>
            Monthly
          </span>
          <button
            onClick={toggleBillingPeriod}
            className="relative inline-flex items-center mx-4 h-7 w-14 rounded-full bg-gray-200 hover:bg-gray-300 transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
            aria-pressed={billingPeriod === "yearly"}
          >
            <span className="sr-only">Toggle billing period</span>
            <span
              className={`absolute h-full w-1/2 rounded-full transition-all duration-300 ease-in-out ${
                billingPeriod === "yearly" ? "bg-primary-500 transform translate-x-full" : "bg-primary-500 transform translate-x-0"
              }`}
            />
             <span
                className={`absolute inset-0 flex items-center justify-center w-1/2 text-xs font-semibold transition-opacity duration-300 ${
                  billingPeriod === "monthly" ? "text-white opacity-100" : "text-gray-600 opacity-0"
                }`}
              >
                ON
              </span>
              <span
                className={`absolute inset-0 flex items-center justify-center w-1/2 ml-auto text-xs font-semibold transition-opacity duration-300 ${
                  billingPeriod === "yearly" ? "text-white opacity-100" : "text-gray-600 opacity-0"
                }`}
              >
                OFF
              </span>
          </button>
          <span className={`text-base font-medium transition-colors duration-300 ${billingPeriod === "yearly" ? "text-gray-900" : "text-gray-500"}`}>
            Yearly <span className="text-green-600 font-semibold ml-1 px-2 py-0.5 bg-green-100 rounded-md">Save 20%</span>
          </span>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 items-stretch">
          {pricingPlans.map((plan) => (
            <div
              key={plan.id}
              className={`relative rounded-2xl border flex flex-col ${
                plan.mostPopular
                  ? "border-primary-500 shadow-2xl shadow-primary-500/30 ring-2 ring-primary-500"
                  : "border-gray-200 shadow-xl"
              } bg-white overflow-hidden transition-all duration-300 hover:shadow-2xl hover:scale-[1.02]`}
            >
              {plan.mostPopular && (
                <div className="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-primary-500 text-white text-xs font-bold py-1.5 px-4 rounded-full shadow-lg uppercase tracking-wider">
                  Most Popular
                </div>
              )}
              <div className={`p-8 flex-grow ${plan.mostPopular ? "pt-10" : "pt-8"}`}>
                <h3 className="text-2xl font-bold text-gray-800 mb-2">{plan.name}</h3>
                <p className="text-gray-500 text-sm mb-6 min-h-[40px]">{plan.description}</p>
                <div className="my-8 text-center">
                  <span className="text-5xl font-extrabold text-gray-900">${getPrice(plan.price)}</span>
                  <span className="text-gray-500 ml-1">
                    {billingPeriod === "monthly" ? "/mo" : "/yr"}
                  </span>
                </div>
                <Link to={`/register?plan=${plan.id}${billingPeriod === "yearly" ? "&billing=yearly" : ""}`}>
                  <Button
                    variant={plan.mostPopular ? "primary" : "outline"}
                    size="lg"
                    fullWidth
                    className="py-3 text-base font-semibold shadow-md hover:shadow-lg transition-shadow duration-300"
                  >
                    {plan.id === "free" ? "Sign Up Free" : "Choose Plan"}
                  </Button>
                </Link>
              </div>
              <div className="border-t border-gray-200 bg-gray-50/50 p-8">
                <p className="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wider">What\s included:</p>
                <ul className="space-y-3">
                  {plan.features.map((feature, index) => (
                    <li key={index} className="flex items-start">
                      <span className={`mr-3 mt-1 flex-shrink-0 w-5 h-5 rounded-full flex items-center justify-center ${
                        plan.mostPopular ? "bg-primary-500 text-white" : "bg-green-500 text-white"
                      }`}>
                        <Check size={14} />
                      </span>
                      <span className="text-gray-600 text-sm">{feature}</span>
                    </li>
                  ))}
                </ul>
              </div>
            </div>
          ))}
        </div>

        <div className="mt-20 text-center bg-slate-100 rounded-xl p-10 max-w-4xl mx-auto shadow-lg border border-slate-200">
          <h3 className="text-2xl md:text-3xl font-bold text-gray-800 mb-4">
            Need Something More?
          </h3>
          <p className="text-gray-600 mb-8 max-w-xl mx-auto leading-relaxed">
            We offer custom enterprise solutions for large organizations and agencies. Get in touch for dedicated support, custom integrations, volume discounts, and more.
          </p>
          <Link to="/contact">
            <Button variant="primary" size="lg" className="px-8 py-3 text-base font-semibold shadow-md hover:shadow-lg transition-shadow duration-300">Contact Sales</Button>
          </Link>
        </div>
      </Container>
    </section>
  );