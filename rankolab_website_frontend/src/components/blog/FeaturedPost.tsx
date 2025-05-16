import React from 'react';
import { Link } from 'react-router-dom';
import { Clock } from 'lucide-react';
import { BlogPost } from '../../lib/types';
import Container from '../ui/Container';
import Button from '../ui/Button';

interface FeaturedPostProps {
  post: BlogPost;
}

export default function FeaturedPost({ post }: FeaturedPostProps) {
  return (
    <div className="bg-gray-50 py-12">
      <Container>
        <div className="grid md:grid-cols-2 gap-8 items-center">
          <div>
            <h2 className="text-3xl font-bold mb-4">{post.title}</h2>
            <p className="text-gray-600 mb-6">{post.excerpt}</p>
            <div className="flex items-center gap-4 mb-6">
              <span className="text-gray-500">{post.author}</span>
              <div className="flex items-center gap-1 text-gray-500">
                <Clock className="w-4 h-4" />
                <span>{post.readTime}</span>
              </div>
            </div>
            <Button as={Link} to={`/blog/${post.id}`} variant="primary">
              Read More
            </Button>
          </div>
          <div>
            <img 
              src={post.image}
              alt={post.title}
              className="w-full h-[400px] object-cover rounded-xl shadow-lg"
            />
          </div>
        </div>
      </Container>
    </div>
  );
}