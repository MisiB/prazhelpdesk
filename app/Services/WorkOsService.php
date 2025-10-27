<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use WorkOS\WorkOS;
use WorkOS\UserManagement;

class WorkOsService
{
    public function __construct()
    {
        // Configure WorkOS statically
        WorkOS::setApiKey(config('services.workos.api_key'));
        WorkOS::setClientId(config('services.workos.client_id'));
    }

    /**
     * Get the authorization URL for WorkOS AuthKit
     *
     * @param string|null $intendedUrl Redirect URL after authentication
     * @return string
     */
    public function getAuthorizationUrl(?string $intendedUrl = null): string
    {
        $userManagement = new UserManagement();
        
        $state = [
            'intended_url' => $intendedUrl ?? '/dashboard',
            'timestamp' => time(),
        ];

        return $userManagement->getAuthorizationUrl(
            config('services.workos.redirect_uri'),
            $state,
            UserManagement::AUTHORIZATION_PROVIDER_AUTHKIT
        );
    }

    /**
     * Handle the OAuth callback and authenticate the user
     *
     * @param string $code Authorization code from WorkOS
     * @return object Authentication response from WorkOS
     * @throws \Exception
     */
    public function handleCallback(string $code): object
    {
        try {
            $userManagement = new UserManagement();
            $authResponse = $userManagement->authenticateWithCode(
                config('services.workos.client_id'),
                $code
            );
            
            Log::info('WorkOS authentication successful', [
                'user_id' => $authResponse->user->id ?? null,
                'email' => $authResponse->user->email ?? null,
            ]);

            return $authResponse;
        } catch (\Exception $e) {
            Log::error('WorkOS authentication failed', [
                'error' => $e->getMessage(),
                'code' => $code,
            ]);
            throw $e;
        }
    }

    /**
     * Find or create a user from WorkOS user object
     *
     * @param object $workosUser WorkOS user object from AuthResponse
     * @return User
     */
    public function findOrCreateUser(object $workosUser): User
    {
        // Try to find user by email
        $user = User::where('email', $workosUser->email)->first();

        if ($user) {
            // Update user information
            $user->update([
                'name' => $workosUser->firstName ?? explode('@', $workosUser->email)[0],
                'email' => $workosUser->email,
            ]);
            return $user;
        }

        // Create new user
        return User::create([
            'name' => $workosUser->firstName ?? explode('@', $workosUser->email)[0],
            'surname' => $workosUser->lastName ?? '',
            'email' => $workosUser->email,
            'password' => Hash::make(str()->random(32)), // Random password for WorkOS users
            'email_verified_at' => now(), // WorkOS handles verification
        ]);
    }

    /**
     * Get user details from WorkOS
     *
     * @param string $userId WorkOS user ID
     * @return object|null
     */
    public function getUser(string $userId): ?object
    {
        try {
            $userManagement = new UserManagement();
            return $userManagement->getUser($userId);
        } catch (\Exception $e) {
            Log::error('Failed to get WorkOS user', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}

