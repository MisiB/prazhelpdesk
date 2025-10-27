<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AI Support Portal') - {{ config('app.name') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #1e293b;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Navigation */
        .navbar {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        
        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }
        
        .nav-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: #3b82f6;
            text-decoration: none;
        }
        
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.5rem;
            color: #64748b;
        }
        
        .mobile-menu-btn svg {
            width: 1.5rem;
            height: 1.5rem;
        }
        
        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
            align-items: center;
            transition: all 0.3s ease-in-out;
        }
        
        .nav-links a,
        .nav-links button {
            color: #64748b;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
        }
        
        .nav-links a:hover,
        .nav-links button:hover {
            color: #3b82f6;
        }
        
        .nav-links form {
            margin: 0;
        }
        
        /* Buttons */
        .btn {
            padding: 0.625rem 1.25rem;
            border-radius: 0.5rem;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: #3b82f6;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2563eb;
        }
        
        .btn-secondary {
            background: #64748b;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #475569;
        }
        
        /* Cards */
        .card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }
        
        /* Forms */
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #334155;
        }
        
        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 0.625rem;
            border: 1px solid #cbd5e1;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        
        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .form-textarea {
            resize: vertical;
            min-height: 120px;
        }
        
        /* Badges */
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .badge-success {
            background: #dcfce7;
            color: #166534;
        }
        
        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }
        
        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .badge-info {
            background: #dbeafe;
            color: #1e40af;
        }
        
        /* Alerts */
        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background: #dcfce7;
            color: #166534;
            border-left: 4px solid #16a34a;
        }
        
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #dc2626;
        }
        
        .alert-info {
            background: #dbeafe;
            color: #1e40af;
            border-left: 4px solid #3b82f6;
        }
        
        /* Loading */
        .spinner {
            border: 3px solid #f3f4f6;
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 2rem auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }
            
            .nav-links {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: white;
                flex-direction: column;
                gap: 0;
                padding: 1rem 0;
                border-bottom: 1px solid #e2e8f0;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }
            
            .nav-links.active {
                display: flex;
            }
            
            .nav-links li {
                width: 100%;
                padding: 0.75rem 1.5rem;
                border-bottom: 1px solid #f1f5f9;
            }
            
            .nav-links li:last-child {
                border-bottom: none;
            }
            
            .nav-links a,
            .nav-links button {
                display: block;
                width: 100%;
                text-align: left;
                padding: 0.5rem 0;
            }
            
            .nav-brand {
                font-size: 1.25rem;
            }
            
            .container {
                padding: 0 15px;
            }
            
            .card {
                padding: 1rem;
            }
        }
        
        @media (max-width: 480px) {
            .nav-brand {
                font-size: 1.125rem;
            }
            
            body {
                font-size: 0.9375rem;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-content">
                <a href="/" class="nav-brand">PRAZ </a>
                
                <!-- Mobile menu button -->
                <button class="mobile-menu-btn" id="mobile-menu-btn" aria-label="Toggle menu">
                    <svg id="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg id="close-icon" style="display: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                
                <ul class="nav-links" id="nav-links">
                    <li><a href="/">Home</a></li>
                    @auth
                        <li><a href="/tickets">My Tickets</a></li>
                        <li>
                            <form action="/logout" method="POST">
                                @csrf
                                <button type="submit">Logout</button>
                            </form>
                        </li>
                    @endauth
                    @guest
                        <li><a href="/auth/workos">Login</a></li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main style="padding: 2rem 0; min-height: calc(100vh - 200px);">
        <div class="container">
            @yield('content')
        </div>
    </main>
    
    <!-- Footer -->
    <footer style="background: white; border-top: 1px solid #e2e8f0; padding: 2rem 0; margin-top: 3rem;">
        <div class="container" style="text-align: center; color: #64748b;">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p style="margin-top: 0.5rem; font-size: 0.875rem;">Powered by AI Knowledge Base</p>
        </div>
    </footer>
    
    <!-- Mobile Menu Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const navLinks = document.getElementById('nav-links');
            const menuIcon = document.getElementById('menu-icon');
            const closeIcon = document.getElementById('close-icon');
            
            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function() {
                    navLinks.classList.toggle('active');
                    
                    // Toggle icons
                    if (navLinks.classList.contains('active')) {
                        menuIcon.style.display = 'none';
                        closeIcon.style.display = 'block';
                    } else {
                        menuIcon.style.display = 'block';
                        closeIcon.style.display = 'none';
                    }
                });
                
                // Close menu when clicking on a link
                const links = navLinks.querySelectorAll('a, button[type="submit"]');
                links.forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth <= 768) {
                            navLinks.classList.remove('active');
                            menuIcon.style.display = 'block';
                            closeIcon.style.display = 'none';
                        }
                    });
                });
                
                // Close menu when clicking outside
                document.addEventListener('click', function(event) {
                    if (!event.target.closest('.navbar')) {
                        navLinks.classList.remove('active');
                        menuIcon.style.display = 'block';
                        closeIcon.style.display = 'none';
                    }
                });
            }
        });
    </script>
 <script async
 src="https://g43kpaqcf35c36jviqbbeqwi.agents.do-ai.run/static/chatbot/widget.js"
 data-agent-id="8f8a0e67-aeb0-11f0-b074-4e013e2ddde4"
 data-chatbot-id="5uSasRSMtpSzuoNvMvhs-V_PzdHjerK4"
 data-name="PRAZ  Chatbot"
 data-primary-color="#0c7d1b"
 data-secondary-color="#E5E8ED"
 data-button-background-color="#0c7d1b"
 data-starting-message="Hello! How can I help you today?"
 data-logo="/static/chatbot/icons/default-agent.svg">
</script>
    
    @stack('scripts')
</body>
</html>


