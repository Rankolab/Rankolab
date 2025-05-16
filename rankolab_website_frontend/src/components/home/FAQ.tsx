import React, { useState } from 'react';
import { ChevronDown, ChevronUp } from 'lucide-react';
import Container from '../ui/Container';
import { faqItems } from '../../lib/mockData';

interface FAQItemProps {
  question: string;
  answer: string;
  isOpen: boolean;
  toggleOpen: () => void;
}

const FAQItem: React.FC<FAQItemProps> = ({ question, answer, isOpen, toggleOpen }) => {
  return (
    <div className="border-b border-gray-200 last:border-b-0">
      <button
        className="flex justify-between items-center w-full py-6 px-6 md:px-8 text-left hover:bg-gray-50 transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 rounded-t-lg"
        onClick={toggleOpen}
        aria-expanded={isOpen}
      >
        <h3 className={`text-lg font-semibold ${isOpen ? "text-primary-600" : "text-gray-800"}`}>{question}</h3>
        <span className={`ml-4 flex-shrink-0 transform transition-transform duration-300 ${isOpen ? "rotate-180 text-primary-600" : "text-gray-400"}`}>
          <ChevronDown size={24} />
        </span>
      </button>
      {isOpen && (
        <div className="pb-6 px-6 md:px-8 bg-gray-50 rounded-b-lg">
          <p className="text-gray-600 leading-relaxed pt-2 border-t border-gray-200">{answer}</p>
        </div>
      )}
    </div>
  );
};

const FAQ: React.FC = () => {
  const [openIndex, setOpenIndex] = useState<number | null>(0); // Allow null to have none open initially if desired

  const toggleFAQ = (index: number) => {
    setOpenIndex(openIndex === index ? null : index);
  };

  return (
    <section className="py-20 md:py-28 bg-white">
      <Container>
        <div className="text-center max-w-3xl mx-auto mb-16">
          <span className="text-sm font-semibold text-secondary-600 uppercase tracking-wider">Help Center</span>
          <h2 className="text-4xl md:text-5xl font-bold text-gray-900 mt-2 mb-5">
            Frequently Asked Questions
          </h2>
          <p className="text-lg text-gray-600 leading-relaxed">
            Find quick answers to common questions about Rankolab, our features, and how to get started.
          </p>
        </div>

        <div className="max-w-3xl mx-auto bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">
          {faqItems.map((item, index) => (
            <FAQItem
              key={index}
              question={item.question}
              answer={item.answer}
              isOpen={openIndex === index}
              toggleOpen={() => toggleFAQ(index)}
            />
          ))}
        </div>

        <div className="text-center mt-16">
          <p className="text-gray-700 text-lg">
            Can\t find the answer you\re looking for? 
            <a href="/contact" className="text-primary-600 hover:text-primary-700 font-semibold underline ml-1 transition-colors duration-200">Contact our support team</a>.
          </p>
        </div>
      </Container>
    </section>
  );
};

export default FAQ;