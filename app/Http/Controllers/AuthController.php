<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use WorkOS\WorkOS;
use WorkOS\UserManagement;

class AuthController extends Controller
{
    public function __construct()
    {
        // Set WorkOS configuration
        WorkOS::setApiKey(config('services.workos.api_key'));
        WorkOS::setClientId(config('services.workos.client_id'));
       
    }

    /**
     * Show the login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Redirect to WorkOS AuthKit
     */
    public function redirectToWorkOS(Request $request)
    {
        // Store the intended URL in session before redirecting
        if (session('url.intended')) {
            $intendedUrl = session('url.intended');
        } else {
            $intendedUrl = $request->input('intended', '/tickets');
        }

        // Generate WorkOS AuthKit authorization URL
        $userManagement = new UserManagement();
        $authorizationUrl = $userManagement->getAuthorizationUrl(
            config('services.workos.redirect_uri'),
            [
                'intended_url' => $intendedUrl,
                'timestamp' => time(),
            ],
            UserManagement::AUTHORIZATION_PROVIDER_AUTHKIT
        );

        return redirect($authorizationUrl);
    }

    /**
     * Handle WorkOS callback
     */
    public function callback(Request $request)
    {
        try {
            $code = $request->query('code');
            $state = $request->query('state');

            if (!$code) {
                return redirect()->route('login')->withErrors(['error' => 'Authentication failed. No code provided.']);
            }

            // Decode state to get intended URL (WorkOS encodes it as JSON)
            $stateData = json_decode($state, true);
            $intendedUrl = $stateData['intended_url'] ?? '/tickets';

            // Authenticate user with code
            $userManagement = new UserManagement();
            $authResponse = $userManagement->authenticateWithCode(
                config('services.workos.client_id'),
                $code
            );

            // Get user data
            $workosUser = $authResponse->user;
            
            // Find or create user
            $user = User::firstOrCreate(
                ['email' => $workosUser->email],
                [
                    'name' => $workosUser->firstName ?? explode('@', $workosUser->email)[0],
                    'surname' => $workosUser->lastName ?? '',
                    'email' => $workosUser->email,
                    'password' => Hash::make(str()->random(32)), // Random password since we use WorkOS
                ]
            );

            // Log the user in
            Auth::login($user, true);

            // Regenerate session
            $request->session()->regenerate();

            // Clear the intended URL from session
            Session::forget('url.intended');

            // Redirect to intended URL
            return redirect($intendedUrl)->with('success', 'Successfully logged in!');

        } catch (\Exception $e) {
            Log::error('WorkOS authentication error: ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['error' => 'Authentication failed: '.$e->getMessage()]);
        }
    }

    /**
     * Traditional login (fallback if WorkOS is not used)
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $intendedUrl = Session::get('url.intended', '/tickets');
            Session::forget('url.intended');
            
            return redirect($intendedUrl);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Show the registration form
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request (fallback if WorkOS is not used)
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        $intendedUrl = Session::get('url.intended', '/tickets');
        Session::forget('url.intended');
        
        return redirect($intendedUrl);
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
