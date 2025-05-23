
import React from 'react';
import BlogCard from './BlogCard';
import FeaturedPost from './FeaturedPost';
import Container from '../ui/Container';
import { blogs } from '../../lib/mockData';

export default function BlogList() {
  const featuredPost = blogs[0];
  const regularPosts = blogs.slice(1);

  return (
    <Container>
      <div className="py-12">
        <h1 className="text-3xl font-bold mb-8">Blog</h1>
        <FeaturedPost post={featuredPost} />
        <AdComponent adSlot="blog-list-top" style={{ display: 'block', marginTop: '2rem', marginBottom: '2rem' }} />
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-12">
          {regularPosts.map((post) => (
            <BlogCard key={post.id} post={post} />
          ))}
        </div>
      </div>
    </Container>
  );
}
