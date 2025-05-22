# Kaatob - Laravel & React Project

This is a full-stack web application using Laravel (backend API) and React (frontend) for generating Arabic poems using AI.

## Project Structure
- `/backend` - Laravel backend
- `/frontend` - React frontend

## Setup Instructions
1. Configure backend (Laravel)
   ```
   cd backend
   composer install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate
   ```

2. Configure frontend (React)
   ```
   cd frontend
   npm install
   npm run dev
   ```

## Database Configuration
Edit the `.env` file in the backend directory to configure your database connection.

## OpenAI Integration
This application uses OpenAI's GPT models to generate Arabic poems. To set up the integration:

1. Get your API key from [OpenAI](https://platform.openai.com/api-keys)
2. Go to the Admin section of the application `/admin/settings/ai`
3. Enter your API key and configure the model settings
4. Save your settings

The application supports both Classical Arabic and Nabati poem generation.

## Features
- Generate Classical Arabic poems
- Generate Nabati poems
- Save generated poems to your collection
- Manage poem visibility (public/private)
- Admin dashboard for API configuration
