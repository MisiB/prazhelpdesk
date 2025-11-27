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

        <!-- WorkOS SSO Login -->
     

        <!-- Divider -->
        <div style="position: relative; margin: 2rem 0;">
            <div style="border-top: 1px solid #e2e8f0;"></div>
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 0 1rem; color: #64748b; font-size: 0.875rem;">
                or
            </div>
        </div>

        <!-- Email/Password Login (Traditional) -->
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="grid grid-cols-2 gap-4">
            <div class="form-group">
                <label for="name" class="form-label">Name</label>
                <input 
                    id="name" 
                    type="text" 
                    class="form-input" 
                    name="name" 
                    value="{{ old('name') }}" 
                    required 
                    autofocus 
                    autocomplete="name"
                >
            </div>
            <div class="form-group">
                <label for="surname" class="form-label">Surname</label>
                <input 
                    id="surname" 
                    type="text" 
                    class="form-input" 
                    name="surname" 
                    value="{{ old('surname') }}" 
                    required 
                    autofocus 
                    autocomplete="surname"
                >
            </div>

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
                <label for="phone" class="form-label">Phone</label>
                <input 
                    id="phone" 
                    type="number" 
                    class="form-input" 
                    name="phone" 
                    value="{{ old('phone') }}" 
                    required 
                    autofocus 
                    autocomplete="phone"
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
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input 
                        id="password_confirmation" 
                        type="password" 
                        class="form-input" 
                        name="password_confirmation" 
                        required 
                        autocomplete="new-password"/>
                </div>
                </div>
                <div class="card-actions">
                    <button type="submit" class="btn w-full btn-primary">Sign Up</button>
                    <p class="text-center text-sm text-gray-500">Already have an account? <a href="/login" class="text-blue-500">Login</a></p>
                </div>
            </form>
        </div>
    </div>
@endsection
