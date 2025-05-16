
import React from 'react';
import { useParams } from 'react-router-dom';
import Container from '../ui/Container';
import { blogs } from '../../lib/mockData';

export default function BlogDetail() {
  const { slug } = useParams();
  const post = blogs.find(p => p.slug === slug);

  if (!post) {
    return (
      <Container>
        <div className="py-12">
          <h1 className="text-2xl font-bold">Post not found</h1>
        </div>
      </Container>
    );
  }

  return (
    <Container>
      <article className="py-12">
        <div className="mb-8">
          <img 
            src={post.coverImage} 
            alt={post.title}
            className="w-full h-[400px] object-cover rounded-xl"
          />
        </div>
        
        <h1 className="text-4xl font-bold text-gray-900 mb-4">
          {post.title}
        </h1>
        
        <div className="flex items-center gap-4 text-gray-600 mb-8">
          <div className="flex items-center gap-2">
            {post.author.avatar ? (
              <img 
                src={post.author.avatar}
                alt={post.author.name}
                className="w-8 h-8 rounded-full"
              />
            ) : (
              <div className="w-8 h-8 bg-primary-100 text-primary-700 rounded-full flex items-center justify-center">
                {post.author.name.charAt(0)}
              </div>
            )}
            <span>{post.author.name}</span>
          </div>
          <span>•</span>
          <span>{post.readTime} min read</span>
        </div>

        <div className="prose prose-lg max-w-none">
          {post.content}
        </div>
      </article>
    </Container>
  );
}
