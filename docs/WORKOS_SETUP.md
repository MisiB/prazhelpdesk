# WorkOS Authentication Setup Guide

This guide will walk you through setting up WorkOS authentication for your support portal.

## What is WorkOS?

WorkOS provides enterprise-grade authentication features including:
- **Single Sign-On (SSO)** - Let users sign in through their organization's identity provider
- **Directory Sync** - Automatically sync users from Azure AD, Google Workspace, Okta, etc.
- **OAuth Support** - Google, Microsoft, GitHub, and more
- **Magic Links** - Passwordless authentication
- **Multi-factor Authentication (MFA)**

## Step 1: Create a WorkOS Account

1. Go to [https://workos.com](https://workos.com) and sign up
2. Create a new project for your support portal
3. Note your **API Key** and **Client ID** from the dashboard

## Step 2: Configure Environment Variables

Add these variables to your `.env` file:

```env
# WorkOS Configuration
WORKOS_API_KEY=sk_test_your_api_key_here
WORKOS_CLIENT_ID=client_your_client_id_here
WORKOS_REDIRECT_URI=http://localhost:8000/auth/workos/callback

# Optional: Default connection settings
WORKOS_CONNECTION=
WORKOS_ORGANIZATION=
WORKOS_PROVIDER=GoogleOAuth

# User provisioning
WORKOS_AUTO_CREATE_USERS=true
WORKOS_AUTO_UPDATE_USERS=true
WORKOS_SESSION_LIFETIME=1440

# Directory Sync (Optional)
WORKOS_DIRECTORY_SYNC_ENABLED=false
WORKOS_DIRECTORY_WEBHOOK_SECRET=
```

### Environment Variables Explained

| Variable | Description | Required |
|----------|-------------|----------|
| `WORKOS_API_KEY` | Your WorkOS API key | ✅ Yes |
| `WORKOS_CLIENT_ID` | Your WorkOS client ID | ✅ Yes |
| `WORKOS_REDIRECT_URI` | OAuth callback URL | ✅ Yes |
| `WORKOS_CONNECTION` | Default connection ID | ❌ No |
| `WORKOS_ORGANIZATION` | Default organization ID | ❌ No |
| `WORKOS_PROVIDER` | Default OAuth provider | ❌ No |
| `WORKOS_AUTO_CREATE_USERS` | Auto-create new users | ❌ No (default: true) |
| `WORKOS_AUTO_UPDATE_USERS` | Auto-update user info | ❌ No (default: true) |
| `WORKOS_SESSION_LIFETIME` | Session duration (minutes) | ❌ No (default: 1440) |

## Step 3: Register Redirect URI in WorkOS Dashboard

1. Go to your WorkOS dashboard
2. Navigate to **Configuration** > **Redirects**
3. Add your redirect URI:
   - Development: `http://localhost:8000/auth/workos/callback`
   - Production: `https://yourdomain.com/auth/workos/callback`

## Step 4: Run Database Migration

```bash
php artisan migrate
```

This adds the following fields to your `users` table:
- `workos_id` - WorkOS profile ID
- `workos_connection_id` - Connection ID used
- `workos_connection_type` - Type of connection (SSO, OAuth, etc.)
- `workos_organization_id` - Organization ID
- `workos_directory_user_id` - Directory sync user ID
- `workos_raw_attributes` - Raw profile data from WorkOS

## Step 5: Configure SSO (Single Sign-On)

### Option A: Using an Existing Identity Provider

If your organization uses Azure AD, Google Workspace, Okta, or similar:

1. In WorkOS dashboard, go to **SSO** > **Connections**
2. Click **+ New Connection**
3. Select your identity provider
4. Follow the setup instructions
5. Copy the **Connection ID**
6. Add to `.env`:
   ```env
   WORKOS_CONNECTION=conn_01XXXXXXXXXXXXX
   ```

### Option B: Using OAuth Providers

For Google, Microsoft, GitHub, etc.:

1. Set the provider in `.env`:
   ```env
   WORKOS_PROVIDER=GoogleOAuth
   ```
   
Available providers:
- `GoogleOAuth`
- `MicrosoftOAuth`
- `GitHubOAuth`

2. Configure OAuth credentials in WorkOS dashboard

## Step 6: Test the Integration

### Test SSO Login

1. Start your server:
   ```bash
   php artisan serve
   ```

2. Visit: `http://localhost:8000/login`

3. Click **"Sign in with SSO"**

4. You should be redirected to WorkOS, then to your identity provider

5. After successful authentication, you'll be redirected back and logged in

### Test with Laravel Tinker

```bash
php artisan tinker
```

```php
// Test WorkOS service
$workos = app(\App\Services\WorkOsService::class);

// Get authorization URL
$url = $workos->getAuthorizationUrl();
echo $url;

// List connections
$connections = $workos->listConnections();
dd($connections);
```

## Step 7: Enable Directory Sync (Optional)

Directory Sync automatically provisions and deprovisions users from your identity provider.

### Setup

1. In WorkOS dashboard, go to **Directory Sync**
2. Create a new directory
3. Copy the **Webhook Secret**
4. Add to `.env`:
   ```env
   WORKOS_DIRECTORY_SYNC_ENABLED=true
   WORKOS_DIRECTORY_WEBHOOK_SECRET=your_webhook_secret_here
   ```

### Configure Webhook Endpoint

1. In WorkOS dashboard, configure webhook URL:
   ```
   https://yourdomain.com/api/webhooks/workos
   ```

2. The webhook handles these events:
   - `dsync.user.created` - New user provisioned
   - `dsync.user.updated` - User information updated
   - `dsync.user.deleted` - User deprovisioned
   - `dsync.group.created/updated/deleted` - Group events

## Authentication Flow

### SSO Flow

```
1. User clicks "Sign in with SSO"
   ↓
2. Redirected to WorkOS (/auth/workos/redirect)
   ↓
3. WorkOS redirects to Identity Provider
   ↓
4. User authenticates with their org credentials
   ↓
5. Identity Provider redirects back to WorkOS
   ↓
6. WorkOS redirects to your callback (/auth/workos/callback)
   ↓
7. Your app exchanges code for user profile
   ↓
8. Find or create user in database
   ↓
9. Log user in and redirect to dashboard
```

## Available Routes

### Public Routes

- `GET /login` - Login page
- `GET /auth/workos/redirect` - Initiate SSO login
- `GET /auth/workos/callback` - OAuth callback
- `GET /auth/workos/connections` - List available SSO connections

### Protected Routes

- `POST /auth/workos/logout` - Logout user

### Webhook Routes

- `POST /api/webhooks/workos` - Directory sync webhook

## Customization

### Custom Login Button

```html
<a href="{{ route('auth.workos.redirect') }}" class="btn">
    Sign in with SSO
</a>
```

### Login with Specific Provider

```html
<a href="{{ route('auth.workos.redirect', ['provider' => 'GoogleOAuth']) }}">
    Sign in with Google
</a>
```

### Login with Specific Organization

```html
<a href="{{ route('auth.workos.redirect', ['organization' => 'org_01XXX']) }}">
    Sign in to Company XYZ
</a>
```

### Login with Specific Connection

```html
<a href="{{ route('auth.workos.redirect', ['connection' => 'conn_01XXX']) }}">
    Sign in via Azure AD
</a>
```

## User Model Helpers

### Check if User is WorkOS Authenticated

```php
if ($user->isWorkOsUser()) {
    // User authenticated via WorkOS
}
```

### Get User's Organization

```php
$organization = $user->getWorkOsOrganization();
```

## Security Best Practices

1. **Always use HTTPS in production**
2. **Verify state parameter** (handled automatically)
3. **Validate webhook signatures** (handled automatically)
4. **Keep API keys secure** - Never commit to version control
5. **Use environment variables** for all credentials
6. **Rotate API keys regularly**
7. **Monitor authentication logs**

## Troubleshooting

### Issue: "Invalid redirect URI"

**Solution:** Make sure the redirect URI in your `.env` matches exactly what you configured in WorkOS dashboard (including http/https and trailing slashes)

### Issue: "Invalid state parameter"

**Solution:** 
- Clear your session: `php artisan cache:clear`
- Make sure cookies are enabled
- Check that your `SESSION_DRIVER` is properly configured

### Issue: "User creation failed"

**Solution:**
- Check `WORKOS_AUTO_CREATE_USERS=true` in `.env`
- Verify database connection
- Check logs in `storage/logs/laravel.log`

### Issue: Webhook signature verification fails

**Solution:**
- Verify `WORKOS_DIRECTORY_WEBHOOK_SECRET` matches the secret in WorkOS dashboard
- Ensure webhook payload is not modified
- Check that you're using the raw request body

## Logging

All WorkOS authentication events are logged to `storage/logs/laravel.log`:

```php
// Successful authentication
Log::info('WorkOS authentication successful', [...]);

// Failed authentication
Log::error('WorkOS authentication failed', [...]);

// Directory sync events
Log::info('Directory sync event received', [...]);
```

## Production Deployment

### Checklist

- [ ] Update `WORKOS_REDIRECT_URI` to production URL
- [ ] Add production redirect URI in WorkOS dashboard
- [ ] Use production API keys (starts with `sk_live_`)
- [ ] Enable HTTPS
- [ ] Configure webhook endpoint URL
- [ ] Test SSO flow end-to-end
- [ ] Test directory sync (if enabled)
- [ ] Set up monitoring and alerts
- [ ] Document SSO setup for your organization

### Environment Variables for Production

```env
WORKOS_API_KEY=sk_live_your_production_key
WORKOS_CLIENT_ID=client_your_production_id
WORKOS_REDIRECT_URI=https://yourdomain.com/auth/workos/callback
```

## Advanced Features

### Multi-Organization Support

If you need to support multiple organizations:

```php
// In your controller
public function showOrganizations()
{
    $workos = app(\App\Services\WorkOsService::class);
    $connections = $workos->listConnections();
    
    return view('auth.organizations', compact('connections'));
}
```

### Custom User Attributes

Store additional WorkOS profile data:

```php
// In WorkOsService::findOrCreateUser()
$user->update([
    'workos_raw_attributes' => json_encode($profile),
]);

// Access later
$attributes = json_decode($user->workos_raw_attributes);
```

### Role-Based Access Control

Assign roles based on WorkOS organization:

```php
if ($user->workos_organization_id === 'org_premium_customer') {
    $user->assignRole('premium');
}
```

## Support

- **WorkOS Documentation**: [https://workos.com/docs](https://workos.com/docs)
- **WorkOS Dashboard**: [https://dashboard.workos.com](https://dashboard.workos.com)
- **Laravel Logs**: `storage/logs/laravel.log`
- **WorkOS Support**: support@workos.com

## Next Steps

1. ✅ Complete basic setup (Steps 1-6)
2. Configure your identity provider
3. Test authentication flow
4. (Optional) Enable directory sync
5. Deploy to production
6. Train your team on SSO login

---

**Congratulations!** Your support portal now has enterprise-grade authentication powered by WorkOS! 🎉






