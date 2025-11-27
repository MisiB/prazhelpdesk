# Installation Guide - AI Support Portal

This guide will walk you through the complete installation and setup process.

## Prerequisites

Before you begin, ensure you have the following installed:

- **PHP 8.2+** with the following extensions:
  - BCMath
  - Ctype
  - Fileinfo
  - JSON
  - Mbstring
  - OpenSSL
  - PDO
  - Tokenizer
  - XML

- **Composer** (latest version)
- **Node.js 18+** and **npm** (or yarn)
- **Database**: SQLite (default), MySQL 8.0+, or PostgreSQL 13+
- **Git** (for cloning the repository)

## Step-by-Step Installation

### 1. Clone the Repository

```bash
git clone <repository-url> prazhelpdesk
cd prazhelpdesk
```

### 2. Install PHP Dependencies

```bash
composer install
```

If you encounter memory issues, try:
```bash
composer install --no-dev
```

### 3. Install JavaScript Dependencies

```bash
npm install
```

### 4. Environment Configuration

Copy the example environment file:
```bash
cp .env.example .env
```

Generate an application key:
```bash
php artisan key:generate
```

### 5. Database Setup

#### Option A: Using SQLite (Recommended for Development)

SQLite is configured by default. The database file is located at `database/database.sqlite`.

No additional configuration needed!

#### Option B: Using MySQL

Update your `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=prazhelpdesk
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Create the database:
```sql
CREATE DATABASE prazhelpdesk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### Option C: Using PostgreSQL

Update your `.env` file:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=prazhelpdesk
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 6. Run Migrations

```bash
php artisan migrate
```

### 7. Seed Sample Data (Optional but Recommended)

This will create:
- Sample categories
- Sample tags
- Sample knowledge base articles
- An admin user (email: admin@example.com, password: password)

```bash
php artisan db:seed
```

### 8. Configure PrazCRM Admin API

Update your `.env` file with your PrazCRM Admin API credentials:

```env
PRAZCRMADMIN_API_URL=https://your-crm-domain.com
PRAZCRMADMIN_API_KEY=your_api_key_here
PRAZCRMADMIN_API_TIMEOUT=30
```

If you don't have API credentials yet, you can leave these as default and the portal will work without CRM integration.

### 9. Storage Configuration

Create symbolic link for public storage:
```bash
php artisan storage:link
```

Set proper permissions:
```bash
chmod -R 775 storage bootstrap/cache
```

On Linux/Mac:
```bash
chown -R www-data:www-data storage bootstrap/cache
```

### 10. Build Frontend Assets

For development:
```bash
npm run dev
```

For production:
```bash
npm run build
```

### 11. Start the Application

#### Development Server

```bash
php artisan serve
```

The application will be available at: `http://localhost:8000`

#### Production Server

For production, configure your web server (Apache/Nginx) to point to the `public` directory.

**Nginx Configuration Example:**
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/prazhelpdesk/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## Post-Installation Configuration

### 1. Create Your First Admin User

If you didn't run the seeder, create an admin user manually:

```bash
php artisan tinker
```

Then run:
```php
User::create([
    'name' => 'Your Name',
    'email' => 'admin@yourdomain.com',
    'password' => bcrypt('your-secure-password')
]);
```

### 2. Configure Email (Optional)

Update `.env` with your email settings:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 3. Configure Queue Worker (Recommended for Production)

Set up a queue worker for background jobs:

```env
QUEUE_CONNECTION=database
```

Run the queue worker:
```bash
php artisan queue:work
```

For production, set up a supervisor configuration:
```ini
[program:prazhelpdesk-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/prazhelpdesk/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/prazhelpdesk/storage/logs/worker.log
```

### 4. Set Up Scheduled Tasks

Add to your crontab:
```bash
* * * * * cd /path/to/prazhelpdesk && php artisan schedule:run >> /dev/null 2>&1
```

### 5. Optimize for Production

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

## Testing the Installation

### 1. Access the Application

Open your browser and navigate to:
- Homepage: `http://localhost:8000`
- Knowledge Base: `http://localhost:8000/knowledge-base`
- Dashboard: `http://localhost:8000/dashboard`

### 2. Test API Endpoints

Test the knowledge base search:
```bash
curl http://localhost:8000/api/knowledge-base/search?query=getting+started
```

Test popular articles:
```bash
curl http://localhost:8000/api/knowledge-base/popular
```

### 3. Create a Test Ticket

1. Navigate to `http://localhost:8000/tickets/create`
2. Fill out the form
3. Submit and observe AI suggestions

## Troubleshooting

### Issue: "500 Internal Server Error"

**Solution:**
1. Check file permissions
2. Enable debug mode: Set `APP_DEBUG=true` in `.env`
3. Check error logs: `storage/logs/laravel.log`

### Issue: Database Connection Failed

**Solution:**
1. Verify database credentials in `.env`
2. Ensure database exists
3. Check database service is running

### Issue: Assets Not Loading

**Solution:**
```bash
npm run build
php artisan storage:link
```

### Issue: Permission Denied

**Solution:**
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Issue: Composer Install Fails

**Solution:**
```bash
composer clear-cache
composer install --no-scripts
composer install
```

## Security Checklist

Before going to production:

- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate a strong `APP_KEY`
- [ ] Use HTTPS (SSL certificate)
- [ ] Set up firewall rules
- [ ] Configure CORS properly
- [ ] Set up regular backups
- [ ] Enable rate limiting
- [ ] Review and secure API keys
- [ ] Set up monitoring and alerts

## Updating the Application

To update to a newer version:

```bash
# Backup database first!

# Pull latest changes
git pull origin main

# Update dependencies
composer install
npm install

# Run migrations
php artisan migrate

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild assets
npm run build

# Re-cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Getting Help

If you encounter any issues:

1. Check the documentation in `README.md`
2. Review error logs in `storage/logs/laravel.log`
3. Search for similar issues in the project repository
4. Create a support ticket through the portal
5. Contact the development team

## Next Steps

After installation:

1. **Customize Categories**: Create categories that match your support structure
2. **Add Knowledge Base Articles**: Populate your knowledge base with helpful content
3. **Configure CRM Integration**: Set up the PrazCRM Admin API integration
4. **Customize Branding**: Update colors, logos, and text to match your brand
5. **Train Your Team**: Familiarize your support team with the portal
6. **Set Up Monitoring**: Configure monitoring and alerting for production

Congratulations! Your AI Support Portal is now ready to use. 🎉












