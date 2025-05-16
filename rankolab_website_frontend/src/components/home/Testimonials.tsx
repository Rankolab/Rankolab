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
  }  return (
    <section className="py-24 bg-white">
      <Container>
        <div className="text-center max-w-3xl mx-auto mb-16">
          <span className="text-sm font-semibold text-secondary-600 uppercase tracking-wider">Social Proof</span>
          <h2 className="text-3xl md:text-4xl font-bold text-gray-900 mt-2 mb-4">
            Trusted by SEO Professionals Like You
          </h2>
          <p className="text-lg text-gray-600">
            Hear what our satisfied customers have to say about their Rankolab experience and results.
          </p>
        </div>

        <div className="max-w-3xl mx-auto">
          <div className="relative bg-gradient-to-br from-primary-500 to-accent-500 rounded-2xl shadow-primary p-8 md:p-12 text-white">
            {/* Decorative elements */}
            <div className="absolute -top-4 -left-4 w-16 h-16 bg-white/20 rounded-full opacity-50"></div>
            <div className="absolute -bottom-4 -right-4 w-24 h-24 bg-white/20 rounded-lg transform rotate-12 opacity-50"></div>
            
            <div className="absolute top-8 right-8 text-7xl text-white/30 font-serif transform scale-x-[-1]">
              ”
            </div>
            <div className="absolute bottom-8 left-8 text-7xl text-white/30 font-serif">
              “
            </div>

            <div className="relative z-10">
              <div className="min-h-[160px] md:min-h-[120px]">
                <p className="text-xl md:text-2xl italic leading-relaxed mb-8">
                  {testimonials[currentIndex].content}
                </p>
              </div>

              <div className="flex items-center mt-6 pt-6 border-t border-white/30">
                {testimonials[currentIndex].author.avatar ? (
                  <img
                    src={testimonials[currentIndex].author.avatar}
                    alt={testimonials[currentIndex].author.name}
                    className="w-14 h-14 rounded-full object-cover mr-5 border-2 border-white/50 shadow-md"
                  />
                ) : (
                  <div className="w-14 h-14 rounded-full bg-white/30 text-white flex items-center justify-center mr-5 text-xl font-semibold">
                    {testimonials[currentIndex].author.name
                      .split(\' \")
                      .map((n) => n[0])
                      .join(\'\")}
                  </div>
                )}
                <div>
                  <h4 className="font-semibold text-lg text-white">
                    {testimonials[currentIndex].author.name}
                  </h4>
                  <p className="text-sm text-primary-100">
                    {testimonials[currentIndex].author.title}, {testimonials[currentIndex].author.company}
                  </p>
                </div>
              </div>
            </div>
          </div>

          {/* Navigation arrows */}
          <div className="flex justify-center mt-10 space-x-4">
            <button 
              onClick={prevTestimonial}
              className="p-3 rounded-full bg-white border border-gray-300 shadow-md text-gray-600 hover:bg-gray-100 hover:text-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-opacity-50 transition-all duration-200"
              aria-label="Previous testimonial"
            >
              <ChevronLeft size={22} />
            </button>
            <div className="flex space-x-2.5 items-center">
              {testimonials.map((_, index) => (
                <button
                  key={index}
                  onClick={() => setCurrentIndex(index)}
                  className={`w-3 h-3 rounded-full transition-all duration-300 ease-in-out transform hover:scale-125 ${
                    index === currentIndex ? \'bg-primary-500 scale-110\': \'bg-gray-300 hover:bg-gray-400\'
                  }`}
                  aria-label={`Go to testimonial ${index + 1}`}
                />
              ))}
            </div>
            <button 
              onClick={nextTestimonial}
              className="p-3 rounded-full bg-white border border-gray-300 shadow-md text-gray-600 hover:bg-gray-100 hover:text-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-opacity-50 transition-all duration-200"
              aria-label="Next testimonial"
            >
              <ChevronRight size={22} />
            </button>
          </div>
        </div>
      </Container>
    </section>
  );export default Testimonials;