@extends('layouts.diagnostic')

@section('title', 'Assistant IA - Création de documents')
@section('breadcrumb', 'Assistant IA')

@section('content')
    <div class="max-w-4xl mx-auto px-4 py-8">

        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">
                🤖 Assistant IA Policify
            </h1>
            <p class="text-gray-600">Créez vos documents de sécurité en quelques clics</p>
        </div>

        <!-- Chat Container -->
        <div class="bg-white rounded-xl shadow-lg" id="chat-container">

            <!-- Messages -->
            <div class="p-6 space-y-4 min-h-96 max-h-96 overflow-y-auto" id="messages-container">
                <div class="text-center text-gray-500 py-8">
                    Cliquez sur "Créer un document" pour commencer !
                </div>
            </div>

            <!-- Input Area -->
            <div class="border-t p-4" id="input-area">
                <!-- Boutons/options apparaîtront ici -->
            </div>

            <!-- Loading -->
            <div class="p-4 text-center hidden" id="loading">
                <div class="inline-flex items-center">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mr-3"></div>
                    <span class="text-gray-600">L'IA réfléchit...</span>
                </div>
            </div>
        </div>

        <!-- Actions après diagnostic -->
        @if(isset($assessment))
            <div class="mt-6 text-center">
                <button onclick="startFromDiagnostic({{ $assessment->id }})"
                        class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition font-semibold">
                    🔍 Analyser mon diagnostic
                </button>
            </div>
        @else
            <!-- Mode direct -->
            <div class="mt-6 text-center">
                <button onclick="startDirect()"
                        class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 transition font-semibold">
                    📄 Créer un document
                </button>
            </div>
        @endif

    </div>

    <script>
        let currentSessionId = null;

        // Variables pour le chat
        const messagesContainer = document.getElementById('messages-container');
        const inputArea = document.getElementById('input-area');
        const loadingDiv = document.getElementById('loading');

        // Démarrer depuis un diagnostic
        async function startFromDiagnostic(assessmentId) {
            showLoading();

            try {
                const response = await fetch(`/ai-chat/start-diagnostic/${assessmentId}`, {
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
                    throw new Error(data.message || data.error || 'Erreur serveur');
                }

                currentSessionId = data.session_id;
                displayMessage(data.message, 'ai');
                displayOptions(data.options, data.type);

            } catch (error) {
                console.error('Erreur startFromDiagnostic:', error);
                displayMessage('❌ Erreur de connexion. Veuillez réessayer.', 'error');
            } finally {
                hideLoading();
            }
        }

        // Démarrer en mode direct
        async function startDirect() {
            showLoading();

            try {
                const response = await fetch('/ai-chat/start-direct', {
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
                    throw new Error(data.message || data.error || 'Erreur serveur');
                }

                currentSessionId = data.session_id;
                displayMessage(data.message, 'ai');
                displayOptions(data.options, data.type);

            } catch (error) {
                console.error('Erreur startDirect:', error);
                displayMessage('❌ Erreur de connexion. Veuillez réessayer.', 'error');
            } finally {
                hideLoading();
            }
        }

        // Envoyer une réponse
        async function sendResponse(response, responseType = 'option') {
            // Afficher la réponse de l'utilisateur
            if (responseType === 'option' || responseType === 'answer') {
                displayMessage('✅ ' + response, 'user');
            }

            showLoading();

            try {
                const payload = {};

                if (responseType === 'option') {
                    payload.selected_option = response;
                } else if (responseType === 'answer') {
                    payload.answer = response;
                } else if (responseType === 'generate') {
                    payload.action = 'generate';
                }

                const responseData = await fetch('/ai-chat/message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        session_id: currentSessionId,
                        ...payload
                    })
                });

                const data = await responseData.json();

                if (!responseData.ok) {
                    throw new Error(data.message || data.error || 'Erreur serveur');
                }

                if (data.error) {
                    displayMessage('❌ ' + data.error, 'error');
                    return;
                }

                displayMessage(data.message, 'ai');

                if (data.options) {
                    displayOptions(data.options, data.type);
                } else if (data.action_button) {
                    displayActionButton(data.action_button);
                } else if (data.document) {
                    displayDocument(data.document, data.actions);
                }

            } catch (error) {
                console.error('Erreur sendResponse:', error);
                displayMessage('❌ Erreur de communication: ' + error.message, 'error');
            } finally {
                hideLoading();
            }
        }

        // Afficher un message
        function displayMessage(message, type) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${type}-message mb-4`;

            if (type === 'ai') {
                messageDiv.className += ' bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg';
                messageDiv.innerHTML = `
                    <div class="flex items-start">
                        <span class="text-2xl mr-3">🤖</span>
                        <div class="whitespace-pre-line">${message}</div>
                    </div>
                `;
            } else if (type === 'user') {
                messageDiv.className += ' bg-gray-50 border-l-4 border-gray-400 p-4 rounded-r-lg ml-12';
                messageDiv.innerHTML = `
                    <div class="flex items-start justify-end">
                        <div class="whitespace-pre-line text-right">${message}</div>
                        <span class="text-2xl ml-3">👤</span>
                    </div>
                `;
            } else if (type === 'error') {
                messageDiv.className += ' bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg';
                messageDiv.innerHTML = `<div class="text-red-700">${message}</div>`;
            }

            messagesContainer.appendChild(messageDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        // Afficher les options
        function displayOptions(options, type) {
            inputArea.innerHTML = '';

            if (type === 'select_document') {
                options.forEach(option => {
                    const button = document.createElement('button');
                    button.className = 'block w-full text-left p-4 mb-2 border-2 border-gray-200 rounded-lg hover:border-blue-400 hover:bg-blue-50 transition';

                    if (typeof option === 'object' && option.label) {
                        button.innerHTML = `
                            <div class="font-semibold">${option.label}</div>
                            <div class="text-sm text-gray-600">Priorité: ${option.priority}</div>
                        `;
                        button.onclick = () => sendResponse(option.id, 'option');
                    } else {
                        button.innerHTML = `<div class="font-semibold">${option}</div>`;
                        button.onclick = () => sendResponse(option, 'option');
                    }

                    inputArea.appendChild(button);
                });
            } else if (type === 'question') {
                options.forEach(option => {
                    const button = document.createElement('button');
                    button.className = 'block w-full text-left p-3 mb-2 border border-gray-300 rounded-lg hover:border-blue-400 hover:bg-blue-50 transition';
                    button.textContent = option;
                    button.onclick = () => sendResponse(option, 'answer');
                    inputArea.appendChild(button);
                });
            }
        }

        // Afficher un bouton d'action
        function displayActionButton(actionButton) {
            inputArea.innerHTML = `
                <button onclick="sendResponse('${actionButton.action}', 'generate')"
                        class="w-full bg-green-600 text-white py-4 px-6 rounded-lg hover:bg-green-700 transition font-semibold text-lg">
                    ${actionButton.text}
                </button>
            `;
        }

        // Afficher le document généré - VERSION CORRIGÉE
        function displayDocument(document, actions) {
            // Stocker le contenu du document de manière sûre
            window.currentDocument = {
                filename: document.filename,
                content: document.content,
                type: document.type,
                provider: document.provider
            };

            inputArea.innerHTML = `
                <div class="text-center space-y-4">
                    <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                        <h3 class="font-semibold text-green-800 mb-2">📄 ${document.filename}</h3>
                        <div class="text-sm text-gray-600 mb-2">
                            ${document.provider || 'Généré par IA'} • ${document.content ? Math.ceil(document.content.split(' ').length / 250) : 'X'} pages estimées
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2 justify-center">
                        ${actions.map(action => `
                            <button onclick="handleDocumentAction('${action.action}')"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                                ${action.text}
                            </button>
                        `).join('')}
                    </div>
                </div>
            `;
        }

        // Gérer les actions sur le document
        function handleDocumentAction(action) {
            if (!window.currentDocument) {
                alert('Document non disponible');
                return;
            }

            const doc = window.currentDocument;

            try {
                switch(action) {
                    case 'view':
                        showDocumentPreview(doc.filename, doc.content);
                        break;
                    case 'download':
                        downloadDocument(doc.filename, doc.content);
                        break;
                    case 'email':
                        copyDocumentToClipboard(doc.filename, doc.content);
                        break;
                    case 'restart':
                        restartChat();
                        break;
                    default:
                        console.log('Action non implémentée:', action);
                        alert('Action non disponible pour le moment');
                }
            } catch (error) {
                console.error('Erreur action:', error);
                alert('Erreur lors de l\'exécution de l\'action: ' + error.message);
            }
        }

        // Afficher le document dans une popup
        function showDocumentPreview(filename, content) {
            try {
                const popup = window.open('', '_blank', 'width=900,height=700,scrollbars=yes,resizable=yes');

                if (!popup) {
                    alert('Popup bloquée !\n\nVeuillez autoriser les popups pour ce site et réessayer.\n\nVous pouvez aussi télécharger le document directement.');
                    return;
                }

                const htmlContent = convertMarkdownToHTML(content);

                popup.document.write(`
                    <!DOCTYPE html>
                    <html lang="fr">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>${filename}</title>
                        <script src="https://cdn.tailwindcss.com"><\/script>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        .markdown h2 {
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 8px;
            margin: 2rem 0 1rem 0;
            font-size: 1.5rem;
            font-weight: bold;
            color: #1f2937;
        }
        .markdown h3 {
            margin: 1.5rem 0 0.75rem 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: #374151;
        }
        .markdown p {
            margin-bottom: 1rem;
            line-height: 1.7;
            color: #374151;
        }
        .markdown ul, .markdown ol {
            margin-bottom: 1rem;
            padding-left: 1.5rem;
        }
        .markdown li {
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }
        .markdown strong {
            font-weight: 600;
            color: #1f2937;
        }
        .complete-section {
            background-color: #fef3c7;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 500;
            border: 1px solid #f59e0b;
        }
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 20px; }
            .markdown h2 { break-after: avoid; }
        }
    </style>
    </head>
    <body class="bg-gray-50 p-8">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-8">
        <div class="flex justify-between items-center mb-6 border-b pb-4 no-print">
            <h1 class="text-2xl font-bold text-gray-800">📄 ${filename}</h1>
            <div class="flex gap-2">
                <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    🖨️ Imprimer
                </button>
                <button onclick="downloadFromPreview()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                    💾 Télécharger
                </button>
                <button onclick="window.close()" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition">
                    ✕ Fermer
                </button>
            </div>
        </div>
        <div class="markdown prose max-w-none">
            ${htmlContent}
        </div>
    </div>
    <script>
        function downloadFromPreview() {
            const content = ${JSON.stringify(content)};
            const blob = new Blob([content], { type: 'text/markdown;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = '${filename}';
            a.click();
            URL.revokeObjectURL(url);
        }
    <\/script>
    </body>
    </html>
    `);

    popup.document.close();

    } catch (error) {
    console.error('Erreur preview:', error);
    alert('Erreur lors de l\'affichage du document: ' + error.message + '\n\nEssayez de le télécharger à la place.');
    }
    }

    // Télécharger le document
    function downloadDocument(filename, content) {
    try {
    const blob = new Blob([content], { type: 'text/markdown;charset=utf-8' });
    const url = URL.createObjectURL(blob);

    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.style.display = 'none';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);

    displayMessage("✅ Document téléchargé : " + filename, 'ai');

    } catch (error) {
    console.error('Erreur téléchargement:', error);
    alert('Erreur lors du téléchargement: ' + error.message);
    }
    }

    // Copier le document (simule l'envoi par email)
    function copyDocumentToClipboard(filename, content) {
    try {
    if (navigator.clipboard) {
    navigator.clipboard.writeText(content).then(() => {
    alert('📋 Document copié dans le presse-papier !\n\nVous pouvez maintenant le coller dans votre email ou document.');
    displayMessage("📋 Document copié pour envoi par email", 'ai');
    }).catch(() => {
    fallbackCopyTextToClipboard(content);
    });
    } else {
    fallbackCopyTextToClipboard(content);
    }
    } catch (error) {
    console.error('Erreur copie:', error);
    alert('Impossible de copier automatiquement.\n\nUtilisez la prévisualisation puis Ctrl+A et Ctrl+C pour copier manuellement.');
    }
    }

    // Fallback pour la copie si clipboard API non disponible
    function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.top = '-1000px';
    textArea.style.left = '-1000px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
    const successful = document.execCommand('copy');
    if (successful) {
    alert('📋 Document copié dans le presse-papier !');
    displayMessage("📋 Document copié pour envoi par email", 'ai');
    } else {
    throw new Error('Commande de copie échouée');
    }
    } catch (err) {
    console.error('Fallback copy failed:', err);
    alert('Impossible de copier automatiquement.\n\nVeuillez utiliser la prévisualisation puis copier manuellement avec Ctrl+A et Ctrl+C.');
    }

    document.body.removeChild(textArea);
    }

    // Redémarrer le chat
    function restartChat() {
    if (confirm('Voulez-vous vraiment créer un nouveau document ?\n\nLe chat actuel sera réinitialisé.')) {
    messagesContainer.innerHTML = '<div class="text-center text-gray-500 py-8">Cliquez sur "Créer un document" pour commencer !</div>';
    inputArea.innerHTML = '';
    currentSessionId = null;
    window.currentDocument = null;

    setTimeout(() => {
    displayMessage("🔄 Chat réinitialisé ! Vous pouvez créer un nouveau document.", 'ai');
    }, 500);
    }
    }

    // Convertir Markdown basique en HTML
    function convertMarkdownToHTML(markdown) {
    if (!markdown) return '';

    let html = markdown;

    // Échapper les caractères HTML dangereux
    html = html.replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');

    // Reconvertir les balises markdown
    // Titres
    html = html.replace(/^## (.*$)/gm, '<h2>$1</h2>');
    html = html.replace(/^### (.*$)/gm, '<h3>$1</h3>');

    // Gras
    html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

    // Sections à compléter
    html = html.replace(/\[À COMPLÉTER[^\]]*\]/g, '<span class="complete-section">$&</span>');

    // Listes - version améliorée
    const lines = html.split('\n');
    let inList = false;
    let result = [];

    for (let i = 0; i < lines.length; i++) {
    const line = lines[i];

    if (line.match(/^- /)) {
    if (!inList) {
    result.push('<ul>');
        inList = true;
        }
        result.push('<li>' + line.substring(2) + '</li>');
        } else {
        if (inList) {
        result.push('</ul>');
    inList = false;
    }
    result.push(line);
    }
    }

    if (inList) {
    result.push('</ul>');
    }

    html = result.join('\n');

    // Paragraphes
    html = html.split('\n\n').map(para => {
    para = para.trim();
    if (para && !para.includes('<h') && !para.includes('<ul') && !para.includes('<li>') && !para.includes('</ul>')) {
        return `<p>${para.replace(/\n/g, '<br>')}</p>`;
        }
        return para;
        }).join('\n\n');

        return html;
        }

        // Loading states
        function showLoading() {
        loadingDiv.classList.remove('hidden');
        inputArea.style.opacity = '0.5';
        }

        function hideLoading() {
        loadingDiv.classList.add('hidden');
        inputArea.style.opacity = '1';
        }
        </script>
@endsection
