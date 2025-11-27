# Update Your .env File for WorkOS

Copy and paste these lines into your `.env` file:

```env
# ==============================================================================
# WORKOS AUTHENTICATION - Add these lines to your .env
# ==============================================================================

# Get your credentials from: https://dashboard.workos.com/
WORKOS_API_KEY=sk_test_your_api_key_here
WORKOS_CLIENT_ID=client_your_client_id_here

# Callback URL (update for production)
WORKOS_REDIRECT_URI="${APP_URL}/auth/workos/callback"

# Optional: Set default authentication method
# Option 1: Use Google OAuth by default
WORKOS_PROVIDER=GoogleOAuth

# Option 2: Use specific SSO connection (leave blank for now)
# WORKOS_CONNECTION=

# Option 3: Use specific organization (leave blank for now)
# WORKOS_ORGANIZATION=

# User provisioning settings
WORKOS_AUTO_CREATE_USERS=true
WORKOS_AUTO_UPDATE_USERS=true
WORKOS_SESSION_LIFETIME=1440

# Directory Sync (Optional - for automatic user provisioning)
WORKOS_DIRECTORY_SYNC_ENABLED=false
WORKOS_DIRECTORY_WEBHOOK_SECRET=

# ==============================================================================
# END WORKOS CONFIGURATION
# ==============================================================================
```

## Then:

1. **Replace** `sk_test_your_api_key_here` with your actual WorkOS API key
2. **Replace** `client_your_client_id_here` with your actual WorkOS Client ID
3. **Run migration**: `php artisan migrate`
4. **Test login**: Visit http://localhost:8000/login

That's it! 🎉











