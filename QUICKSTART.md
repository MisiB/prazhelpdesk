# Quick Start Guide

Get your AI Support Portal up and running in 5 minutes!

## Prerequisites

- PHP 8.2+
- Composer
- Node.js & npm

## Installation (5 Steps)

### 1. Install Dependencies

```bash
composer install
npm install
```

### 2. Set Up Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Run Migrations & Seed Data

```bash
php artisan migrate
php artisan db:seed
```

This creates sample data including:
- Categories (Getting Started, Technical Support, etc.)
- Knowledge base articles
- Admin user: `admin@example.com` / `password`

### 4. Build Frontend

```bash
npm run build
```

### 5. Start the Server

```bash
php artisan serve
```

## Access Your Portal

Open your browser and visit:

- **Homepage**: http://localhost:8000
- **Knowledge Base**: http://localhost:8000/knowledge-base
- **Create Ticket**: http://localhost:8000/tickets/create
- **Dashboard**: http://localhost:8000/dashboard

## Test the AI Features

### 1. AI-Powered Search

Go to the homepage and search for:
- "how to get started"
- "api authentication"
- "billing"

The AI will find relevant articles based on semantic meaning!

### 2. Create a Ticket with AI Suggestions

1. Go to "Create Ticket"
2. Start typing a subject like "I can't login to my account"
3. Watch as AI suggests related knowledge base articles
4. Submit the ticket to see AI-generated suggestions

### 3. Browse Knowledge Base

- Visit `/knowledge-base` to see all articles
- Click on categories to filter
- Use the search to find specific topics
- Mark articles as helpful/not helpful

## Configure PrazCRM Admin Integration (Optional)

Edit your `.env` file:

```env
PRAZCRMADMIN_API_URL=https://your-crm-domain.com
PRAZCRMADMIN_API_KEY=your_api_key_here
```

The portal works perfectly without CRM integration - this is just for syncing customer data and tickets.

## Key Features to Try

### Knowledge Base
- ✅ AI-powered semantic search
- ✅ Categories and tags
- ✅ Featured articles
- ✅ Popular articles tracking
- ✅ Helpfulness voting

### Tickets
- ✅ Create tickets with AI suggestions
- ✅ Auto-categorization
- ✅ Priority detection
- ✅ Status tracking
- ✅ Comments and attachments

### Dashboard
- ✅ Ticket statistics
- ✅ Response time metrics
- ✅ Popular articles
- ✅ Agent performance

## Default Admin Credentials

```
Email: admin@example.com
Password: password
```

**⚠️ Change this password immediately for production!**

## Production Deployment

When ready for production:

1. Update `.env`:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```

2. Optimize:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. Use a proper web server (Nginx/Apache)

See `INSTALLATION.md` for detailed production setup.

## Customization Quick Tips

### Change Brand Colors

Edit `resources/views/layouts/app.blade.php`:
```css
/* Find and replace */
#3b82f6  /* Primary blue - change to your brand color */
```

### Add Your Logo

Replace the emoji in the navbar:
```html
<a href="/" class="nav-brand">🤖 AI Support Portal</a>
```

### Customize Categories

Edit `database/seeders/SupportPortalSeeder.php` and run:
```bash
php artisan db:seed --class=SupportPortalSeeder
```

## Common Commands

```bash
# Create new migration
php artisan make:migration create_something_table

# Create new model
php artisan make:model ModelName

# Create new controller
php artisan make:controller ControllerName

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run tests
php artisan test

# View routes
php artisan route:list

# Open tinker (Laravel REPL)
php artisan tinker
```

## Need Help?

- 📖 Full documentation: `README.md`
- 🛠️ Installation guide: `INSTALLATION.md`
- 🔧 Check logs: `storage/logs/laravel.log`

## What's Next?

1. **Add Your Content**: Create knowledge base articles for your specific use case
2. **Customize Categories**: Adjust categories to match your support structure
3. **Set Up CRM**: Configure PrazCRM Admin API integration
4. **Train Your Team**: Show your support team how to use the portal
5. **Go Live**: Deploy to production and help your customers!

---

**Pro Tip**: Start by creating 10-15 good knowledge base articles. The AI search gets better with more content!

Happy supporting! 🚀












