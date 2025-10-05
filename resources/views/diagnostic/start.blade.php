@extends('layouts.diagnostic')

@section('title', 'Diagnostic de Maturité Cyber')
@section('breadcrumb', 'Diagnostic')

@section('content')
    <div class="max-w-4xl mx-auto px-4 py-8">

        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">
                🔍 Diagnostic de Maturité Cyber
            </h1>
            <p class="text-gray-600">Version simple pour test</p>
        </div>

        <!-- Progress Bar -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="flex justify-between mb-2">
            <span id="question-counter" class="text-sm font-medium text-gray-700">
                Question 1 sur {{ count($questions) }}
            </span>
                <span id="progress-text" class="text-sm font-medium text-blue-600">
                6%
            </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div id="progress-bar" class="bg-blue-600 h-3 rounded-full transition-all duration-500" style="width: 6%"></div>
            </div>
        </div>

        <!-- Questions -->
        <div class="bg-white rounded-lg shadow-lg p-8" id="questions-container">
            @foreach($questions as $index => $question)
                <div class="question-slide {{ $index === 0 ? '' : 'hidden' }}"
                     data-question-index="{{ $index }}"
                     data-question-id="{{ $question->id }}"
                     data-domain="{{ $question->domain }}">

                    <!-- Domain Badge -->
                    <div class="mb-6">
                <span class="inline-block px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    {{ $question->code }} • {{ ucfirst($question->domain) }}
                </span>
                    </div>

                    <!-- Question -->
                    <h2 class="text-xl font-semibold text-gray-800 mb-6">
                        {{ $question->question }}
                    </h2>

                    <!-- Options -->
                    <div class="space-y-3">
                        @foreach($question->options as $optionIndex => $option)
                            <label class="block cursor-pointer">
                                <input type="radio"
                                       name="question_{{ $question->id }}"
                                       value="{{ $optionIndex }}"
                                       data-points="{{ $option['points'] }}"
                                       data-question-id="{{ $question->id }}"
                                       class="sr-only option-radio">

                                <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-blue-300 transition option-content">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-700">{{ $option['text'] }}</span>
                                        <span class="text-sm text-gray-500">{{ $option['points'] }} pts</span>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Navigation -->
        <div class="flex justify-between items-center mt-8">
            <button id="prev-btn" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition hidden">
                ← Précédent
            </button>

            <div class="text-center">
                <p class="text-sm text-gray-500">Diagnostic sécurisé</p>
            </div>

            <button id="next-btn" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50" disabled>
                Suivant →
            </button>

            <button id="submit-btn" class="hidden px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                🚀 Terminer
            </button>
        </div>

    </div>

    <!-- JavaScript inline simple -->
    <script>
        console.log('🚀 Page diagnostic chargée');

        let currentQuestion = 0;
        let totalQuestions = {{ count($questions) }};
        let answers = {};

        document.addEventListener('DOMContentLoaded', function() {
            console.log('✅ DOM ready, total questions:', totalQuestions);

            // Vérification des éléments essentiels
            const nextBtn = document.getElementById('next-btn');
            const prevBtn = document.getElementById('prev-btn');
            const submitBtn = document.getElementById('submit-btn');

            console.log('🔍 Éléments trouvés:', {
                nextBtn: !!nextBtn,
                prevBtn: !!prevBtn,
                submitBtn: !!submitBtn
            });

            // Gestion des boutons avec vérification
            if (nextBtn) {
                nextBtn.addEventListener('click', nextQuestion);
                console.log('➡️ Event nextBtn ajouté');
            }

            if (prevBtn) {
                prevBtn.addEventListener('click', prevQuestion);
                console.log('⬅️ Event prevBtn ajouté');
            }

            if (submitBtn) {
                submitBtn.addEventListener('click', submitDiagnostic);
                console.log('🚀 Event submitBtn ajouté');
            }

            // Gestion sélection réponses avec vérification renforcée
            document.addEventListener('change', function(e) {
                if (e.target.matches('.option-radio')) {
                    console.log('🎯 Change détecté sur:', e.target);
                    selectAnswer(e.target);
                }
            });

            // Initialisation
            updateNavigation();
            console.log('🎮 Initialisation terminée');
        });

        function selectAnswer(radio) {
            console.log('🔧 selectAnswer appelée avec:', radio);

            const questionId = radio.dataset.questionId;
            const points = parseInt(radio.dataset.points);
            const questionSlide = radio.closest('.question-slide');

            if (!questionSlide) {
                console.error('❌ Impossible de trouver questionSlide pour:', radio);
                return;
            }

            const domain = questionSlide.dataset.domain;

            console.log(`✅ Réponse Q${questionId}: ${points} pts (${domain})`);

            // Stocker la réponse
            answers[questionId] = {
                option: radio.value,
                points: points,
                domain: domain
            };

            // Style visuel avec vérifications
            const allOptions = questionSlide.querySelectorAll('.option-content');
            console.log('🎨 Options trouvées pour styling:', allOptions.length);

            allOptions.forEach((opt, index) => {
                if (opt && opt.classList) {
                    opt.classList.remove('border-blue-500', 'bg-blue-50');
                    opt.classList.add('border-gray-200');
                } else {
                    console.error('❌ Option sans classList à l\'index:', index);
                }
            });

            const selectedContent = radio.closest('.option-content');
            if (selectedContent && selectedContent.classList) {
                selectedContent.classList.remove('border-gray-200');
                selectedContent.classList.add('border-blue-500', 'bg-blue-50');
                console.log('✅ Style appliqué à la réponse sélectionnée');
            } else {
                console.error('❌ selectedContent non trouvé ou sans classList');
            }

            // Activer navigation
            updateNavigation();

            // Auto-advance avec délai plus court pour test
            setTimeout(() => {
                console.log('⏱️ Auto-advance déclenché');
                if (currentQuestion < totalQuestions - 1) {
                    nextQuestion();
                }
            }, 1500); // 1.5 secondes pour avoir le temps de voir
        }

        function nextQuestion() {
            console.log('➡️ nextQuestion appelée');

            if (currentQuestion < totalQuestions - 1) {
                hideQuestion(currentQuestion);
                currentQuestion++;
                showQuestion(currentQuestion);
                updateProgress();
                updateNavigation();
                console.log(`📄 Passage à la question ${currentQuestion + 1}`);
            }
        }

        function prevQuestion() {
            console.log('⬅️ prevQuestion appelée');

            if (currentQuestion > 0) {
                hideQuestion(currentQuestion);
                currentQuestion--;
                showQuestion(currentQuestion);
                updateProgress();
                updateNavigation();
                console.log(`📄 Retour à la question ${currentQuestion + 1}`);
            }
        }

        function showQuestion(index) {
            const question = document.querySelector(`[data-question-index="${index}"]`);
            if (question) {
                question.classList.remove('hidden');
                console.log(`👁️ Question ${index + 1} affichée`);
            } else {
                console.error(`❌ Question ${index + 1} non trouvée`);
            }
        }

        function hideQuestion(index) {
            const question = document.querySelector(`[data-question-index="${index}"]`);
            if (question) {
                question.classList.add('hidden');
                console.log(`🙈 Question ${index + 1} cachée`);
            }
        }

        function updateProgress() {
            const progress = Math.round(((currentQuestion + 1) / totalQuestions) * 100);

            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');
            const questionCounter = document.getElementById('question-counter');

            if (progressBar) progressBar.style.width = progress + '%';
            if (progressText) progressText.textContent = progress + '%';
            if (questionCounter) questionCounter.textContent = `Question ${currentQuestion + 1} sur ${totalQuestions}`;

            console.log(`📊 Progrès mis à jour: ${progress}%`);
        }

        function updateNavigation() {
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');
            const submitBtn = document.getElementById('submit-btn');

            console.log('🧭 updateNavigation appelée, currentQuestion:', currentQuestion);

            // Bouton précédent
            if (prevBtn) {
                if (currentQuestion === 0) {
                    prevBtn.classList.add('hidden');
                } else {
                    prevBtn.classList.remove('hidden');
                }
            }

            // Vérifier si question répondue
            const currentSlide = document.querySelector(`[data-question-index="${currentQuestion}"]`);
            if (!currentSlide) {
                console.error('❌ currentSlide non trouvé pour index:', currentQuestion);
                return;
            }

            const questionId = currentSlide.dataset.questionId;
            const isAnswered = answers[questionId] !== undefined;

            console.log(`🤔 Question ${questionId} répondue:`, isAnswered);

            // Dernière question ?
            if (currentQuestion === totalQuestions - 1) {
                if (nextBtn) nextBtn.classList.add('hidden');
                if (submitBtn) {
                    submitBtn.classList.remove('hidden');
                    submitBtn.disabled = !isAnswered;
                    console.log('🏁 Dernière question, bouton submit visible, disabled:', !isAnswered);
                }
            } else {
                if (nextBtn) {
                    nextBtn.classList.remove('hidden');
                    nextBtn.disabled = !isAnswered;
                    console.log('➡️ Bouton next, disabled:', !isAnswered);
                }
                if (submitBtn) {
                    submitBtn.classList.add('hidden');
                }
            }
        }

        function submitDiagnostic() {
            console.log('🚀 submitDiagnostic appelée');

            // Au lieu de l'alerte, montrer un modal pour les infos entreprise
            showCompanyModal();
        }

        function showCompanyModal() {
            // Créer un modal simple
            const modal = document.createElement('div');
            modal.innerHTML = `
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-8 rounded-xl max-w-md w-full mx-4">
                <h3 class="text-xl font-bold mb-4">🎉 Diagnostic terminé !</h3>
                <p class="text-gray-600 mb-6">Pour personnaliser vos résultats :</p>

                <form id="company-form">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nom de votre entreprise *</label>
                        <input type="text" id="company_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Ex: Ma Super PME">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Secteur d'activité</label>
                        <select id="sector" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionnez...</option>
                            <option value="it">Informatique / Tech</option>
                            <option value="finance">Finance / Banque</option>
                            <option value="health">Santé / Médical</option>
                            <option value="industry">Industrie</option>
                            <option value="commerce">Commerce</option>
                            <option value="services">Services</option>
                            <option value="other">Autre</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre d'employés</label>
                        <select id="employees_count" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionnez...</option>
                            <option value="1-10">1 à 10</option>
                            <option value="11-50">11 à 50</option>
                            <option value="51-200">51 à 200</option>
                            <option value="201-500">201 à 500</option>
                            <option value="500+">Plus de 500</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition font-semibold">
                        🚀 Voir mes résultats
                    </button>
                </form>
            </div>
        </div>
    `;

            document.body.appendChild(modal);

            // Focus sur le premier champ
            setTimeout(() => document.getElementById('company_name').focus(), 100);

            // Gérer la soumission
            document.getElementById('company-form').addEventListener('submit', function(e) {
                e.preventDefault();
                sendToServer(modal);
            });
        }

        async function sendToServer(modal) {
            try {
                // Afficher loading
                const submitBtn = modal.querySelector('button[type="submit"]');
                submitBtn.textContent = '⏳ Sauvegarde en cours...';
                submitBtn.disabled = true;

                // Calculer les scores
                const scores = { gouvernance: 0, access: 0, protection: 0, continuity: 0 };
                Object.values(answers).forEach(answer => {
                    if (scores[answer.domain] !== undefined) {
                        scores[answer.domain] += answer.points;
                    }
                });
                const totalScore = Object.values(scores).reduce((sum, score) => sum + score, 0);

                // Préparer les données
                const data = {
                    answers: answers,
                    scores: scores,
                    total_score: totalScore,
                    company_name: document.getElementById('company_name').value,
                    sector: document.getElementById('sector').value,
                    employees_count: document.getElementById('employees_count').value,
                    _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                };

                console.log('📤 Envoi des données:', data);

                // Envoyer au serveur
                const response = await fetch('/diagnostic', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': data._token
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                console.log('✅ Réponse serveur:', result);

                if (result.success) {
                    // Rediriger vers les résultats
                    window.location.href = result.redirect;
                } else {
                    alert('❌ Erreur: ' + result.message);
                    submitBtn.textContent = '🚀 Voir mes résultats';
                    submitBtn.disabled = false;
                }

            } catch (error) {
                console.error('❌ Erreur:', error);
                alert('Erreur de communication avec le serveur');
            }
        }
    </script>
@endsection
