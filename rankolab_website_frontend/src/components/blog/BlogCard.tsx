import React from 'react';
import { Link } from 'react-router-dom';
import { Clock } from 'lucide-react';
import { BlogPost } from '../../lib/types';
import Card from '../ui/Card';

interface BlogCardProps {
  post: BlogPost;
}

export default function BlogCard({ post }: BlogCardProps) {
  return (
    <Card className="flex flex-col h-full">
      <img 
        src={post.image} 
        alt={post.title}
        className="w-full h-48 object-cover rounded-t-lg"
      />
      <div className="p-6 flex flex-col flex-grow">
        <h3 className="text-xl font-semibold mb-2 text-gray-900">
          {post.title}
        </h3>
        <p className="text-gray-600 mb-4 flex-grow">
          {post.excerpt}
        </p>
        <div className="flex items-center justify-between text-sm text-gray-500">
          <span>{post.author}</span>
          <div className="flex items-center gap-1">
            <Clock className="w-4 h-4" />
            <span>{post.readTime}</span>
          </div>
        </div>
      </div>
    </Card>
  );
}