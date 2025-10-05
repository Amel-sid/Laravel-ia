<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Policify - Diagnostic Cyber')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">  <!-- ← IMPORTANT ! -->

    <script src="https://cdn.tailwindcss.com"></script>
    <!-- ... reste du head -->
</head>
<body class="bg-gray-50 min-h-screen font-sans antialiased">

<!-- Navigation -->
<header class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center space-x-4">
                <a href="{{ route('home') }}" class="text-2xl font-bold text-primary">
                    🛡️ Policify
                </a>
                @hasSection('breadcrumb')
                    <span class="text-gray-400">|</span>
                    <div class="text-gray-700 font-medium">
                        @yield('breadcrumb')
                    </div>
                @endif
            </div>

            <!-- User Menu -->
            <div class="flex items-center space-x-4">
                @auth
                    <div class="flex items-center space-x-3">
                            <span class="text-sm text-gray-600">
                                👋 {{ auth()->user()->name }}
                            </span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
                                Déconnexion
                            </button>
                        </form>
                    </div>
                @else
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-primary transition-colors">
                            Connexion
                        </a>
                        <a href="{{ route('register') }}" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            S'inscrire
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</header>

<!-- Flash Messages -->
@if(session('success'))
    <div class="mx-4 mt-4">
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            ✅ {{ session('success') }}
        </div>
    </div>
@endif

@if(session('error'))
    <div class="mx-4 mt-4">
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            ❌ {{ session('error') }}
        </div>
    </div>
@endif

<!-- Main Content -->
<main class="flex-1">
    @yield('content')
</main>

<!-- Footer -->
<footer class="bg-white border-t mt-16">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="text-center text-gray-600">
            <p class="text-sm">
                © {{ date('Y') }} Policify - Assistant conformité cyber pour PME
            </p>
            <p class="text-xs mt-2 opacity-75">
                Diagnostic confidentiel et sécurisé
            </p>
        </div>
    </div>
</footer>

@stack('scripts')
</body>
</html>
