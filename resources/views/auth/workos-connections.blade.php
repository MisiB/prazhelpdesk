@extends('layouts.app')

@section('title', 'Choose Organization')

@section('content')
<div style="max-width: 600px; margin: 4rem auto;">
    <div class="card">
        <h1 style="font-size: 2rem; font-weight: 700; text-align: center; margin-bottom: 1rem;">
            Choose Your Organization
        </h1>
        <p style="text-align: center; color: #64748b; margin-bottom: 2rem;">
            Select your organization to continue with Single Sign-On
        </p>

        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if (count($connections) > 0)
            <div style="display: grid; gap: 1rem;">
                @foreach ($connections as $connection)
                    <a href="{{ route('auth.workos.redirect', ['connection' => $connection['id']]) }}" 
                       class="card" 
                       style="cursor: pointer; transition: all 0.2s; text-decoration: none; color: inherit;"
                       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.1)';"
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.1)';">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="width: 48px; height: 48px; border-radius: 0.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 1.25rem;">
                                {{ strtoupper(substr($connection['name'] ?? 'O', 0, 1)) }}
                            </div>
                            <div style="flex: 1;">
                                <h3 style="font-weight: 600; margin-bottom: 0.25rem;">
                                    {{ $connection['name'] ?? 'Organization' }}
                                </h3>
                                <p style="color: #64748b; font-size: 0.875rem;">
                                    {{ ucfirst($connection['type'] ?? 'SSO') }} Connection
                                </p>
                            </div>
                            <div style="color: #3b82f6;">
                                →
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="alert alert-info">
                <p>No SSO connections are currently configured.</p>
                <p style="margin-top: 0.5rem;">Please contact your administrator or use email/password login.</p>
            </div>
        @endif

        <div style="margin-top: 2rem; text-align: center;">
            <a href="{{ route('login') }}" style="color: #64748b; text-decoration: none;">
                ← Back to login
            </a>
        </div>
    </div>
</div>
@endsection











