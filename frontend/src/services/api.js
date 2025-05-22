import axios from 'axios';

// Create axios instance with base URL
const api = axios.create({
  baseURL: '/api', // This will use the proxy defined in vite.config.js
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Add a request interceptor for authentication
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token');
    if (token) {
      config.headers['Authorization'] = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

export const poemService = {
  // Get all poems
  getPoems: async () => {
    try {
      const response = await api.get('/poems');
      return response.data;
    } catch (error) {
      console.error('Error fetching poems:', error);
      throw error;
    }
  },

  // Get a specific poem
  getPoem: async (id) => {
    try {
      const response = await api.get(`/poems/${id}`);
      return response.data;
    } catch (error) {
      console.error(`Error fetching poem ${id}:`, error);
      throw error;
    }
  },
  
  // Generate a new poem using AI
  generatePoem: async (theme, type = 'classical') => {
    try {
      const response = await api.post('/generate-poem', { theme, type });
      return response.data;
    } catch (error) {
      console.error('Error generating poem:', error);
      throw error;
    }
  },

  // Create a new poem
  createPoem: async (poemData) => {
    try {
      const response = await api.post('/poems', poemData);
      return response.data;
    } catch (error) {
      console.error('Error creating poem:', error);
      throw error;
    }
  },

  // Update an existing poem
  updatePoem: async (id, poemData) => {
    try {
      const response = await api.put(`/poems/${id}`, poemData);
      return response.data;
    } catch (error) {
      console.error(`Error updating poem ${id}:`, error);
      throw error;
    }
  },

  // Delete a poem
  deletePoem: async (id) => {
    try {
      await api.delete(`/poems/${id}`);
      return true;
    } catch (error) {
      console.error(`Error deleting poem ${id}:`, error);
      throw error;
    }
  },

  // Generate a poem using AI
  generatePoem: async (generationData) => {
    try {
      const response = await api.post('/generate-poem', generationData);
      return response.data;
    } catch (error) {
      console.error('Error generating poem:', error);
      throw error;
    }
  }
};

export default api;
