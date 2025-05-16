
import React from 'react';
import { useParams, Link } from 'react-router-dom';
import { Clock, ArrowLeft } from 'lucide-react';
import Container from '../ui/Container';
import { blogs } from '../../lib/mockData';

export default function BlogDetail() {
  const { id } = useParams();
  const post = blogs.find(p => p.id === id);

  if (!post) {
    return (
      <Container>
        <div className="py-12">
          <h1 className="text-2xl font-bold text-gray-900">Post not found</h1>
          <Link to="/blog" className="text-primary-500 hover:underline mt-4 inline-block">
            Back to blog
          </Link>
        </div>
      </Container>
    );
  }

  return (
    <Container>
      <article className="py-12 max-w-4xl mx-auto">
        <Link 
          to="/blog" 
          className="inline-flex items-center text-gray-600 hover:text-primary-500 mb-8"
        >
          <ArrowLeft className="w-4 h-4 mr-2" />
          Back to blog
        </Link>
        
        <img 
          src={post.image} 
          alt={post.title}
          className="w-full h-[400px] object-cover rounded-xl mb-8"
        />
        
        <h1 className="text-4xl font-bold text-gray-900 mb-4">
          {post.title}
        </h1>
        
        <div className="flex items-center gap-4 text-gray-600 mb-8">
          <span>{post.author}</span>
          <span>•</span>
          <div className="flex items-center gap-1">
            <Clock className="w-4 h-4" />
            <span>{post.readTime}</span>
          </div>
          <span>•</span>
          <span>{post.date}</span>
        </div>

        <div className="prose prose-lg max-w-none">
          {post.content}
        </div>
      </article>
    </Container>
  );
}
