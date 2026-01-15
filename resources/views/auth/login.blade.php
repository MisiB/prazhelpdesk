@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div style="max-width: 450px; margin: 4rem auto;">
    <div class="card">
        <h1 style="font-size: 2rem; font-weight: 700; text-align: center; margin-bottom: 2rem;">
            Sign In to Support Portal
        </h1>

        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- <!-- WorkOS SSO Login -->
        <div style="margin-bottom: 2rem;">
            <a href="{{ route('auth.workos.redirect') }}" 
               class="btn btn-primary" 
               style="width: 100%; text-align: center; padding: 1rem; font-size: 1.125rem;">
                🔐 Sign in with SSO
            </a>
        </div>

        <!-- Divider -->
        <div style="position: relative; margin: 2rem 0;">
            <div style="border-top: 1px solid #e2e8f0;"></div>
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 0 1rem; color: #64748b; font-size: 0.875rem;">
                or
            </div>
        </div> --}}

        <!-- Email/Password Login (Traditional) -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input 
                    id="email" 
                    type="email" 
                    class="form-input" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus 
                    autocomplete="email"
                >
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input 
                    id="password" 
                    type="password" 
                    class="form-input" 
                    name="password" 
                    required 
                    autocomplete="current-password"
                >
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input type="checkbox" name="remember" id="remember" style="margin-right: 0.5rem;">
                    <span>Remember me</span>
                </label>
            </div>

            <div style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Sign In
                </button>
            </div>
        </form>

        <!-- Additional Options -->
        <div style="margin-top: 2rem; text-align: center;">
           
            <p style="color: #64748b; font-size: 0.875rem; margin-top: 0.5rem;">
                Don't have an account? 
                <a href="{{ route('register') }}" style="color: #3b82f6; text-decoration: none;">
                    Sign up
                </a>
            </p>
        </div>

        {{-- <!-- SSO Connections -->
        <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e2e8f0;">
            <p style="text-align: center; color: #64748b; font-size: 0.875rem; margin-bottom: 1rem;">
                Sign in with your organization
            </p>
            <div style="display: grid; gap: 0.75rem;">
                <a href="{{ route('auth.workos.redirect', ['provider' => 'GoogleOAuth']) }}" 
                   class="btn btn-secondary" 
                   style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <svg width="18" height="18" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z" fill="#4285F4"/>
                        <path d="M9.003 18c2.43 0 4.467-.806 5.956-2.18L12.05 13.56c-.806.54-1.836.86-3.047.86-2.344 0-4.328-1.584-5.036-3.711H.96v2.332C2.44 15.983 5.485 18 9.003 18z" fill="#34A853"/>
                        <path d="M3.964 10.712c-.18-.54-.282-1.117-.282-1.71 0-.593.102-1.17.282-1.71V4.96H.957C.347 6.175 0 7.55 0 9.002c0 1.452.348 2.827.957 4.042l3.007-2.332z" fill="#FBBC05"/>
                        <path d="M9.003 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.464.891 11.426 0 9.003 0 5.485 0 2.44 2.017.96 4.958L3.967 7.29c.708-2.127 2.692-3.71 5.036-3.71z" fill="#EA4335"/>
                    </svg>
                    Sign in with Google
                </a>

                <a href="{{ route('auth.workos.redirect', ['provider' => 'MicrosoftOAuth']) }}" 
                   class="btn btn-secondary" 
                   style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <svg width="18" height="18" viewBox="0 0 23 23" xmlns="http://www.w3.org/2000/svg">
                        <path fill="#f3f3f3" d="M0 0h23v23H0z"/>
                        <path fill="#f35325" d="M1 1h10v10H1z"/>
                        <path fill="#81bc06" d="M12 1h10v10H12z"/>
                        <path fill="#05a6f0" d="M1 12h10v10H1z"/>
                        <path fill="#ffba08" d="M12 12h10v10H12z"/>
                    </svg>
                    Sign in with Microsoft
                </a>
            </div>
        </div> --}}
    </div>

    <!-- Help Text -->
    <div style="text-align: center; margin-top: 2rem; color: #64748b; font-size: 0.875rem;">
        <p>Need help? <a href="/knowledge-base" style="color: #3b82f6; text-decoration: none;">Visit our Knowledge Base</a></p>
    </div>
</div>
@endsection











