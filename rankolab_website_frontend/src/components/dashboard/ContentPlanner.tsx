
import React, { useState } from 'react';
import Container from '../ui/Container';
import Button from '../ui/Button';

interface ContentPlan {
  topic: string;
  keywords: string[];
  contentType: 'blog' | 'article' | 'guide';
  targetWordCount: number;
  scheduledDate: string;
}

export default function ContentPlanner() {
  const [plans, setPlans] = useState<ContentPlan[]>([]);
  const [newPlan, setNewPlan] = useState<ContentPlan>({
    topic: '',
    keywords: [],
    contentType: 'blog',
    targetWordCount: 1000,
    scheduledDate: ''
  });

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    // TODO: Integrate with backend API
    setPlans([...plans, newPlan]);
    setNewPlan({
      topic: '',
      keywords: [],
      contentType: 'blog',
      targetWordCount: 1000,
      scheduledDate: ''
    });
  };

  return (
    <Container>
      <div className="py-8">
        <h2 className="text-2xl font-bold mb-6">Content Planning</h2>
        
        <form onSubmit={handleSubmit} className="space-y-4 mb-8">
          <div>
            <label className="block text-sm font-medium mb-1">Topic</label>
            <input
              type="text"
              value={newPlan.topic}
              onChange={(e) => setNewPlan({...newPlan, topic: e.target.value})}
              className="w-full px-3 py-2 border rounded-md"
              required
            />
          </div>
          
          <div>
            <label className="block text-sm font-medium mb-1">Keywords (comma-separated)</label>
            <input
              type="text"
              value={newPlan.keywords.join(', ')}
              onChange={(e) => setNewPlan({...newPlan, keywords: e.target.value.split(',').map(k => k.trim())})}
              className="w-full px-3 py-2 border rounded-md"
            />
          </div>

          <div>
            <label className="block text-sm font-medium mb-1">Content Type</label>
            <select
              value={newPlan.contentType}
              onChange={(e) => setNewPlan({...newPlan, contentType: e.target.value as 'blog' | 'article' | 'guide'})}
              className="w-full px-3 py-2 border rounded-md"
            >
              <option value="blog">Blog Post</option>
              <option value="article">Article</option>
              <option value="guide">Guide</option>
            </select>
          </div>

          <div>
            <label className="block text-sm font-medium mb-1">Target Word Count</label>
            <input
              type="number"
              value={newPlan.targetWordCount}
              onChange={(e) => setNewPlan({...newPlan, targetWordCount: parseInt(e.target.value)})}
              className="w-full px-3 py-2 border rounded-md"
              min="500"
              max="5000"
            />
          </div>

          <div>
            <label className="block text-sm font-medium mb-1">Scheduled Date</label>
            <input
              type="date"
              value={newPlan.scheduledDate}
              onChange={(e) => setNewPlan({...newPlan, scheduledDate: e.target.value})}
              className="w-full px-3 py-2 border rounded-md"
              required
            />
          </div>

          <Button type="submit">Add to Content Plan</Button>
        </form>

        <div className="space-y-4">
          <h3 className="text-xl font-semibold">Planned Content</h3>
          {plans.map((plan, index) => (
            <div key={index} className="p-4 border rounded-lg">
              <h4 className="font-medium">{plan.topic}</h4>
              <p className="text-sm text-gray-600">
                {plan.contentType} • {plan.targetWordCount} words • {plan.scheduledDate}
              </p>
              <p className="text-sm mt-2">Keywords: {plan.keywords.join(', ')}</p>
            </div>
          ))}
        </div>
      </div>
    </Container>
  );
}
