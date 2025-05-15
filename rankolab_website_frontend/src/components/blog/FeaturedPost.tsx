import React from 'react';
import { Link } from 'react-router-dom';
import { Clock } from 'lucide-react';
import { BlogPost } from '../../lib/types';
import { formatDate } from '../../lib/utils';
import Container from '../ui/Container';
import Button from '../ui/Button';

interface FeaturedPostProps {
  post: BlogPost;
}

const FeaturedPost: React.FC<FeaturedPostProps> = ({ post }) => {
  return (
    <div className="bg-gray-50 py-16">
      <Container>
        <div className="grid md:grid-cols-2 gap-12 items-center">
          <div className="order-2 md:order-1">
            <div className="mb-4">
              <span className="bg-accent-500 text-white text-xs font-medium px-2.5 py-1 rounded">
                {post.category}
              </span>
            </div>
            
            <h1 className="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
              {post.title}
            </h1>
            
            <div className="flex items-center text-sm text-gray-500 mb-4">
              <span className="flex items-center">
                <Clock size={16} className="mr-1" />
                {post.readTime} min read
              </span>
              <span className="mx-2">•</span>
              <span>{formatDate(post.publishedAt)}</span>
            </div>
            
            <p className="text-gray-600 text-lg mb-6">
              {post.excerpt}
            </p>
            
            <div className="flex items-center mb-6">
              {post.author.avatar ? (
                <img 
                  src={post.author.avatar} 
                  alt={post.author.name} 
                  className="w-10 h-10 rounded-full mr-3"
                />
              ) : (
                <div className="w-10 h-10 bg-primary-100 text-primary-700 rounded-full flex items-center justify-center mr-3">
                  {post.author.name.charAt(0)}
                </div>
              )}
              <span className="font-medium text-gray-900">By {post.author.name}</span>
            </div>
            
            <Link to={`/blog/${post.slug}`}>
              <Button>Read Article</Button>
            </Link>
          </div>
          
          <div className="order-1 md:order-2">
            <div className="rounded-xl overflow-hidden shadow-medium">
              <img 
                src={post.coverImage} 
                alt={post.title} 
                className="w-full h-full object-cover"
              />
            </div>
          </div>
        </div>
      </Container>
    </div>
  );
};

export default FeaturedPost;