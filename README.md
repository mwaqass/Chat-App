# Laravel Reverb Conversation Application

A real-time conversation application built with Laravel 11, Vue.js, and Laravel Reverb for instant messaging capabilities.

## 📋 Table of Contents

- [Features](#-features)
- [Prerequisites](#-prerequisites)
- [Installation & Setup](#️-installation--setup)
- [Running the Application](#-running-the-application)
- [Testing Real-Time Conversation](#-testing-real-time-conversation)
- [Project Structure](#-project-structure)
- [Key Components](#-key-components)
- [Troubleshooting](#-troubleshooting)
- [Contributing](#-contributing)
- [License](#-license)

## 🚀 Features

- **Real-time Messaging**: Instant message delivery using Laravel Reverb
- **User Authentication**: Built-in Laravel Breeze authentication
- **Modern UI**: Beautiful interface with Tailwind CSS
- **Vue.js Components**: Interactive conversation interface
- **Private Channels**: Secure private messaging between users
- **Typing Indicators**: Shows when users are typing
- **Responsive Design**: Works on desktop and mobile devices
- **Conversation Management**: Organized conversation system with partners

## 📋 Prerequisites

Before you begin, ensure you have the following installed:
- **PHP 8.2+** (Required for Laravel 11)
- **Composer** (PHP package manager)
- **Node.js & NPM** (For frontend assets)
- **MySQL/PostgreSQL** (Database)
- **Git** (Version control)

## 🛠️ Installation & Setup

### 1. Clone the Repository
```bash
git clone https://github.com/mwaqass/Chat-App.git
cd Chat-App
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install Node.js Dependencies
```bash
npm install
```

### 4. Environment Configuration
Copy the environment file and configure your settings:
```bash
cp .env.example .env
```

**Important Environment Variables:**
```env
APP_NAME="Laravel Reverb Conversation"
APP_ENV=local
APP_KEY=base64:your-generated-key
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_reverb_conversation
DB_USERNAME=root
DB_PASSWORD=

# Broadcasting Configuration (CRITICAL for real-time conversation)
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=229560
REVERB_APP_KEY=any-random-app-key
REVERB_APP_SECRET=any-random-app-secret
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

# Frontend Environment Variables
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### 5. Generate Application Key
```bash
php artisan key:generate
```

### 6. Database Setup
```bash
# Run migrations to create database tables
php artisan migrate

# Seed the database with test users and sample conversations
php artisan db:seed
```

### 7. Build Frontend Assets
```bash
# Build theme assets
npm run build:tailwind
```

## 🚀 Running the Application

### Start All Required Servers

You need to run **three servers simultaneously** for the application to work properly:

#### 1. Laravel Development Server
```bash
php artisan serve
```
- **URL**: http://127.0.0.1:8000
- **Purpose**: Main PHP application server

#### 2. Theme Vite Development Server
```bash
npm run dev:tailwind
```
- **URL**: http://localhost:5173 (or similar)
- **Purpose**: Compiles and serves frontend assets (CSS, JS, Vue components)

#### 3. Reverb Server (CRITICAL for real-time conversation)
```bash
php artisan reverb:start
```
- **URL**: localhost:8080
- **Purpose**: WebSocket server for real-time messaging

### Access the Application
1. Visit: http://127.0.0.1:8000
2. Register a new account or use test accounts:
   - `test@example.com` / `12345678`
   - `john@example.com` / `12345678`
   - `jane@example.com` / `12345678`
   - `mike@example.com` / `12345678`
   - `sarah@example.com` / `12345678`

## 🧪 Testing Real-Time Conversation

1. **Open two browser windows/tabs**
2. **Login with different users** (e.g., `test@example.com` and `john@example.com`)
3. **Navigate to the dashboard** and click on a conversation partner
4. **Send messages** - they should appear instantly in both browsers without refreshing!

## 📁 Project Structure

```
laravel-reverb-conversation-main/
├── app/
│   ├── Events/
│   │   └── ConversationMessageSent.php     # Real-time message broadcasting
│   ├── Http/Controllers/
│   │   ├── ConversationController.php      # Conversation management
│   │   └── DashboardController.php         # Dashboard functionality
│   ├── Models/
│   │   ├── ConversationMessage.php         # Message model
│   │   └── User.php                        # User model
│   └── Providers/
│       └── BroadcastServiceProvider.php    # Broadcasting configuration
├── themes/tailwind/
│   ├── js/
│   │   ├── components/
│   │   │   └── ConversationInterface.vue   # Vue.js conversation component
│   │   ├── app.js                          # Main JavaScript file
│   │   ├── bootstrap.js                    # Bootstrap configuration
│   │   └── echo.js                         # Laravel Echo configuration
│   ├── views/
│   │   ├── dashboard.blade.php             # Dashboard view
│   │   └── conversation.blade.php          # Conversation view
│   └── vite.config.js                      # Vite configuration for theme
├── routes/
│   ├── web.php                             # Main routes
│   └── channels.php                        # Broadcasting channels
└── database/
    ├── migrations/                         # Database migrations
    └── seeders/                            # Database seeders
```

## 🔧 Key Components

### Real-Time Conversation Flow
1. **User sends message** → POST `/conversation/{partner}/send`
2. **Message saved** to database
3. **ConversationMessageSent event** broadcasted to `conversation.{recipient_id}` channel
4. **Vue component** listens on `conversation.{currentUser.id}` channel
5. **Message appears** instantly in recipient's browser

### Broadcasting Configuration
- **Connection**: Laravel Reverb (WebSocket server)
- **Channels**: Private channels for security
- **Events**: ConversationMessageSent event for real-time updates
- **Authentication**: Channel authorization via `routes/channels.php`

### Vue.js Integration
- **Component**: ConversationInterface.vue handles real-time messaging
- **Echo**: Laravel Echo configured for Reverb
- **Channels**: Private channel listening for incoming messages
- **Whispers**: Typing indicators via Echo whispers

## 🐛 Troubleshooting

### Common Issues

#### 1. Messages not appearing in real-time
- **Check**: Reverb server is running (`php artisan reverb:start`)
- **Check**: Broadcasting connection is set to `reverb` in `.env`
- **Check**: Browser console for Echo connection errors

#### 2. CSS/JS not loading
- **Check**: Theme Vite server is running (`npm run dev:tailwind`)
- **Check**: Assets are built (`npm run build:tailwind`)

#### 3. Database connection issues
- **Check**: Database credentials in `.env`
- **Check**: Database exists and migrations are run

#### 4. Authentication issues
- **Check**: Application key is generated (`php artisan key:generate`)
- **Check**: Session configuration in `.env`

### Debug Mode
Enable debug mode in `.env`:
```env
APP_DEBUG=true
```

Check browser console and Laravel logs for detailed error information.

## 📝 Important Notes

1. **All three servers must be running** for the application to work properly
2. **Broadcasting configuration is critical** for real-time functionality
3. **Channel authorization** ensures secure private messaging
4. **Vue.js components** handle the interactive conversation interface
5. **Laravel Reverb** provides WebSocket functionality for real-time updates

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

---

**Happy Conversing! 🎉**
 
