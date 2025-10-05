@php
    // Fonction pour tronquer le contenu pour les utilisateurs non connectés
    function getTeaserContent($content, $isAuthenticated = false) {
        if ($isAuthenticated) {
            return $content; // Contenu complet pour les utilisateurs connectés
        }

        // Pour les anonymes : montrer seulement les 30% premiers du contenu
        $lines = explode("\n", $content);
        $totalLines = count($lines);
        $previewLines = (int)($totalLines * 0.3); // 30% du contenu
        $previewLines = max($previewLines, 10); // Au minimum 10 lignes

        $teaserLines = array_slice($lines, 0, $previewLines);
        return implode("\n", $teaserLines);
    }

    // Fonction pour formater le contenu (remplace le markdown)
    function formatContent($content) {
        // Échapper le HTML
        $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');

        // Titres
        $content = preg_replace('/^# (.*$)/m', '<h1>$1</h1>', $content);
        $content = preg_replace('/^## (.*$)/m', '<h2>$1</h2>', $content);
        $content = preg_replace('/^### (.*$)/m', '<h3>$1</h3>', $content);

        // Gras
        $content = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $content);

        // Listes à puces
        $content = preg_replace('/^[\*\-] (.+)$/m', '<li>$1</li>', $content);
        $content = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $content);

        // Paragraphes
        $content = preg_replace('/\n\n/', '</p><p>', $content);
        $content = '<p>' . $content . '</p>';

        // Nettoyer les balises vides
        $content = preg_replace('/<p><\/p>/', '', $content);
        $content = preg_replace('/<p>(<h[1-6]>.*<\/h[1-6]>)<\/p>/', '$1', $content);
        $content = preg_replace('/<p>(<ul>.*<\/ul>)<\/p>/s', '$1', $content);

        return $content;
    }

    $displayContent = getTeaserContent($content, auth()->check());
    $formattedContent = formatContent($displayContent);
    $isTeaser = !auth()->check();
@endphp

    <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isTeaser ? 'Aperçu' : 'Document complet' }} - {{ $filename }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .document-content h1 {
            font-size: 2rem;
            font-weight: bold;
            color: #1f2937;
            margin: 2rem 0 1rem 0;
            text-align: center;
            border-bottom: 3px solid #3b82f6;
            padding-bottom: 10px;
        }
        .document-content h2 {
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 8px;
            margin: 2rem 0 1rem 0;
            font-size: 1.5rem;
            font-weight: bold;
            color: #1f2937;
        }
        .document-content h3 {
            margin: 1.5rem 0 0.75rem 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: #374151;
        }
        .document-content p {
            margin-bottom: 1rem;
            color: #374151;
            text-align: justify;
        }
        .document-content ul, .document-content ol {
            margin-bottom: 1rem;
            padding-left: 1.5rem;
        }
        .document-content li {
            margin-bottom: 0.5rem;
        }
        .document-content strong {
            font-weight: 600;
            color: #1f2937;
        }

        /* Styles pour le mode teaser */
        .teaser-overlay {
            position: relative;
        }
        .teaser-overlay::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 150px;
            background: linear-gradient(transparent, rgba(255,255,255,0.95), white);
            pointer-events: none;
        }

        /* Bloquer l'impression pour les anonymes */
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 20px; }
            @if(!auth()->check())
                body::before {
                content: "🔒 INSCRIPTION REQUISE POUR IMPRIMER - Rendez-vous sur policify.com";
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                font-size: 24px;
                font-weight: bold;
                color: #dc2626;
                text-align: center;
                z-index: 9999;
                background: white;
                padding: 20px;
                border: 3px solid #dc2626;
            }
            .document-content { display: none !important; }
        @endif
}
    </style>
</head>
<body class="bg-gray-50 p-8">
<div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">

    <!-- Header avec actions -->
    <div class="flex justify-between items-center p-6 border-b bg-gradient-to-r from-blue-50 to-purple-50 no-print">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                {{ $isTeaser ? '👀 Aperçu gratuit' : '📄 Document complet' }}
            </h1>
            <p class="text-gray-600 mt-1">{{ $filename }}</p>
        </div>

        <div class="flex gap-2">
            @auth
                <!-- UTILISATEUR CONNECTÉ - Accès complet -->
                <button onclick="window.print()"
                        class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition">
                    🖨️ Imprimer
                </button>
                <a href="{{ route('download.word', $download_token) }}"
                   class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                    💾 Word
                </a>
                <a href="{{ route('download.pdf', $download_token) }}"
                   class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                    📄 PDF
                </a>
                <a href="{{ route('dashboard') }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    📊 Dashboard
                </a>
            @else
                <!-- UTILISATEUR ANONYME - Actions limitées -->
                <button onclick="showPrintMessage()"
                        class="bg-gray-400 text-white px-4 py-2 rounded cursor-not-allowed opacity-75">
                    🔒 Imprimer
                </button>
                <button onclick="redirectToSignup()"
                        class="bg-gradient-to-r from-green-600 to-blue-600 text-white px-6 py-2 rounded hover:from-green-700 hover:to-blue-700 transition font-medium">
                    🚀 S'inscrire pour télécharger
                </button>
            @endauth

            <button onclick="window.close()"
                    class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition">
                ✕ Fermer
            </button>
        </div>
    </div>

    @guest
        <!-- Banner d'inscription en haut pour les anonymes -->
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border-b-2 border-amber-300 p-4 no-print">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="text-amber-600 text-2xl">🔓</div>
                    <div>
                        <h3 class="font-bold text-amber-900">Aperçu limité - Inscription requise</h3>
                        <p class="text-sm text-amber-800">Vous voyez seulement 30% du document. Inscrivez-vous pour l'accès complet !</p>
                    </div>
                </div>
                <button onclick="redirectToSignup()"
                        class="bg-amber-600 text-white px-4 py-2 rounded-lg hover:bg-amber-700 transition font-medium text-sm">
                    S'inscrire gratuitement
                </button>
            </div>
        </div>
    @endguest

    <!-- Contenu du document -->
    <div class="p-8">
        <div class="document-content {{ $isTeaser ? 'teaser-overlay' : '' }}">
            <!-- Affichage formaté du contenu (sans Markdown externe) -->
            {!! $formattedContent !!}
        </div>

        @if($isTeaser)
            <!-- Section tronquée pour les anonymes -->
            <div class="text-center py-8 bg-gradient-to-t from-white via-gray-50 to-transparent -mt-20 relative z-10">
                <div class="bg-white border-2 border-dashed border-gray-300 rounded-xl p-8 max-w-md mx-auto">
                    <div class="text-gray-400 text-6xl mb-4">📄</div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Contenu complet disponible</h3>
                    <p class="text-gray-600 mb-6">
                        Il reste encore <strong>70% de votre document</strong> à découvrir !<br>
                        Sections détaillées, procédures, annexes...
                    </p>

                    <div class="space-y-3 text-sm text-gray-700 mb-6">
                        <div class="flex items-center justify-center space-x-2">
                            <span class="text-green-500">✓</span>
                            <span>Procédures détaillées</span>
                        </div>
                        <div class="flex items-center justify-center space-x-2">
                            <span class="text-green-500">✓</span>
                            <span>Plans d'action personnalisés</span>
                        </div>
                        <div class="flex items-center justify-center space-x-2">
                            <span class="text-green-500">✓</span>
                            <span>Annexes et modèles</span>
                        </div>
                        <div class="flex items-center justify-center space-x-2">
                            <span class="text-green-500">✓</span>
                            <span>Format Word éditable</span>
                        </div>
                    </div>

                    <button onclick="redirectToSignup()"
                            class="bg-gradient-to-r from-amber-600 to-orange-600 text-white px-8 py-4 rounded-xl text-lg font-bold hover:from-amber-700 hover:to-orange-700 transition-all transform hover:scale-105 shadow-lg w-full">
                        🔓 Débloquer maintenant
                    </button>
                    <p class="text-xs text-gray-500 mt-3">
                        Inscription gratuite • Téléchargement immédiat
                    </p>
                </div>
            </div>
        @else
            <!-- Footer pour les utilisateurs connectés -->
            <div class="text-center py-6 border-t border-gray-200 bg-green-50">
                <div class="flex items-center justify-center space-x-2 text-green-800">
                    <i class="fas fa-check-circle"></i>
                    <span class="font-medium">Document complet disponible</span>
                </div>
                <p class="text-sm text-green-700 mt-1">
                    Téléchargement, impression et sauvegarde activés
                </p>
            </div>
        @endif
    </div>

    @guest
        <!-- CTA final en bas pour les anonymes -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-8 text-center no-print">
            <h2 class="text-2xl font-bold mb-2">🎯 Prêt à débloquer votre document ?</h2>
            <p class="mb-6 opacity-90">
                Inscription en 30 secondes • Email ou Magic Link • Aucune carte bancaire
            </p>

            <div class="grid md:grid-cols-3 gap-4 mb-8 max-w-2xl mx-auto">
                <div class="bg-white bg-opacity-20 rounded-lg p-4">
                    <div class="text-2xl mb-2">📧</div>
                    <div class="font-medium">Magic Link</div>
                    <div class="text-sm opacity-75">Connexion par email</div>
                </div>
                <div class="bg-white bg-opacity-20 rounded-lg p-4">
                    <div class="text-2xl mb-2">🔐</div>
                    <div class="font-medium">Compte classique</div>
                    <div class="text-sm opacity-75">Email + mot de passe</div>
                </div>
                <div class="bg-white bg-opacity-20 rounded-lg p-4">
                    <div class="text-2xl mb-2">⚡</div>
                    <div class="font-medium">Accès immédiat</div>
                    <div class="text-sm opacity-75">0 validation requise</div>
                </div>
            </div>

            <button onclick="redirectToSignup()"
                    class="bg-white text-blue-600 px-12 py-4 rounded-xl text-xl font-bold hover:bg-gray-100 transition-all transform hover:scale-105 shadow-lg">
                🚀 S'inscrire maintenant
            </button>
        </div>
    @endguest
</div>

<script>
    // Redirection vers inscription
    function redirectToSignup() {
        // Sauvegarder le contexte
        const context = {
            download_token: '{{ $download_token }}',
            filename: '{{ $filename }}',
            return_url: window.location.href
        };

        localStorage.setItem('policify_download_context', JSON.stringify(context));

        // Redirection avec paramètres
        window.location.href = '/auth/save-document?session={{ $download_token }}&source=preview_teaser';
    }

    // Message pour impression bloquée
    function showPrintMessage() {
        alert('🔒 Impression réservée aux membres inscrits\n\nInscrivez-vous gratuitement pour imprimer votre document !');
        redirectToSignup();
    }

    @guest
    // Bloquer les raccourcis d'impression pour les anonymes
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
            e.preventDefault();
            showPrintMessage();
            return false;
        }
    });

    // Bloquer le clic droit pour éviter "Imprimer"
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        return false;
    });

    // Message de bienvenue après 10 secondes
    setTimeout(function() {
        if (!document.hidden) {
            const shouldSignup = confirm('👀 Vous appréciez l\'aperçu ?\n\nInscrivez-vous gratuitement pour accéder au document complet !');
            if (shouldSignup) {
                redirectToSignup();
            }
        }
    }, 10000);
    @endguest
</script>
</body>
</html>

