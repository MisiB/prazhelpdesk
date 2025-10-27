# WorkOS Authentication - Quick Start

Get WorkOS SSO authentication working in 5 minutes!

## Prerequisites

- WorkOS account (sign up at [workos.com](https://workos.com))
- Laravel application running
- WorkOS PHP SDK installed (already done ✅)

## Step 1: Get WorkOS Credentials (2 minutes)

1. Go to [https://dashboard.workos.com](https://dashboard.workos.com)
2. Create a new project or select existing one
3. Copy your **API Key** and **Client ID**

## Step 2: Configure Environment (1 minute)

Add to your `.env` file:

```env
WORKOS_API_KEY=sk_test_your_key_here
WORKOS_CLIENT_ID=client_your_id_here
WORKOS_REDIRECT_URI=http://localhost:8000/auth/workos/callback
WORKOS_PROVIDER=GoogleOAuth
```

## Step 3: Register Redirect URI (1 minute)

1. In WorkOS dashboard → **Configuration** → **Redirects**
2. Add: `http://localhost:8000/auth/workos/callback`
3. Save

## Step 4: Run Migration (30 seconds)

```bash
php artisan migrate
```

## Step 5: Test It! (30 seconds)

1. Start server: `php artisan serve`
2. Visit: `http://localhost:8000/login`
3. Click **"Sign in with SSO"** or **"Sign in with Google"**
4. Authenticate
5. You're logged in! 🎉

## What You Get

✅ **Single Sign-On (SSO)** - Enterprise authentication
✅ **OAuth Login** - Google, Microsoft, GitHub
✅ **Auto User Creation** - No manual setup needed
✅ **Secure Authentication** - Enterprise-grade security
✅ **Directory Sync** - Optional automatic user provisioning
✅ **Modern Login UI** - Beautiful login page included

## Available Login Options

### SSO (Enterprise)

```html
<a href="{{ route('auth.workos.redirect') }}">Sign in with SSO</a>
```

### Google OAuth

```html
<a href="{{ route('auth.workos.redirect', ['provider' => 'GoogleOAuth']) }}">
    Sign in with Google
</a>
```

### Microsoft OAuth

```html
<a href="{{ route('auth.workos.redirect', ['provider' => 'MicrosoftOAuth']) }}">
    Sign in with Microsoft
</a>
```

## Production Deployment

Update `.env` for production:

```env
WORKOS_API_KEY=sk_live_your_production_key
WORKOS_CLIENT_ID=client_your_production_id
WORKOS_REDIRECT_URI=https://yourdomain.com/auth/workos/callback
```

Register production redirect URI in WorkOS dashboard.

## Need More?

- **Full Documentation**: See `docs/WORKOS_SETUP.md`
- **Troubleshooting**: Check `storage/logs/laravel.log`
- **WorkOS Docs**: [workos.com/docs](https://workos.com/docs)

That's it! You now have enterprise-grade authentication! 🚀






