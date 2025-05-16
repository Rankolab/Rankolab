import { BlogPost, Feature, PricingPlan, Testimonial, FAQItem, User } from './types';
import { 
  BarChart, Zap, Globe, Lock, Search, Users, Code, LineChart, 
  Bell, Layers, Shield, Key
} from 'lucide-react';

// Mock user data
export const currentUser: User | null = null; // Initially not logged in

// Blog data
export interface BlogPost {
  id: string;
  title: string;
  excerpt: string;
  content: string;
  author: string;
  date: string;
  readTime: string;
  image: string;
}

export const blogs: BlogPost[] = [
  {
    id: '1',
    title: 'Mastering SEO: A Complete Guide for 2025',
    excerpt: 'Learn the latest SEO techniques and strategies to boost your website rankings.',
    content: 'Full article content here...',
    author: 'John Smith',
    date: 'March 15, 2025',
    readTime: '8 min read',
    image: 'https://placekitten.com/800/400'
  },
  {
    id: '2',
    title: 'Content Creation Strategies That Work',
    excerpt: 'Discover proven content creation methods that engage readers.',
    content: 'Full article content here...',
    author: 'Sarah Johnson',
    date: 'March 10, 2025',
    readTime: '6 min read',
    image: 'https://placekitten.com/801/400'
  },
  {
    id: '3',
    title: 'The Future of AI in Digital Marketing',
    excerpt: 'Explore how AI is transforming the digital marketing landscape.',
    content: 'Full article content here...',
    author: 'Mike Wilson',
    date: 'March 5, 2025',
    readTime: '10 min read',
    image: 'https://placekitten.com/802/400'
  }
];

// Features data
export const features: Feature[] = [
  {
    id: '1',
    title: 'Advanced Rank Tracking',
    description: 'Monitor your search rankings across multiple search engines with daily updates and historical data.',
    icon: 'BarChart',
  },
  {
    id: '2',
    title: 'Lightning Fast Analysis',
    description: 'Get insights in seconds with our proprietary algorithm that processes data 10x faster than competitors.',
    icon: 'Zap',
  },
  {
    id: '3',
    title: 'Global Coverage',
    description: 'Track rankings in over 100 countries and 800+ locations to understand your global SEO performance.',
    icon: 'Globe',
  },
  {
    id: '4',
    title: 'Secure Data Management',
    description: 'Enterprise-grade security ensures your data and competitive intelligence remains completely private.',
    icon: 'Lock',
  },
  {
    id: '5',
    title: 'Competitor Analysis',
    description: 'Compare your performance against competitors to identify opportunities and threats.',
    icon: 'Search',
  },
  {
    id: '6',
    title: 'Team Collaboration',
    description: 'Share reports and insights with your team and clients with customizable user permissions.',
    icon: 'Users',
  },
  {
    id: '7',
    title: 'API Access',
    description: 'Integrate Rankolab data into your existing workflows with our comprehensive API.',
    icon: 'Code',
  },
  {
    id: '8',
    title: 'Advanced Reporting',
    description: 'Create beautiful, white-labeled reports that can be scheduled and automatically sent to clients.',
    icon: 'LineChart',
  },
];

// Pricing plans data
export const pricingPlans: PricingPlan[] = [
  {
    id: 'free',
    name: 'Free',
    price: 0,
    billingPeriod: 'monthly',
    description: 'Basic features for individuals getting started with SEO',
    features: [
      '100 keyword rankings',
      'Weekly updates',
      'Basic reporting',
      'Email support',
    ],
  },
  {
    id: 'basic',
    name: 'Basic',
    price: 49,
    billingPeriod: 'monthly',
    description: 'Everything you need for a growing website',
    features: [
      '500 keyword rankings',
      'Daily updates',
      'Advanced reporting',
      'Competitor tracking',
      'Email & chat support',
    ],
    mostPopular: true,
  },
  {
    id: 'pro',
    name: 'Professional',
    price: 99,
    billingPeriod: 'monthly',
    description: 'Advanced features for marketing teams and agencies',
    features: [
      '2,000 keyword rankings',
      'Real-time updates',
      'White-label reports',
      'API access',
      'Team collaboration',
      'Priority support',
    ],
  },
  {
    id: 'enterprise',
    name: 'Enterprise',
    price: 299,
    billingPeriod: 'monthly',
    description: 'Custom solutions for large businesses and agencies',
    features: [
      'Unlimited keyword rankings',
      'Custom integrations',
      'Dedicated account manager',
      'Custom reporting',
      'SLA guarantees',
      '24/7 phone support',
    ],
  },
];

// Testimonials data
export const testimonials: Testimonial[] = [
  {
    id: '1',
    content: 'Rankolab has transformed how we track and report SEO results to our clients. The interface is intuitive and the data accuracy is unmatched.',
    author: {
      name: 'Sarah Johnson',
      title: 'SEO Director',
      company: 'Digital Growth Agency',
      avatar: 'https://images.pexels.com/photos/1181686/pexels-photo-1181686.jpeg?auto=compress&cs=tinysrgb&w=150',
    },
  },
  {
    id: '2',
    content: 'We switched from a competitor to Rankolab last year and haven\'t looked back. The depth of data and reporting capabilities have helped us improve our search rankings by 43%.',
    author: {
      name: 'Michael Chen',
      title: 'Marketing Manager',
      company: 'TechStart Inc.',
      avatar: 'https://images.pexels.com/photos/220453/pexels-photo-220453.jpeg?auto=compress&cs=tinysrgb&w=150',
    },
  },
  {
    id: '3',
    content: 'The ability to track rankings across multiple locations has been game-changing for our international marketing strategy. Rankolab provides insights we couldn\'t get anywhere else.',
    author: {
      name: 'Emma Rodriguez',
      title: 'Head of Growth',
      company: 'Global Commerce',
      avatar: 'https://images.pexels.com/photos/774909/pexels-photo-774909.jpeg?auto=compress&cs=tinysrgb&w=150',
    },
  },
];

// Blog posts data
export const blogPosts: BlogPost[] = [
  {
    id: '1',
    title: 'How to Optimize Your Content for Featured Snippets',
    slug: 'optimize-content-featured-snippets',
    excerpt: 'Learn the strategies and techniques to increase your chances of winning featured snippets in Google search results.',
    content: 'Full blog post content here...',
    coverImage: 'https://images.pexels.com/photos/265087/pexels-photo-265087.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2',
    category: 'SEO Tips',
    author: {
      name: 'Alex Turner',
      avatar: 'https://images.pexels.com/photos/614810/pexels-photo-614810.jpeg?auto=compress&cs=tinysrgb&w=150',
    },
    publishedAt: '2023-04-15T10:30:00Z',
    readTime: 8,
  },
  {
    id: '2',
    title: 'Core Web Vitals: The Complete Guide for SEO Professionals',
    slug: 'core-web-vitals-guide-seo',
    excerpt: 'Everything you need to know about Core Web Vitals and how they impact your search engine rankings.',
    content: 'Full blog post content here...',
    coverImage: 'https://images.pexels.com/photos/196645/pexels-photo-196645.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2',
    category: 'Technical SEO',
    author: {
      name: 'Jessica Lee',
      avatar: 'https://images.pexels.com/photos/415829/pexels-photo-415829.jpeg?auto=compress&cs=tinysrgb&w=150',
    },
    publishedAt: '2023-03-28T09:15:00Z',
    readTime: 12,
  },
  {
    id: '3',
    title: 'Local SEO in 2025: New Strategies for Small Businesses',
    slug: 'local-seo-2025-strategies',
    excerpt: 'Discover the latest trends and tactics to improve your local search visibility in an increasingly competitive landscape.',
    content: 'Full blog post content here...',
    coverImage: 'https://images.pexels.com/photos/7412076/pexels-photo-7412076.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2',
    category: 'Local SEO',
    author: {
      name: 'David Wilson',
      avatar: 'https://images.pexels.com/photos/846741/pexels-photo-846741.jpeg?auto=compress&cs=tinysrgb&w=150',
    },
    publishedAt: '2023-02-12T14:45:00Z',
    readTime: 10,
  },
];

// FAQ data
export const faqItems: FAQItem[] = [
  {
    question: 'How accurate is Rankolab\'s rank tracking?',
    answer: 'Rankolab uses advanced algorithms and multiple data sources to provide highly accurate rank tracking. Our system updates daily and can simulate searches from different devices, locations, and personalization settings to give you the most precise view of your rankings.',
  },
  {
    question: 'Can I track my competitors\' rankings?',
    answer: 'Yes, Rankolab allows you to track and compare rankings for your competitors. You can add multiple competitors to monitor and receive alerts when their rankings change significantly, giving you valuable insights into your competitive landscape.',
  },
  {
    question: 'Do you offer white-label reporting?',
    answer: 'Yes, our Professional and Enterprise plans include white-label reporting features. You can customize reports with your own logo, colors, and branding to share professional analytics with your clients or stakeholders.',
  },
  {
    question: 'Is there a limit to how many keywords I can track?',
    answer: 'Each pricing plan comes with a different keyword tracking limit. Our Free plan includes 100 keywords, Basic offers 500, Professional provides 2,000, and Enterprise gives unlimited keyword tracking. You can upgrade your plan at any time as your needs grow.',
  },
  {
    question: 'How does the affiliate program work?',
    answer: 'Our affiliate program lets you earn commission for every new customer you refer to Rankolab. After signing up as an affiliate, you\'ll receive a unique tracking link to share. You earn 25% recurring commission on the monthly subscription fee for the lifetime of the customer\'s account.',
  },
  {
    question: 'Can I cancel my subscription at any time?',
    answer: 'Yes, you can cancel your subscription at any time through your account dashboard. There are no long-term contracts or cancellation fees. If you cancel, you\'ll continue to have access to your plan until the end of your current billing cycle.',
  },
];