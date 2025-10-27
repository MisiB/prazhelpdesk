# AI Knowledge-Based Support Portal

A modern AI-powered support portal built with Laravel that integrates with PrazCRM Admin APIs. This application provides intelligent ticket management, a comprehensive knowledge base, and AI-driven suggestions for faster issue resolution.

## 🚀 Features

### Core Features
- **AI-Powered Knowledge Base Search** - Semantic search that finds relevant articles based on user queries
- **Intelligent Ticket Management** - Create, track, and manage support tickets with AI suggestions
- **Auto-Suggestions** - AI automatically suggests related articles when creating tickets
- **CRM Integration** - Seamlessly integrates with PrazCRM Admin APIs
- **Enterprise Authentication** - WorkOS-powered SSO and OAuth (Google, Microsoft, Azure AD, Okta)
- **Dashboard & Analytics** - Comprehensive dashboard with ticket statistics and trends
- **Modern UI** - Clean, responsive interface with excellent UX

### AI Capabilities
- **Semantic Search** - Find knowledge base articles by meaning, not just keywords
- **Auto-Categorization** - AI suggests appropriate categories for tickets
- **Priority Detection** - Automatically detects urgent keywords and suggests priority
- **Similar Ticket Matching** - Find similar resolved tickets for faster resolution
- **Sentiment Analysis** - Detect customer sentiment in ticket descriptions
- **Auto-Response Generation** - Generate helpful responses based on knowledge base

### Knowledge Base
- Categories and tags organization
- Featured articles
- Popular articles tracking
- Helpfulness voting system
- View counts and analytics
- Rich text content support

### Ticket System
- Ticket creation with AI suggestions
- Status tracking (Open, In Progress, Waiting Customer, Resolved, Closed)
- Priority levels (Low, Medium, High, Urgent)
- Comments and internal notes
- File attachments
- Assignment to agents
- Response time tracking
- Resolution time tracking

### CRM Integration
- Sync customers from PrazCRM Admin
- Sync tickets to/from CRM
- Customer support history
- Order information integration
- Automatic synchronization

### Authentication (WorkOS)
- Single Sign-On (SSO) - Azure AD, Google Workspace, Okta, OneLogin
- OAuth 2.0 - Google, Microsoft, GitHub
- Directory Sync - Automatic user provisioning
- Multi-organization support
- Enterprise-grade security
- Auto-create and update users

## 📋 Requirements

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- SQLite/MySQL/PostgreSQL
- Laravel 11.x

## 🛠️ Installation

1. **Clone the repository**
```bash
git clone <repository-url>
cd prazhelpdesk
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Set up environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure your .env file**
```env
# Database
DB_CONNECTION=sqlite

# PrazCRM Admin API
PRAZCRMADMIN_API_URL=https://your-crm-domain.com
PRAZCRMADMIN_API_KEY=your_api_key_here

# WorkOS Authentication (Optional - for SSO/OAuth)
WORKOS_API_KEY=sk_test_your_key_here
WORKOS_CLIENT_ID=client_your_id_here
WORKOS_REDIRECT_URI=http://localhost:8000/auth/workos/callback
WORKOS_PROVIDER=GoogleOAuth
```

5. **Run migrations**
```bash
php artisan migrate
```

6. **Seed the database (optional)**
```bash
php artisan db:seed
```

7. **Build frontend assets**
```bash
npm run build
```

8. **Start the development server**
```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## 🔧 Configuration

### PrazCRM Admin API Integration

Update your `.env` file with your PrazCRM Admin credentials:

```env
PRAZCRMADMIN_API_URL=https://prazcrmadmin.example.com
PRAZCRMADMIN_API_KEY=your_api_key_here
PRAZCRMADMIN_API_TIMEOUT=30
PRAZCRMADMIN_SYNC_ENABLED=true
PRAZCRMADMIN_AUTO_SYNC_TICKETS=true
```

### WorkOS Authentication (Optional)

Enable enterprise SSO and OAuth authentication:

```env
WORKOS_API_KEY=sk_test_your_api_key
WORKOS_CLIENT_ID=client_your_client_id
WORKOS_REDIRECT_URI=http://localhost:8000/auth/workos/callback
WORKOS_PROVIDER=GoogleOAuth
```

**Quick Start:** See `WORKOS_QUICKSTART.md` for 5-minute setup
**Full Guide:** See `docs/WORKOS_SETUP.md` for complete configuration

### AI Service (Optional)

For enhanced AI capabilities, you can integrate with external AI services:

```env
AI_SERVICE_PROVIDER=openai
OPENAI_API_KEY=your_openai_key
# or
AI_SERVICE_PROVIDER=anthropic
ANTHROPIC_API_KEY=your_anthropic_key
```

## 📚 API Documentation

### Knowledge Base Endpoints

**Public Endpoints (No authentication required)**

- `GET /api/knowledge-base` - List all articles
- `GET /api/knowledge-base/popular` - Get popular articles
- `GET /api/knowledge-base/featured` - Get featured articles
- `GET /api/knowledge-base/search?query={query}` - AI-powered search
- `GET /api/knowledge-base/{slug}` - Get article details
- `POST /api/knowledge-base/{id}/helpful` - Mark article as helpful
- `POST /api/knowledge-base/{id}/not-helpful` - Mark article as not helpful

### Ticket Endpoints

**Protected Endpoints (Require authentication)**

- `GET /api/tickets` - List tickets
- `POST /api/tickets` - Create ticket (with AI suggestions)
- `GET /api/tickets/{id}` - Get ticket details
- `PUT /api/tickets/{id}` - Update ticket
- `DELETE /api/tickets/{id}` - Delete ticket
- `POST /api/tickets/{id}/comments` - Add comment
- `POST /api/tickets/{id}/attachments` - Upload attachment
- `POST /api/tickets/{id}/assign` - Assign to agent

### Dashboard Endpoints

- `GET /api/dashboard/overview` - Get statistics overview
- `GET /api/dashboard/ticket-trends` - Get ticket trends
- `GET /api/dashboard/tickets-by-category` - Ticket distribution by category
- `GET /api/dashboard/tickets-by-priority` - Ticket distribution by priority
- `GET /api/dashboard/recent-tickets` - Recent tickets
- `GET /api/dashboard/top-articles` - Top performing articles
- `GET /api/dashboard/agent-performance` - Agent performance metrics
- `GET /api/dashboard/crm-status` - CRM integration status

## 🏗️ Architecture

### Models
- `User` - User authentication and management
- `Ticket` - Support tickets
- `TicketComment` - Ticket comments and notes
- `TicketAttachment` - File attachments
- `KnowledgeBase` - Knowledge base articles
- `Category` - Categories for organization
- `Tag` - Tags for articles

### Services
- `CrmApiService` - Integration with PrazCRM Admin APIs
- `AiService` - AI-powered features (search, suggestions, sentiment analysis)

### Controllers
- `TicketController` - Ticket management
- `KnowledgeBaseController` - Knowledge base operations
- `DashboardController` - Dashboard and analytics

## 🎨 Frontend

The frontend is built with:
- Laravel Blade templates
- Vanilla JavaScript (no heavy frameworks)
- Modern CSS with custom styling
- Responsive design
- Real-time search
- AJAX-powered interactions

## 🔒 Security

- CSRF protection on all forms
- SQL injection protection via Eloquent ORM
- XSS protection
- File upload validation
- API authentication via Laravel Sanctum
- Environment-based configuration

## 🚀 Deployment

### Production Checklist

1. Set `APP_ENV=production` and `APP_DEBUG=false`
2. Generate a secure `APP_KEY`
3. Configure your database
4. Set up proper caching:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
5. Run migrations in production:
```bash
php artisan migrate --force
```
6. Build assets for production:
```bash
npm run build
```
7. Set up queue workers for background jobs
8. Configure proper file storage

## 📊 Database Schema

### Main Tables
- `users` - User accounts
- `categories` - Hierarchical categories
- `tags` - Article tags
- `knowledge_base` - Knowledge base articles
- `knowledge_base_tag` - Article-tag relationships
- `tickets` - Support tickets
- `ticket_comments` - Ticket comments
- `ticket_attachments` - File attachments

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📝 License

This project is open-sourced software licensed under the MIT license.

## 🙏 Acknowledgments

- Built with [Laravel](https://laravel.com)
- Icons from emoji
- Modern UI inspired by best practices in UX design

## 📞 Support

For support, please create a ticket through the portal or contact the development team.

## 🔄 Version History

### Version 1.0.0 (Current)
- Initial release
- AI-powered knowledge base search
- Ticket management system
- CRM integration
- Dashboard and analytics
- Modern responsive UI

---

Made with ❤️ for better customer support
