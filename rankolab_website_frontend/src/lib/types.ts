export interface User {
  id: string;
  name: string;
  email: string;
  avatar?: string;
  role: 'user' | 'admin';
  licenseType?: 'free' | 'basic' | 'pro' | 'enterprise';
  licenseExpiry?: string;
}

export interface BlogPost {
  id: string;
  title: string;
  slug: string;
  excerpt: string;
  content: string;
  coverImage: string;
  category: string;
  author: {
    name: string;
    avatar?: string;
  };
  publishedAt: string;
  readTime: number;
}

export interface Feature {
  id: string;
  title: string;
  description: string;
  icon: string;
}

export interface PricingPlan {
  id: string;
  name: string;
  price: number;
  billingPeriod: 'monthly' | 'yearly';
  description: string;
  features: string[];
  mostPopular?: boolean;
}

export interface Testimonial {
  id: string;
  content: string;
  author: {
    name: string;
    title: string;
    company: string;
    avatar?: string;
  };
}

export interface FAQItem {
  question: string;
  answer: string;
}

export interface AffiliateStats {
  clicks: number;
  signups: number;
  conversions: number;
  earnings: number;
}