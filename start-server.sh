#!/bin/bash

# Laravel Server Startup Script
echo "🚀 Starting Laravel Development Server..."
echo ""

# Check if composer dependencies are installed
if [ ! -d "vendor" ]; then
    echo "📦 Installing Composer dependencies..."
    composer install
fi

# Check if npm dependencies are installed
if [ ! -d "node_modules" ]; then
    echo "📦 Installing NPM dependencies..."
    npm install
fi

# Build assets
echo "🔨 Building assets..."
npm run build

# Generate app key if not exists
if [ ! -f ".env" ]; then
    echo "⚙️  Creating .env file..."
    cp .env.example .env
    php artisan key:generate
fi

# Create database if not exists
if [ ! -f "database/database.sqlite" ]; then
    echo "🗄️  Creating SQLite database..."
    touch database/database.sqlite
    php artisan migrate
fi

echo ""
echo "✅ Setup complete!"
echo ""
echo "🌐 Starting server at: http://localhost:8000"
echo "🏠 Homepage: http://localhost:8000"
echo "⚡ Admin Dashboard: http://localhost:8000/admin/dashboard"
echo "👥 User Management: http://localhost:8000/admin/users"
echo ""
echo "Press Ctrl+C to stop the server"
echo ""

# Start Laravel development server
php artisan serve
