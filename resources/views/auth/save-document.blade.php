<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sauvegarder votre document - Policify</title>
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
                <span class="text-gray-700">Sauvegarder votre document</span>
            </div>
        </div>
    </div>
</header>

<div class="max-w-4xl mx-auto px-4 py-12">

    <!-- Success Message -->
    <div class="text-center mb-12">
        <div class="bg-green-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-check-circle text-green-600 text-3xl"></i>
        </div>
        <h1 class="text-4xl font-bold text-gray-800 mb-4">
            🎉 Votre document est prêt !
        </h1>
        <p class="text-xl text-gray-600 mb-2">
            Document de cybersécurité généré avec succès
        </p>
        <p class="text-gray-500">
            Créez un compte gratuit pour le sauvegarder et accéder à plus de fonctionnalités
        </p>
    </div>

    <div class="grid lg:grid-cols-2 gap-12 items-start">

        <!-- Formulaire de création de compte -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">💾 Sauvegarder mon document</h2>
                <p class="text-gray-600">Créez votre compte gratuit en 30 secondes</p>
            </div>

            <!-- Méthode recommandée : Magic Link -->
            <div class="mb-8">
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-6">
                    <h3 class="font-semibold text-blue-800 mb-2">
                        <i class="fas fa-magic mr-2"></i>Méthode recommandée
                    </h3>
                    <p class="text-blue-700 text-sm mb-4">
                        Connexion instantanée par email, sans mot de passe à retenir
                    </p>

                    <form id="magic-link-form" class="space-y-4">
                        <div>
                            <label for="email-magic" class="block text-sm font-medium text-gray-700 mb-2">
                                Email professionnel
                            </label>
                            <input type="email" id="email-magic" name="email" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="votre@entreprise.com">
                        </div>
                        <button type="submit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-all font-semibold">
                            <i class="fas fa-paper-plane mr-2"></i>Recevoir le lien de connexion
                        </button>
                    </form>
                </div>
            </div>

            <!-- Séparateur -->
            <div class="relative mb-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">ou</span>
                </div>
            </div>

            <!-- Méthode classique -->
            <div>
                <h3 class="font-semibold text-gray-800 mb-4">
                    <i class="fas fa-user-plus mr-2"></i>Création de compte classique
                </h3>

                <form id="register-form" class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom complet
                        </label>
                        <input type="text" id="name" name="name" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Jean Dupont">
                    </div>
                    <div>
                        <label for="email-register" class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>
                        <input type="email" id="email-register" name="email" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="jean@entreprise.com">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Mot de passe
                        </label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="••••••••">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirmer le mot de passe
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="••••••••">
                    </div>
                    <button type="submit" class="w-full bg-gray-600 text-white py-3 px-6 rounded-lg hover:bg-gray-700 transition-all font-semibold">
                        <i class="fas fa-user-plus mr-2"></i>Créer mon compte
                    </button>
                </form>
            </div>

            <!-- Déjà un compte -->
            <div class="mt-8 pt-6 border-t border-gray-200 text-center">
                <p class="text-gray-600 mb-4">Vous avez déjà un compte ?</p>
                <button onclick="showLoginForm()" class="text-blue-600 hover:text-blue-700 font-medium">
                    Se connecter
                </button>
            </div>
        </div>

        <!-- Avantages et prévisualisation -->
        <div class="space-y-8">

            <!-- Prévisualisation du document -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4">
                    <i class="fas fa-eye mr-2"></i>Votre document généré
                </h3>
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="flex items-center space-x-3 mb-3">
                        <i class="fas fa-file-word text-blue-600 text-2xl"></i>
                        <div>
                            <div class="font-medium text-gray-800">PSSI_Entreprise_2024.docx</div>
                            <div class="text-sm text-gray-600">2,340 mots • 9 pages • Format Word</div>
                        </div>
                    </div>
                    <div class="text-xs text-gray-500 bg-white p-3 rounded border">
                        # POLITIQUE DE SÉCURITÉ DES SYSTÈMES D'INFORMATION

                        ## 1. Contexte et objectifs
                        La présente politique définit les règles de sécurité...

                        ## 2. Organisation de la sécurité
                        L'organisation de la sécurité repose sur...
                    </div>
                </div>
                <div class="flex space-x-3">
                    <button class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition text-sm">
                        <i class="fas fa-download mr-1"></i>Télécharger
                    </button>
                    <button class="flex-1 bg-gray-100 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-200 transition text-sm">
                        <i class="fas fa-eye mr-1"></i>Prévisualiser
                    </button>
                </div>
            </div>

            <!-- Avantages du compte -->
            <div class="bg-gradient-to-br from-green-50 to-blue-50 rounded-xl p-6">
                <h3 class="font-semibold text-gray-800 mb-4">
                    <i class="fas fa-star mr-2"></i>Avec votre compte gratuit
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-check-circle text-green-600"></i>
                        <span class="text-gray-700">Sauvegarde de tous vos documents</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-check-circle text-green-600"></i>
                        <span class="text-gray-700">Accès à votre dashboard personnel</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-check-circle text-green-600"></i>
                        <span class="text-gray-700">Historique et versioning</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-check-circle text-green-600"></i>
                        <span class="text-gray-700">Nouveaux modèles chaque mois</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-check-circle text-green-600"></i>
                        <span class="text-gray-700">Support prioritaire</span>
                    </div>
                </div>
            </div>

            <!-- Témoignage -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-start space-x-4">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150"
                         class="w-12 h-12 rounded-full" alt="Témoignage">
                    <div>
                        <div class="flex text-yellow-400 mb-2">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <p class="text-gray-700 text-sm mb-2">
                            "En 2 minutes j'avais créé mon compte et récupéré tous mes documents. Interface très intuitive !"
                        </p>
                        <p class="text-xs text-gray-500">Thomas M., CEO TechStart</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alternative sans compte -->
    <div class="mt-12 text-center">
        <div class="bg-white rounded-xl shadow-sm p-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">
                <i class="fas fa-download mr-2"></i>Ou téléchargez directement
            </h3>
            <p class="text-gray-600 mb-6">
                Vous préférez télécharger maintenant sans créer de compte ?<br>
                <span class="text-sm text-gray-500">Vous pourrez toujours en créer un plus tard</span>
            </p>
            <div class="flex justify-center space-x-4">
                <button onclick="downloadDirectly()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-all font-medium">
                    <i class="fas fa-download mr-2"></i>Télécharger Word
                </button>
                <button onclick="previewDocument()" class="bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200 transition-all font-medium">
                    <i class="fas fa-eye mr-2"></i>Prévisualiser
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div id="loading-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-8 max-w-sm mx-4 text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <h3 class="font-semibold text-gray-800 mb-2">Traitement en cours...</h3>
        <p class="text-sm text-gray-600" id="loading-message">Envoi du lien de connexion</p>
    </div>
</div>

<!-- Success Modal -->
<div id="success-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-8 max-w-md mx-4 text-center">
        <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-check text-green-600 text-2xl"></i>
        </div>
        <h3 class="font-semibold text-gray-800 mb-2" id="success-title">Email envoyé !</h3>
        <p class="text-gray-600 mb-6" id="success-message">
            Vérifiez votre boîte email et cliquez sur le lien pour vous connecter.
        </p>
        <button onclick="closeModal('success-modal')" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
            Compris
        </button>
    </div>
</div>

<!-- Login Modal -->
<div id="login-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-8 max-w-md mx-4">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-gray-800">Se connecter</h3>
            <button onclick="closeModal('login-modal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="login-form" class="space-y-4">
            <div>
                <label for="login-email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" id="login-email" name="email" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="votre@email.com">
            </div>
            <div>
                <label for="login-password" class="block text-sm font-medium text-gray-700 mb-2">Mot de passe</label>
                <input type="password" id="login-password" name="password" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="••••••••">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-all font-semibold">
                Se connecter
            </button>
        </form>

        <div class="mt-4 text-center">
            <button onclick="sendMagicLinkFromLogin()" class="text-sm text-blue-600 hover:text-blue-700">
                Ou recevoir un lien de connexion par email
            </button>
        </div>
    </div>
</div>

<script>
    // Variables globales
    const sessionId = new URLSearchParams(window.location.search).get('session');

    // Formulaire Magic Link
    document.getElementById('magic-link-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const email = document.getElementById('email-magic').value;
        await sendMagicLink(email);
    });

    // Formulaire d'inscription
    document.getElementById('register-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        await registerUser(formData);
    });

    // Formulaire de connexion
    document.getElementById('login-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        await loginUser(formData);
    });

    // Envoyer Magic Link
    async function sendMagicLink(email) {
        try {
            showLoading('Envoi du lien de connexion...');

            const response = await fetch('{{ route("auth.magic.send") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    email: email,
                    session_id: sessionId
                })
            });

            const data = await response.json();

            if (response.ok) {
                hideLoading();
                showSuccess(
                    'Email envoyé !',
                    `Un lien de connexion a été envoyé à ${email}. Vérifiez votre boîte email et cliquez sur le lien pour vous connecter automatiquement.`
                );
            } else {
                throw new Error(data.message || 'Erreur lors de l\'envoi');
            }

        } catch (error) {
            hideLoading();
            showError('Erreur lors de l\'envoi de l\'email: ' + error.message);
        }
    }

    // Inscription utilisateur
    async function registerUser(formData) {
        try {
            showLoading('Création de votre compte...');

            const data = Object.fromEntries(formData);
            data.session_id = sessionId;

            const response = await fetch('{{ route("auth.register") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                // Redirection vers le dashboard
                window.location.href = '{{ route("dashboard") }}';
            } else {
                throw new Error(result.message || 'Erreur lors de la création du compte');
            }

        } catch (error) {
            hideLoading();
            showError('Erreur lors de la création du compte: ' + error.message);
        }
    }

    // Connexion utilisateur
    async function loginUser(formData) {
        try {
            showLoading('Connexion en cours...');

            const data = Object.fromEntries(formData);
            data.session_id = sessionId;

            const response = await fetch('{{ route("auth.login") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                window.location.href = '{{ route("dashboard") }}';
            } else {
                throw new Error(result.message || 'Erreur de connexion');
            }

        } catch (error) {
            hideLoading();
            showError('Erreur de connexion: ' + error.message);
        }
    }

    // Téléchargement direct (sans compte)
    function downloadDirectly() {
        // Récupérer le token depuis l'URL ou la session
        const token = sessionId; // À adapter selon votre implémentation
        window.open(`/assistant/download/${token}`, '_blank');
    }

    // Prévisualisation
    function previewDocument() {
        const token = sessionId;
        window.open(`/assistant/preview/${token}`, '_blank');
    }

    // Afficher le formulaire de connexion
    function showLoginForm() {
        document.getElementById('login-modal').classList.remove('hidden');
    }

    // Magic link depuis la modal de login
    // Envoyer Magic Link
    async function sendMagicLink(email) {
        try {
            showLoading('Envoi du lien de connexion...');

            const response = await fetch('{{ route("auth.magic.send") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    email: email,
                    session_id: sessionId
                })
            });

            const data = await response.json();

            if (response.ok) {
                hideLoading();

                // ✅ MODE TEST - Afficher le lien directement
                if (data.magic_link) {
                    const modal = document.getElementById('success-modal');
                    document.getElementById('success-title').textContent = 'Lien de test généré !';
                    document.getElementById('success-message').innerHTML = `
                    <p class="mb-4">Mode test : Cliquez sur le lien ci-dessous pour vous connecter :</p>
                    <a href="${data.magic_link}"
                       class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                        🔗 Se connecter maintenant
                    </a>
                    <p class="text-xs text-gray-500 mt-2">Email : ${email}</p>
                `;
                    modal.classList.remove('hidden');
                } else {
                    showSuccess('Email envoyé !', data.message);
                }
            } else {
                throw new Error(data.message || 'Erreur lors de l\'envoi');
            }

        } catch (error) {
            hideLoading();
            showError('Erreur lors de l\'envoi de l\'email: ' + error.message);
        }
    }

    // Utilitaires pour les modals
    function showLoading(message) {
        document.getElementById('loading-message').textContent = message;
        document.getElementById('loading-modal').classList.remove('hidden');
    }

    function hideLoading() {
        document.getElementById('loading-modal').classList.add('hidden');
    }

    function showSuccess(title, message) {
        document.getElementById('success-title').textContent = title;
        document.getElementById('success-message').textContent = message;
        document.getElementById('success-modal').classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    function showError(message) {
        alert('Erreur: ' + message); // Remplacer par une modal d'erreur plus jolie si besoin
    }

    // Fermer les modals en cliquant à l'extérieur
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('fixed') && e.target.classList.contains('inset-0')) {
            const modals = ['loading-modal', 'success-modal', 'login-modal'];
            modals.forEach(modalId => {
                if (!document.getElementById(modalId).classList.contains('hidden')) {
                    closeModal(modalId);
                }
            });
        }
    });

    // Auto-focus sur le premier champ
    document.addEventListener('DOMContentLoaded', function() {
        const emailField = document.getElementById('email-magic');
        if (emailField) {
            emailField.focus();
        }
    });
</script>
</body>
</html>
