<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Policify - Votre Assistant IA Cybersécurité</title>
    <meta name="description" content="Créez vos documents de cybersécurité (PSSI, Chartes, Procédures) en 5 minutes avec notre IA spécialisée. Gratuit et sans inscription.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .floating-element {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        .typewriter {
            overflow: hidden;
            border-right: .15em solid #667eea;
            white-space: nowrap;
            margin: 0 auto;
            letter-spacing: .05em;
            animation: typing 3.5s steps(40, end), blink-caret .75s step-end infinite;
        }
        @keyframes typing {
            from { width: 0 }
            to { width: 100% }
        }
        @keyframes blink-caret {
            from, to { border-color: transparent }
            50% { border-color: #667eea; }
        }
    </style>
</head>
<body class="bg-gray-50">

<!-- Barre d'authentification en haut -->
<div class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 py-3">
        @auth
            <!-- Utilisateur connecté -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <span class="text-green-600 font-medium">Vous êtes connecté</span>
                    <span class="text-gray-500">•</span>
                    <span class="text-gray-700">Bonjour, {{ auth()->user()->name }}</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-gray-700 text-sm font-medium">
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        @else
            <!-- Utilisateur non connecté -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="text-2xl">🛡️</span>
                    <span class="text-xl font-bold text-gray-800">Policify</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="#" onclick="showLoginModal()" 
                       class="text-gray-600 hover:text-gray-800 font-medium transition-colors">
                        Se connecter
                    </a>
                    <a href="#" onclick="showRegisterModal()" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        S'inscrire
                    </a>
                </div>
            </div>
        @endauth
    </div>
</div>

<!-- Hero Section -->
<section class="gradient-bg min-h-screen flex items-center relative overflow-hidden">
    <!-- Floating elements pour le design -->
    <div class="absolute top-20 left-10 floating-element opacity-20">
        <i class="fas fa-shield-alt text-white text-6xl"></i>
    </div>
    <div class="absolute bottom-20 right-10 floating-element opacity-20" style="animation-delay: -2s;">
        <i class="fas fa-lock text-white text-8xl"></i>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-20 grid lg:grid-cols-2 gap-12 items-center">
        <!-- Texte principal -->
        <div class="text-white space-y-8">
            <div class="space-y-4">
                <h1 class="text-5xl lg:text-6xl font-bold leading-tight">
                    Créez vos documents de
                    <span class="text-yellow-300">cybersécurité</span>
                    en 5 minutes
                </h1>
                <div class="text-xl lg:text-2xl text-blue-100 typewriter">
                    Assistant IA spécialisé pour PME françaises
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-check-circle text-green-300 text-xl"></i>
                    <span class="text-lg">PSSI, Chartes, Procédures générées par IA</span>
                </div>
                <div class="flex items-center space-x-3">
                    <i class="fas fa-check-circle text-green-300 text-xl"></i>
                    <span class="text-lg">Conformité RGPD automatique</span>
                </div>
                <div class="flex items-center space-x-3">
                    <i class="fas fa-check-circle text-green-300 text-xl"></i>
                    <span class="text-lg">Personnalisé selon votre secteur</span>
                </div>
            </div>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 pt-8">
                <button onclick="startFreeTest()" class="bg-yellow-400 text-gray-900 px-8 py-4 rounded-xl text-lg font-semibold hover:bg-yellow-300 transition-all transform hover:scale-105 shadow-lg">
                    🚀 Essayer GRATUITEMENT
                </button>
                <button onclick="scrollToDemo()" class="border-2 border-white text-white px-8 py-4 rounded-xl text-lg font-semibold hover:bg-white hover:text-gray-900 transition-all">
                    👀 Voir la démo
                </button>
            </div>

            <div class="text-sm text-blue-100 pt-4">
                ✨ Aucune inscription requise • Résultat immédiat • 100% gratuit
            </div>
        </div>

        <!-- Mockup / Animation -->
        <div class="relative">
            <div class="bg-white rounded-2xl shadow-2xl p-6 transform rotate-3 hover:rotate-0 transition-transform duration-500">
                <div class="flex items-center space-x-2 mb-4">
                    <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                    <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <div class="bg-blue-100 p-2 rounded-lg">
                            <i class="fas fa-robot text-blue-600"></i>
                        </div>
                        <div class="bg-gray-100 rounded-lg p-3 flex-1">
                            <p class="text-sm text-gray-700">🤖 Quel document souhaitez-vous créer ?</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="bg-blue-600 text-white rounded-lg p-3 inline-block">
                            <p class="text-sm">Une PSSI pour mon entreprise</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="bg-blue-100 p-2 rounded-lg">
                            <i class="fas fa-robot text-blue-600"></i>
                        </div>
                        <div class="bg-gray-100 rounded-lg p-3 flex-1">
                            <p class="text-sm text-gray-700">✨ Parfait ! Votre PSSI personnalisée est prête...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Social Proof / Stats -->
<section class="bg-white py-16">
    <div class="max-w-6xl mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div class="space-y-2">
                <div class="text-3xl font-bold text-gray-800">{{ number_format($stats['documents_generated']) }}+</div>
                <div class="text-gray-600">Documents générés</div>
            </div>
            <div class="space-y-2">
                <div class="text-3xl font-bold text-gray-800">{{ number_format($stats['users_count']) }}+</div>
                <div class="text-gray-600">PME accompagnées</div>
            </div>
            <div class="space-y-2">
                <div class="text-3xl font-bold text-gray-800">{{ $stats['avg_generation_time'] }}</div>
                <div class="text-gray-600">Temps moyen</div>
            </div>
            <div class="space-y-2">
                <div class="text-3xl font-bold text-gray-800">{{ $stats['compliance_rate'] }}</div>
                <div class="text-gray-600">Conformité RGPD</div>
            </div>
        </div>
    </div>
</section>

<!-- Comment ça marche -->
<section id="demo" class="bg-gray-50 py-20">
    <div class="max-w-6xl mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Comment ça marche ?</h2>
            <p class="text-xl text-gray-600">3 étapes simples pour vos documents de cybersécurité</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Étape 1 -->
            <div class="text-center space-y-4">
                <div class="bg-blue-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto">
                    <span class="text-2xl font-bold text-blue-600">1</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-800">Répondez aux questions</h3>
                <p class="text-gray-600">Notre IA vous pose 3-5 questions sur votre entreprise (secteur, taille, données)</p>
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <div class="text-sm text-gray-700">💼 Dans quel secteur ?</div>
                    <div class="mt-2 space-y-1">
                        <div class="bg-gray-100 rounded px-3 py-1 text-xs">Services</div>
                        <div class="bg-gray-100 rounded px-3 py-1 text-xs">Commerce</div>
                    </div>
                </div>
            </div>

            <!-- Étape 2 -->
            <div class="text-center space-y-4">
                <div class="bg-green-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto">
                    <span class="text-2xl font-bold text-green-600">2</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-800">L'IA génère votre document</h3>
                <p class="text-gray-600">En 30-60 secondes, un document professionnel de 8-12 pages personnalisé</p>
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <div class="animate-pulse">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-robot text-blue-600"></i>
                            <span class="text-sm text-gray-700">Génération en cours...</span>
                        </div>
                        <div class="mt-2 space-y-2">
                            <div class="h-2 bg-gray-200 rounded"></div>
                            <div class="h-2 bg-gray-200 rounded w-3/4"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Étape 3 -->
            <div class="text-center space-y-4">
                <div class="bg-purple-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto">
                    <span class="text-2xl font-bold text-purple-600">3</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-800">Téléchargez et utilisez</h3>
                <p class="text-gray-600">Document Word professionnel prêt à imprimer et diffuser dans votre équipe</p>
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <div class="flex items-center justify-center space-x-4">
                        <button class="bg-blue-600 text-white px-3 py-1 rounded text-xs">
                            <i class="fas fa-download mr-1"></i>Word
                        </button>
                        <button class="bg-red-600 text-white px-3 py-1 rounded text-xs">
                            <i class="fas fa-file-pdf mr-1"></i>PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Central -->
        <div class="text-center mt-16">
            <button onclick="startFreeTest()" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-12 py-4 rounded-xl text-xl font-semibold hover:from-blue-700 hover:to-purple-700 transition-all transform hover:scale-105 shadow-xl">
                🎯 Je crée mon premier document GRATUITEMENT
            </button>
            <p class="text-sm text-gray-500 mt-4">Aucune carte bancaire • Aucune inscription • Résultat en 2 minutes</p>
        </div>
    </div>
</section>

<!-- Types de documents -->
<section class="bg-white py-20">
    <div class="max-w-6xl mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Tous vos documents de cybersécurité</h2>
            <p class="text-xl text-gray-600">Générés par IA, adaptés à votre entreprise</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            @foreach($features as $feature)
                <div class="bg-{{ $feature['color'] }}-50 rounded-xl p-8 text-center hover:shadow-lg transition-shadow">
                    <div class="bg-{{ $feature['color'] }}-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="{{ $feature['icon'] }} text-{{ $feature['color'] }}-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">{{ $feature['title'] }}</h3>
                    <p class="text-gray-600">{{ $feature['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Témoignages -->
<section class="bg-gray-50 py-20">
    <div class="max-w-6xl mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Ils nous font confiance</h2>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            @foreach($testimonials as $testimonial)
                <div class="bg-white rounded-xl p-6 shadow-sm">
                    <div class="flex items-center mb-4">
                        <img src="{{ $testimonial['avatar'] }}" class="w-12 h-12 rounded-full mr-4" alt="{{ $testimonial['name'] }}">
                        <div>
                            <h4 class="font-semibold text-gray-800">{{ $testimonial['name'] }}</h4>
                            <p class="text-sm text-gray-600">{{ $testimonial['position'] }}, {{ $testimonial['company'] }}</p>
                        </div>
                    </div>
                    <p class="text-gray-700 mb-3">"{{ $testimonial['text'] }}"</p>
                    <div class="flex text-yellow-400">
                        @for($i = 0; $i < $testimonial['rating']; $i++)
                            <i class="fas fa-star"></i>
                        @endfor
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA Final -->
<section class="gradient-bg py-20">
    <div class="max-w-4xl mx-auto text-center px-4">
        <h2 class="text-4xl font-bold text-white mb-6">Prêt à sécuriser votre entreprise ?</h2>
        <p class="text-xl text-blue-100 mb-10">Créez votre premier document de cybersécurité en moins de 5 minutes</p>

        <button onclick="startFreeTest()" class="bg-yellow-400 text-gray-900 px-12 py-4 rounded-xl text-xl font-semibold hover:bg-yellow-300 transition-all transform hover:scale-105 shadow-xl mb-6">
            🚀 Commencer GRATUITEMENT maintenant
        </button>

        <div class="flex justify-center items-center space-x-8 text-blue-100 text-sm">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                Gratuit sans inscription
            </div>
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                Résultat en 2 minutes
            </div>
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                Document professionnel
            </div>
        </div>
    </div>
</section>

<!-- Footer simple -->
<footer class="bg-gray-800 text-white py-8">
    <div class="max-w-6xl mx-auto px-4">
        <div class="grid md:grid-cols-4 gap-8">
            <div class="md:col-span-2">
                <div class="flex items-center space-x-2 mb-4">
                    <span class="text-2xl">🛡️</span>
                    <span class="text-xl font-bold">Policify</span>
                </div>
                <p class="text-gray-400 mb-4">
                    Assistant IA spécialisé en cybersécurité pour PME françaises.
                    Créez vos documents de sécurité en quelques minutes.
                </p>
            </div>
            <div>
                <h3 class="font-semibold mb-4">Produit</h3>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="#" class="hover:text-white">Fonctionnalités</a></li>
                    <li><a href="#" class="hover:text-white">Tarifs</a></li>
                    <li><a href="#demo" class="hover:text-white">Démo</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-semibold mb-4">Support</h3>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="#" class="hover:text-white">Centre d'aide</a></li>
                    <li><a href="#" class="hover:text-white">Contact</a></li>
                    <li><a href="#" class="hover:text-white">Confidentialité</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
            <p>© {{ date('Y') }} Policify - Assistant IA Cybersécurité pour PME</p>
        </div>
    </div>
</footer>

<!-- Modales de connexion et inscription -->
@guest
    <!-- Modal de connexion -->
    <div id="loginModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Se connecter</h3>
                        <button onclick="closeLoginModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form action="{{ route('auth.login.custom') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="email" name="email" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="votre@email.com">
                        </div>
                        
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                            <input type="password" id="password" name="password" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="••••••••">
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-600">Se souvenir de moi</span>
                            </label>
                        </div>
                        
                        <button type="submit" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md font-medium transition-colors">
                            Se connecter
                        </button>
                    </form>
                    
                    <div class="mt-4 text-center">
                        <p class="text-sm text-gray-600">
                            Pas encore de compte ? 
                            <a href="#" onclick="closeLoginModal(); showRegisterModal();" class="text-blue-600 hover:text-blue-800 font-medium">
                                S'inscrire
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'inscription -->
    <div id="registerModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">S'inscrire</h3>
                        <button onclick="closeRegisterModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form action="{{ route('auth.register') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom complet</label>
                            <input type="text" id="name" name="name" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="John Doe">
                        </div>
                        
                        <div>
                            <label for="register_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="register_email" name="email" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="votre@email.com">
                        </div>
                        
                        <div>
                            <label for="register_password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                            <input type="password" id="register_password" name="password" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="••••••••">
                        </div>
                        
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmer le mot de passe</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="••••••••">
                        </div>
                        
                        <button type="submit" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md font-medium transition-colors">
                            S'inscrire
                        </button>
                    </form>
                    
                    <div class="mt-4 text-center">
                        <p class="text-sm text-gray-600">
                            Déjà un compte ? 
                            <a href="#" onclick="closeRegisterModal(); showLoginModal();" class="text-blue-600 hover:text-blue-800 font-medium">
                                Se connecter
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endguest

<script>
    function startFreeTest() {
        // Analytics
        trackConversion('cta_click', 'hero');

        // Redirection vers l'assistant IA sans inscription
        window.location.href = '{{ route("assistant.start") }}';
    }

    function scrollToDemo() {
        document.getElementById('demo').scrollIntoView({ behavior: 'smooth' });
        trackConversion('demo_view', 'hero');
    }

    // Analytics simple
    function trackConversion(action, source) {
        fetch('/api/track-conversion', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ action, source })
        }).catch(err => console.log('Analytics error:', err));
    }

    // Gestion des modales
    function showLoginModal() {
        document.getElementById('loginModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeLoginModal() {
        document.getElementById('loginModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function showRegisterModal() {
        document.getElementById('registerModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeRegisterModal() {
        document.getElementById('registerModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Fermer les modales en cliquant sur le backdrop
    document.addEventListener('click', function(e) {
        if (e.target.id === 'loginModal') {
            closeLoginModal();
        }
        if (e.target.id === 'registerModal') {
            closeRegisterModal();
        }
    });

    // Animation au scroll
    document.addEventListener('DOMContentLoaded', function() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in');
                }
            });
        }, observerOptions);

        // Observer les sections
        document.querySelectorAll('section').forEach(section => {
            observer.observe(section);
        });
    });
</script>
</body>
</html>
