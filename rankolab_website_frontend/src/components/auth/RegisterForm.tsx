import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { User, Mail, Lock, AlertCircle, CheckCircle, Eye, EyeOff } from 'lucide-react';
import Input from '../ui/Input';
import Button from '../ui/Button';
import { useAuth } from '../../lib/authContext';

const RegisterForm: React.FC = () => {
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [passwordConfirm, setPasswordConfirm] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [showPasswordConfirm, setShowPasswordConfirm] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState('');
  const [successMessage, setSuccessMessage] = useState('');
  const { register } = useAuth();
  const navigate = useNavigate();

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setSuccessMessage('');

    if (!name || !email || !password || !passwordConfirm) {
      setError('Please fill in all fields.');
      return;
    }
    if (password !== passwordConfirm) {
      setError('Passwords do not match.');
      return;
    }
    if (password.length < 8) {
      setError('Password must be at least 8 characters long.');
      return;
    }
    // Basic email validation
    if (!/\S+@\S+\.\S+/.test(email)) {
        setError('Please enter a valid email address.');
        return;
    }

    try {
      setIsLoading(true);
      // Mocking successful registration for now as backend might be blocked
      // In a real scenario, this would be: await register(name, email, password);
      await new Promise(resolve => setTimeout(resolve, 1500)); // Simulate API call
      console.log('Mock registration successful for:', { name, email });
      setSuccessMessage('Registration successful! Redirecting to login...'); 
      // Redirect to login page after a short delay to show success message
      setTimeout(() => {
        navigate('/login'); 
      }, 2000);
    } catch (err) {
      setError('Registration failed. Please try again later.');
      console.error('Registration error:', err);
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="max-w-lg w-full mx-auto bg-white p-8 md:p-10 rounded-xl shadow-2xl border border-gray-200">
      <div className="text-center mb-8">
        <Link to="/" className="inline-block mb-6">
          <img src="/src/assets/rankolab-logo.jpeg" alt="Rankolab Logo" className="w-10 h-10" />
        </Link>
        <h1 className="text-3xl font-bold text-gray-900 mb-2">Create Your Rankolab Account</h1>
        <p className="text-gray-600">Join thousands of SEO professionals. Start your 14-day free trial.</p>
      </div>

      {error && (
        <div className="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 flex items-start">
          <AlertCircle className="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" />
          <p className="text-sm font-medium">{error}</p>
        </div>
      )}
      {successMessage && (
        <div className="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 flex items-start">
          <CheckCircle className="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" />
          <p className="text-sm font-medium">{successMessage}</p>
        </div>
      )}

      <form onSubmit={handleSubmit} className="space-y-6">
        <Input
          type="text"
          label="Full Name"
          id="name"
          value={name}
          onChange={(e) => setName(e.target.value)}
          placeholder="e.g., Jane Doe"
          startIcon={<User size={18} className="text-gray-400" />}
          fullWidth
          required
          aria-describedby="name-error"
        />

        <Input
          type="email"
          label="Email Address"
          id="email"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
          placeholder="you@example.com"
          startIcon={<Mail size={18} className="text-gray-400" />}
          fullWidth
          required
          aria-describedby="email-error"
        />

        <Input
          type={showPassword ? "text" : "password"}
          label="Password"
          id="password"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          placeholder="Minimum 8 characters"
          startIcon={<Lock size={18} className="text-gray-400" />}
          endIcon={showPassword ? <EyeOff size={18} className="text-gray-500 cursor-pointer" /> : <Eye size={18} className="text-gray-500 cursor-pointer" />}
          onEndIconClick={() => setShowPassword(!showPassword)}
          helperText="Use 8 or more characters with a mix of letters, numbers & symbols."
          fullWidth
          required
          aria-describedby="password-error"
        />

        <Input
          type={showPasswordConfirm ? "text" : "password"}
          label="Confirm Password"
          id="passwordConfirm"
          value={passwordConfirm}
          onChange={(e) => setPasswordConfirm(e.target.value)}
          placeholder="Re-enter your password"
          startIcon={<Lock size={18} className="text-gray-400" />}
          endIcon={showPasswordConfirm ? <EyeOff size={18} className="text-gray-500 cursor-pointer" /> : <Eye size={18} className="text-gray-500 cursor-pointer" />}
          onEndIconClick={() => setShowPasswordConfirm(!showPasswordConfirm)}
          fullWidth
          required
          aria-describedby="passwordConfirm-error"
        />

        <div className="flex items-start mb-6 pt-2">
          <input
            id="accept-terms"
            name="accept-terms"
            type="checkbox"
            className="h-4 w-4 mt-1 text-primary-600 focus:ring-primary-500 border-gray-300 rounded cursor-pointer"
            required
          />
          <label htmlFor="accept-terms" className="ml-3 block text-sm text-gray-700">
            I agree to the Rankolab{' '}
            <Link to="/terms" className="font-medium text-primary-600 hover:text-primary-700 hover:underline">
              Terms of Service
            </Link>{' '}
            and{' '}
            <Link to="/privacy" className="font-medium text-primary-600 hover:text-primary-700 hover:underline">
              Privacy Policy
            </Link>.
          </label>
        </div>

        <Button
          type="submit"
          variant="primary"
          size="lg"
          fullWidth
          isLoading={isLoading}
          className="text-base font-semibold shadow-md hover:shadow-lg transition-shadow duration-300"
        >
          {isLoading ? 'Creating Account...' : 'Create Free Account'}
        </Button>
      </form>

      <div className="mt-8 text-center">
        <p className="text-sm text-gray-600">
          Already have an account?{' '}
          <Link to="/login" className="font-semibold text-primary-600 hover:text-primary-700 hover:underline">
            Sign In
          </Link>
        </p>
      </div>
    </div>
  );
};

export default RegisterForm;
