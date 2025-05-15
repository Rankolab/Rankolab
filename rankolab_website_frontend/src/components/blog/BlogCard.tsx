import React from 'react';
import { Link } from 'react-router-dom';
import { Clock } from 'lucide-react';
import { BlogPost } from '../../lib/types';
import { formatDate, truncate } from '../../lib/utils';
import Card from '../ui/Card';

interface BlogCardProps {
  post: BlogPost;
}

const BlogCard: React.FC<BlogCardProps> = ({ post }) => {
  return (
    <Card hoverable className="overflow-hidden h-full flex flex-col" padding="none">
      <div className="relative h-48 overflow-hidden">
        <img 
          src={post.coverImage} 
          alt={post.title} 
          className="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
        />
        <div className="absolute top-4 left-4">
          <span className="bg-accent-500 text-white text-xs font-medium px-2.5 py-1 rounded">
            {post.category}
          </span>
        </div>
      </div>
      
      <div className="p-5 flex-grow flex flex-col">
        <div className="flex items-center text-sm text-gray-500 mb-3">
          <span className="flex items-center">
            <Clock size={14} className="mr-1" />
            {post.readTime} min read
          </span>
          <span className="mx-2">•</span>
          <span>{formatDate(post.publishedAt)}</span>
        </div>
        
        <Link to={`/blog/${post.slug}`}>
          <h3 className="text-xl font-semibold text-gray-900 mb-2 hover:text-primary-500 transition-colors duration-200">
            {post.title}
          </h3>
        </Link>
        
        <p className="text-gray-600 mb-4 flex-grow">
          {truncate(post.excerpt, 120)}
        </p>
        
        <div className="flex items-center mt-auto pt-4 border-t border-gray-100">
          {post.author.avatar ? (
            <img 
              src={post.author.avatar} 
              alt={post.author.name} 
              className="w-8 h-8 rounded-full mr-3"
            />
          ) : (
            <div className="w-8 h-8 bg-primary-100 text-primary-700 rounded-full flex items-center justify-center mr-3">
              {post.author.name.charAt(0)}
            </div>
          )}
          <span className="text-sm font-medium text-gray-700">{post.author.name}</span>
        </div>
      </div>
    </Card>
  );
};

export default BlogCard;