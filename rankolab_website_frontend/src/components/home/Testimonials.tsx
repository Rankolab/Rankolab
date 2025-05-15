import React, { useState } from 'react';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import Container from '../ui/Container';
import { testimonials } from '../../lib/mockData';

const Testimonials: React.FC = () => {
  const [currentIndex, setCurrentIndex] = useState(0);

  const nextTestimonial = () => {
    setCurrentIndex((prevIndex) => (prevIndex + 1) % testimonials.length);
  };

  const prevTestimonial = () => {
    setCurrentIndex((prevIndex) => (prevIndex - 1 + testimonials.length) % testimonials.length);
  };

  return (
    <section className="py-24 bg-gray-50">
      <Container>
        <div className="text-center max-w-3xl mx-auto mb-16">
          <h2 className="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            Trusted by SEO Professionals
          </h2>
          <p className="text-lg text-gray-600">
            See what our customers have to say about Rankolab
          </p>
        </div>

        <div className="max-w-4xl mx-auto">
          <div className="relative bg-white rounded-2xl shadow-medium p-8 md:p-10">
            {/* Quote marks */}
            <div className="absolute top-6 left-8 text-8xl text-primary-100 font-serif">
              "
            </div>

            <div className="relative z-10">
              <div className="min-h-[180px]">
                <p className="text-lg md:text-xl text-gray-700 mb-6 relative z-10 pt-6">
                  {testimonials[currentIndex].content}
                </p>
              </div>

              <div className="flex items-center mt-8">
                {testimonials[currentIndex].author.avatar ? (
                  <img
                    src={testimonials[currentIndex].author.avatar}
                    alt={testimonials[currentIndex].author.name}
                    className="w-12 h-12 rounded-full object-cover mr-4"
                  />
                ) : (
                  <div className="w-12 h-12 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center mr-4">
                    {testimonials[currentIndex].author.name
                      .split(' ')
                      .map((n) => n[0])
                      .join('')}
                  </div>
                )}
                <div>
                  <h4 className="font-semibold text-gray-900">
                    {testimonials[currentIndex].author.name}
                  </h4>
                  <p className="text-sm text-gray-600">
                    {testimonials[currentIndex].author.title}, {testimonials[currentIndex].author.company}
                  </p>
                </div>
              </div>
            </div>
          </div>

          {/* Navigation arrows */}
          <div className="flex justify-center mt-8 space-x-4">
            <button 
              onClick={prevTestimonial}
              className="p-2 rounded-full bg-white border border-gray-200 shadow-sm text-gray-500 hover:text-primary-500 transition-colors duration-200"
              aria-label="Previous testimonial"
            >
              <ChevronLeft size={20} />
            </button>
            <div className="flex space-x-2 items-center">
              {testimonials.map((_, index) => (
                <button
                  key={index}
                  onClick={() => setCurrentIndex(index)}
                  className={`w-2.5 h-2.5 rounded-full transition-colors duration-200 ${
                    index === currentIndex ? 'bg-primary-500' : 'bg-gray-300'
                  }`}
                  aria-label={`Go to testimonial ${index + 1}`}
                />
              ))}
            </div>
            <button 
              onClick={nextTestimonial}
              className="p-2 rounded-full bg-white border border-gray-200 shadow-sm text-gray-500 hover:text-primary-500 transition-colors duration-200"
              aria-label="Next testimonial"
            >
              <ChevronRight size={20} />
            </button>
          </div>
        </div>
      </Container>
    </section>
  );
};

export default Testimonials;