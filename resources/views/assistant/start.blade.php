<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assistant IA - Policify</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">

<!-- Header -->
<header class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 py-4">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <a href="{{ route('home') }}" class="text-2xl font-bold text-blue-600">
                    🛡️ Policify
                </a>
                <span class="text-gray-400">|</span>
                <span class="text-gray-700">Assistant IA</span>
            </div>
            <div class="flex items-center space-x-4">
                @auth
                    <!-- Utilisateur connecté -->
                    <div class="text-sm text-green-600 font-medium">
                        <i class="fas fa-crown mr-1"></i>
                        {{ auth()->user()->name }} - Membre Premium
                    </div>
                    <a href="{{ route('dashboard') }}"
                       class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition">
                        Dashboard
                    </a>
                @else
                    <!-- Utilisateur anonyme -->
                    <div class="text-sm text-amber-600 font-medium">
                        <i class="fas fa-eye mr-1"></i>
                        Aperçu gratuit • Inscription pour télécharger
                    </div>
                @endauth
            </div>
        </div>
    </div>
</header>

<div class="max-w-4xl mx-auto px-4 py-8">

    <!-- Progress Bar -->
    <div class="mb-8">
        <div class="bg-white rounded-full p-1 shadow-sm">
            <div class="bg-blue-600 rounded-full h-2 transition-all duration-300" id="progress-bar" style="width: 0%"></div>
        </div>
        <div class="text-center text-sm text-gray-500 mt-2" id="progress-text">
            Prêt à commencer
        </div>
    </div>

    <!-- Header informations -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            🤖 Assistant IA Policify
        </h1>
        @auth
            <p class="text-gray-600 text-lg">
                Bienvenue {{ auth()->user()->name }} ! Créez votre nouveau document de cybersécurité
            </p>
            <div class="bg-green-50 border border-green-200 rounded-lg p-3 mt-4 max-w-md mx-auto">
                <div class="flex items-center justify-center text-green-800">
                    <i class="fas fa-crown mr-2"></i>
                    <span class="font-medium">Compte Premium Actif</span>
                </div>
            </div>
        @else
            <p class="text-gray-600 text-lg mb-4">
                Générez votre document de cybersécurité en quelques minutes
            </p>
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 max-w-md mx-auto">
                <div class="text-amber-800 text-sm">
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>Mode découverte :</strong> Aperçu gratuit, inscription requise pour le téléchargement
                </div>
            </div>
        @endauth

        <div class="flex justify-center items-center space-x-6 mt-6 text-sm text-gray-500">
            <div class="flex items-center">
                <i class="fas fa-clock mr-2 text-green-600"></i>
                2-5 minutes
            </div>
            <div class="flex items-center">
                <i class="fas fa-brain mr-2 text-blue-600"></i>
                IA Expert
            </div>
            @auth
                <div class="flex items-center">
                    <i class="fas fa-download mr-2 text-purple-600"></i>
                    Téléchargement direct
                </div>
            @else
                <div class="flex items-center">
                    <i class="fas fa-eye mr-2 text-amber-600"></i>
                    Aperçu gratuit
                </div>
            @endauth
        </div>
    </div>

    <!-- Chat Container -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden" id="chat-container">

        <!-- Messages Container -->
        <div class="p-6 space-y-4 min-h-96 max-h-96 overflow-y-auto" id="messages-container">
            <div class="text-center text-gray-500 py-8">
                <div class="animate-pulse">
                    <i class="fas fa-robot text-4xl text-blue-600 mb-4"></i>
                    <p>Cliquez sur "Commencer" pour démarrer l'assistant IA...</p>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="border-t p-4 bg-gray-50" id="input-area">
            <!-- Les options apparaîtront ici -->
        </div>

        <!-- Loading Indicator -->
        <div class="p-4 text-center hidden" id="loading">
            <div class="inline-flex items-center">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mr-3"></div>
                <span class="text-gray-600">L'IA réfléchit...</span>
            </div>
        </div>
    </div>

    <!-- Start Button -->
    <div class="mt-6 text-center">
        <button onclick="startAssistant()" id="start-button" class="bg-gradient-to-r from-green-600 to-blue-600 text-white px-12 py-4 rounded-xl text-xl font-semibold hover:from-green-700 hover:to-blue-700 transition-all transform hover:scale-105 shadow-xl">
            @auth
                🚀 Créer mon nouveau document
            @else
                🔍 Générer et prévisualiser
            @endauth
        </button>
        <p class="text-sm text-gray-500 mt-3">
            @auth
                ✨ Sauvegarde automatique • Téléchargement immédiat
            @else
                ✨ Génération instantanée • Aperçu gratuit • <strong class="text-blue-600">Inscription simple pour télécharger</strong>
            @endauth
        </p>
    </div>

    @guest
        <!-- Section incitation inscription pour les anonymes -->
        <div class="mt-8 bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200 rounded-xl p-6">
            <div class="text-center">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">
                    🎯 Pourquoi s'inscrire ?
                </h3>
                <div class="grid md:grid-cols-2 gap-4 text-sm">
                    <div class="flex items-center text-gray-700">
                        <span class="text-green-500 mr-2 text-lg">📄</span>
                        <span>Téléchargement Word + PDF</span>
                    </div>
                    <div class="flex items-center text-gray-700">
                        <span class="text-blue-500 mr-2 text-lg">💾</span>
                        <span>Sauvegarde de vos documents</span>
                    </div>
                    <div class="flex items-center text-gray-700">
                        <span class="text-purple-500 mr-2 text-lg">🔄</span>
                        <span>Génération illimitée</span>
                    </div>
                    <div class="flex items-center text-gray-700">
                        <span class="text-yellow-500 mr-2 text-lg">⚡</span>
                        <span>Inscription en 30 secondes</span>
                    </div>
                </div>
            </div>
        </div>
    @endguest

    <!-- Benefits -->
    <div class="mt-12 grid md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl p-6 text-center shadow-sm">
            <div class="bg-blue-100 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-brain text-blue-600 text-xl"></i>
            </div>
            <h3 class="font-semibold text-gray-800 mb-2">IA Spécialisée</h3>
            <p class="text-sm text-gray-600">Expert en cybersécurité formé sur les réglementations françaises</p>
        </div>
        <div class="bg-white rounded-xl p-6 text-center shadow-sm">
            <div class="bg-green-100 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-zap text-green-600 text-xl"></i>
            </div>
            <h3 class="font-semibold text-gray-800 mb-2">Ultra Rapide</h3>
            <p class="text-sm text-gray-600">Document généré en 30-60 secondes au lieu de plusieurs jours</p>
        </div>
        <div class="bg-white rounded-xl p-6 text-center shadow-sm">
            <div class="bg-purple-100 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-cog text-purple-600 text-xl"></i>
            </div>
            <h3 class="font-semibold text-gray-800 mb-2">100% Personnalisé</h3>
            <p class="text-sm text-gray-600">Adapté à votre secteur, taille d'entreprise et besoins spécifiques</p>
        </div>
    </div>
</div>

<script>
    let currentSessionId = null;
    let currentStep = 0;
    const totalSteps = 4;
    const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};

    // Variables DOM
    const messagesContainer = document.getElementById('messages-container');
    const inputArea = document.getElementById('input-area');
    const loadingDiv = document.getElementById('loading');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    const startButton = document.getElementById('start-button');

    // Démarrer l'assistant
    async function startAssistant() {
        try {
            showLoading();
            startButton.style.display = 'none';
            updateProgress(1, 'Connexion à l\'IA...');

            const response = await fetch('{{ route("assistant.session") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({})
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Erreur serveur');
            }

            currentSessionId = data.session_id;
            messagesContainer.innerHTML = '';
            displayMessage(data.message, 'ai');
            displayOptions(data.options, data.type);
            updateProgress(2, 'Assistant IA prêt');

        } catch (error) {
            console.error('Erreur démarrage:', error);
            displayMessage('❌ Erreur de connexion. Veuillez réessayer.', 'error');
            startButton.style.display = 'block';
        } finally {
            hideLoading();
        }
    }

    // Envoyer une réponse
    async function sendResponse(content, messageType = 'document_selection') {
        try {
            if (messageType !== 'generate') {
                displayMessage('✅ ' + content, 'user');
            }

            showLoading();

            const response = await fetch('{{ route("assistant.message") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    session_id: currentSessionId,
                    message_type: messageType,
                    content: content
                })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Erreur serveur');
            }

            displayMessage(data.message, 'ai');

            // Gestion des différents types de réponse
            if (data.options) {
                displayOptions(data.options, data.type);
                if (data.progress) {
                    updateProgress(
                        Math.round((data.progress.current / data.progress.total) * 50) + 25,
                        `Étape ${data.progress.current}/${data.progress.total}`
                    );
                }
            } else if (data.action_button) {
                displayActionButton(data.action_button);
                updateProgress(75, 'Prêt pour génération');
            } else if (data.document) {
                displayDocument(data.document, data.actions);
                updateProgress(100, 'Document prêt !');
            }

        } catch (error) {
            console.error('Erreur envoi:', error);
            displayMessage('❌ Erreur: ' + error.message, 'error');
        } finally {
            hideLoading();
        }
    }

    // Afficher un message
    function displayMessage(message, type) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}-message mb-4 message-enter`;

        if (type === 'ai') {
            messageDiv.className += ' bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg';
            messageDiv.innerHTML = `
                    <div class="flex items-start">
                        <div class="bg-blue-100 p-2 rounded-full mr-3 mt-1">
                            <i class="fas fa-robot text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="whitespace-pre-line text-gray-800">${message}</div>
                        </div>
                    </div>
                `;
        } else if (type === 'user') {
            messageDiv.className += ' bg-gray-50 border-l-4 border-gray-400 p-4 rounded-r-lg ml-12';
            messageDiv.innerHTML = `
                    <div class="flex items-start justify-end">
                        <div class="whitespace-pre-line text-right text-gray-800">${message}</div>
                        <div class="bg-gray-200 p-2 rounded-full ml-3 mt-1">
                            <i class="fas fa-user text-gray-600"></i>
                        </div>
                    </div>
                `;
        } else if (type === 'error') {
            messageDiv.className += ' bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg';
            messageDiv.innerHTML = `
                    <div class="flex items-start">
                        <div class="bg-red-100 p-2 rounded-full mr-3 mt-1">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="text-red-700">${message}</div>
                    </div>
                `;
        }

        messagesContainer.appendChild(messageDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;

        // Animation d'entrée
        setTimeout(() => {
            messageDiv.style.opacity = '1';
            messageDiv.style.transform = 'translateY(0)';
        }, 100);
    }

    // Afficher les options
    function displayOptions(options, type) {
        inputArea.innerHTML = '';

        if (type === 'document_selection') {
            options.forEach(option => {
                const button = document.createElement('button');
                button.className = 'option-button block w-full text-left p-4 mb-3 border-2 border-gray-200 rounded-xl hover:border-blue-400 hover:bg-blue-50 transition-all duration-200 option-hover';

                button.innerHTML = `
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="text-2xl">${option.label.split(' ')[0]}</div>
                                <div>
                                    <div class="font-semibold text-gray-800">${option.label}</div>
                                    <div class="text-sm text-gray-600">Priorité: ${option.priority}</div>
                                    ${option.description ? `<div class="text-xs text-gray-500 mt-1">${option.description}</div>` : ''}
                                </div>
                            </div>
                            <div class="text-blue-400">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                        </div>
                    `;

                button.onclick = () => sendResponse(option.id, 'document_selection');
                inputArea.appendChild(button);
            });
        } else if (type === 'question') {
            options.forEach(option => {
                const button = document.createElement('button');
                button.className = 'option-button block w-full text-left p-3 mb-2 border border-gray-300 rounded-lg hover:border-blue-400 hover:bg-blue-50 transition-all duration-200';
                button.innerHTML = `
                        <div class="flex items-center justify-between">
                            <span class="text-gray-800">${option}</span>
                            <i class="fas fa-check text-green-500 opacity-0 transition-opacity" style="transition-delay: 0.1s;"></i>
                        </div>
                    `;

                button.onclick = () => {
                    // Animation de sélection
                    button.querySelector('i').style.opacity = '1';
                    button.style.backgroundColor = '#dbeafe';
                    button.style.borderColor = '#3b82f6';

                    setTimeout(() => sendResponse(option, 'answer'), 300);
                };

                inputArea.appendChild(button);
            });
        }
    }

    // Afficher bouton d'action
    function displayActionButton(actionButton) {
        inputArea.innerHTML = `
                <button onclick="sendResponse('generate', 'generate')"
                        class="w-full bg-gradient-to-r from-green-600 to-blue-600 text-white py-4 px-6 rounded-xl hover:from-green-700 hover:to-blue-700 transition-all font-semibold text-lg shadow-lg transform hover:scale-105">
                    ${actionButton.text}
                </button>
                <p class="text-center text-sm text-gray-500 mt-3">
                    <i class="fas fa-clock mr-1"></i>Génération en cours... Cela peut prendre 30-60 secondes
                </p>
            `;
    }

    // Afficher le document généré - STRATÉGIE FREEMIUM
    function displayDocument(document, actions) {
        let ctaSection = '';

        if (!isAuthenticated) {
            // Utilisateur non connecté = CTA forte inscription
            ctaSection = `
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 border-2 border-amber-300 rounded-xl p-6">
                    <div class="text-center">
                        <div class="text-amber-600 text-4xl mb-3">🔒</div>
                        <h3 class="font-bold text-amber-900 text-xl mb-2">
                            Document généré avec succès !
                        </h3>
                        <p class="text-amber-800 mb-4">
                            Votre ${document.filename} est prêt. <strong>Inscrivez-vous en 30 secondes</strong> pour le télécharger.
                        </p>

                        <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                            <div class="bg-white rounded-lg p-3 border border-amber-200">
                                <i class="fas fa-file-word text-blue-600 text-xl mb-2"></i>
                                <div class="font-medium">Format Word</div>
                                <div class="text-gray-600">Éditable</div>
                            </div>
                            <div class="bg-white rounded-lg p-3 border border-amber-200">
                                <i class="fas fa-file-pdf text-red-600 text-xl mb-2"></i>
                                <div class="font-medium">Format PDF</div>
                                <div class="text-gray-600">Impression</div>
                            </div>
                        </div>

                        <button onclick="showSignupOptions()"
                                class="bg-gradient-to-r from-amber-600 to-orange-600 text-white px-8 py-4 rounded-xl text-lg font-bold hover:from-amber-700 hover:to-orange-700 transition-all transform hover:scale-105 shadow-lg w-full mb-3">
                            🚀 S'inscrire et télécharger maintenant
                        </button>

                        <p class="text-xs text-amber-700">
                            📧 Inscription par email ou Magic Link • Aucune carte bancaire
                        </p>
                    </div>
                </div>
            `;
        } else {
            // Utilisateur connecté = téléchargement direct
            ctaSection = `
                <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                    <div class="text-center">
                        <i class="fas fa-check-circle text-green-600 text-2xl mb-2"></i>
                        <h4 class="font-semibold text-green-800 mb-2">Document sauvegardé automatiquement</h4>
                        <p class="text-sm text-green-700">Retrouvez tous vos documents dans votre dashboard</p>
                    </div>
                </div>
            `;
        }

        inputArea.innerHTML = `
                <div class="text-center space-y-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                        <div class="flex items-center justify-center mb-4">
                            <div class="bg-blue-100 p-3 rounded-full">
                                <i class="fas fa-file-alt text-blue-600 text-2xl"></i>
                            </div>
                        </div>
                        <h3 class="font-bold text-blue-800 text-lg mb-2">📄 ${document.filename}</h3>
                        <div class="text-sm text-blue-700 space-y-1">
                            <div><i class="fas fa-words mr-2"></i>${document.word_count} mots</div>
                            <div><i class="fas fa-file-alt mr-2"></i>${document.estimated_pages} pages estimées</div>
                            <div><i class="fas fa-check-circle mr-2"></i>Contenu professionnel</div>
                        </div>
                    </div>

                    <div class="flex justify-center space-x-4">
                        <button onclick="previewDocument('${document.download_token}')"
                                class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition font-medium flex items-center space-x-2">
                            <i class="fas fa-eye"></i>
                            <span>${isAuthenticated ? 'Aperçu complet' : 'Aperçu limité'}</span>
                        </button>

                        ${isAuthenticated ? `
                            <button onclick="newDocument()"
                                    class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-medium flex items-center space-x-2">
                                <i class="fas fa-plus"></i>
                                <span>Nouveau document</span>
                            </button>
                        ` : ''}
                    </div>

                    ${ctaSection}
                </div>
            `;
    }

    // Prévisualiser le document
    function previewDocument(token) {
        window.open(`{{ url('/assistant/preview') }}/${token}`, '_blank');
    }

    // Nouveau document
    function newDocument() {
        if(confirm('Voulez-vous créer un nouveau document ?')) {
            location.reload();
        }
    }

    // Afficher les options d'inscription
    function showSignupOptions() {
        // Redirection vers inscription avec token de session
        window.location.href = '{{ route("auth.save.form") }}?session=' + currentSessionId + '&source=assistant';
    }

    // Mettre à jour la barre de progression
    function updateProgress(percentage, text) {
        progressBar.style.width = percentage + '%';
        progressText.textContent = text;
    }

    // États de chargement
    function showLoading() {
        loadingDiv.classList.remove('hidden');
        inputArea.style.opacity = '0.5';
    }

    function hideLoading() {
        loadingDiv.classList.add('hidden');
        inputArea.style.opacity = '1';
    }

    // Styles CSS inline pour les animations
    const style = document.createElement('style');
    style.textContent = `
            .message-enter {
                opacity: 0;
                transform: translateY(20px);
                transition: all 0.3s ease-out;
            }

            .option-hover:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            }

            .option-button {
                transition: all 0.2s ease;
            }

            .option-button:active {
                transform: scale(0.98);
            }
        `;
    document.head.appendChild(style);

    // Raccourcis clavier
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.ctrlKey) {
            const firstOption = document.querySelector('.option-button');
            if (firstOption) {
                firstOption.click();
            }
        }
    });
</script>
</body>
</html>
