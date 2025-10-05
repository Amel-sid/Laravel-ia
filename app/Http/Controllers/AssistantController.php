<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Document;
use App\Services\GroqService;
use App\Http\Requests\AssistantMessageRequest;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Html;
use Dompdf\Dompdf;
use Dompdf\Options;

class AssistantController extends Controller
{
    private GroqService $groqService;

    public function __construct(GroqService $groqService)
    {
        $this->groqService = $groqService;
    }

    /**
     * Page d'accueil de l'assistant (sans authentification)
     */
    public function start()
    {
        return view('assistant.start', [
            'session_id' => null,
            'is_anonymous' => !auth()->check()
        ]);
    }

    /**
     * Créer une session anonyme pour l'assistant
     */
    public function createSession(Request $request)
    {
        try {
            $sessionId = 'anon_' . Str::uuid();

            // Stocker en session Laravel + Cache pour sécurité
            $sessionData = [
                'id' => $sessionId,
                'created_at' => now()->toISOString(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_id' => auth()->check() ? auth()->id() : null,
                'steps_completed' => [],
                'answers' => [],
                'document_type' => null,
                'current_step' => 'document_selection'
            ];

            session(['assistant_session' => $sessionData]);
            Cache::put("assistant_session:{$sessionId}", $sessionData, now()->addHours(2));

            return response()->json([
                'success' => true,
                'session_id' => $sessionId,
                'message' => "🤖 Bonjour ! Je suis votre assistant IA cybersécurité.\n\n✨ **Je vais créer votre document personnalisé en 3 étapes :**\n\n📋 Questions sur votre entreprise\n🤖 Génération IA (30-60 sec)\n📄 Document Word professionnel\n\n**Quel document souhaitez-vous créer ?**",
                'options' => [
                    [
                        'id' => 'pssi',
                        'label' => '🛡️ PSSI - Politique de Sécurité',
                        'priority' => 'ESSENTIEL',
                        'description' => 'Document cadre obligatoire'
                    ],
                    [
                        'id' => 'charte',
                        'label' => '👥 Charte Utilisateur',
                        'priority' => 'IMPORTANT',
                        'description' => 'Règles d\'usage pour vos équipes'
                    ],
                    [
                        'id' => 'sauvegarde',
                        'label' => '💾 Procédure Sauvegarde',
                        'priority' => 'IMPORTANT',
                        'description' => 'Protection de vos données'
                    ]
                ],
                'type' => 'document_selection',
                'expires_in' => 7200 // 2 heures
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur création session assistant', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'error' => 'Impossible de créer la session. Veuillez réessayer.'
            ], 500);
        }
    }

    /**
     * Traiter les messages du chat assistant
     */
    public function processMessage(AssistantMessageRequest $request)
    {
        try {
            $sessionId = $request->session_id;
            $sessionData = $this->getSessionData($sessionId);

            if (!$sessionData) {
                return response()->json([
                    'error' => 'Session expirée. Veuillez recommencer.',
                    'action' => 'restart'
                ], 419);
            }

            // Router selon le type de message
            return match($request->message_type) {
                'document_selection' => $this->handleDocumentSelection($request, $sessionData),
                'answer' => $this->handleAnswer($request, $sessionData),
                'generate' => $this->handleGenerate($request, $sessionData),
                default => response()->json(['error' => 'Type de message invalide'], 400)
            };

        } catch (\Exception $e) {
            Log::error('Erreur traitement message assistant', [
                'session_id' => $request->session_id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Une erreur est survenue. Veuillez réessayer.',
                'retry' => true
            ], 500);
        }
    }

    /**
     * Gérer la sélection du type de document
     */
    private function handleDocumentSelection(Request $request, array $sessionData): \Illuminate\Http\JsonResponse
    {
        $documentType = $request->content;
        $questions = $this->getQuestionsForDocument($documentType);

        // Mettre à jour la session
        $sessionData['document_type'] = $documentType;
        $sessionData['questions'] = $questions;
        $sessionData['current_step'] = 'questions';
        $sessionData['current_question'] = 0;

        $this->updateSession($sessionData);

        $firstQuestion = $questions[0];

        return response()->json([
            'message' => "Parfait ! Je vais créer votre **" . $this->getDocumentLabel($documentType) . "**.\n\n📊 **Question 1/" . count($questions) . "** :\n\n" . $firstQuestion['question'],
            'options' => $firstQuestion['options'],
            'type' => 'question',
            'progress' => [
                'current' => 1,
                'total' => count($questions),
                'percentage' => round((1 / count($questions)) * 100)
            ]
        ]);
    }

    /**
     * Gérer les réponses aux questions
     */
    private function handleAnswer(Request $request, array $sessionData): \Illuminate\Http\JsonResponse
    {
        $answer = $request->content;
        $questions = $sessionData['questions'];
        $currentQ = $sessionData['current_question'];

        // Enregistrer la réponse
        $sessionData['answers'][$questions[$currentQ]['key']] = $answer;
        $sessionData['current_question']++;

        $this->updateSession($sessionData);

        // Question suivante ou génération ?
        if ($sessionData['current_question'] < count($questions)) {
            $nextQuestion = $questions[$sessionData['current_question']];
            $questionNum = $sessionData['current_question'] + 1;

            return response()->json([
                'message' => "**Question {$questionNum}/" . count($questions) . "** :\n\n" . $nextQuestion['question'],
                'options' => $nextQuestion['options'],
                'type' => 'question',
                'progress' => [
                    'current' => $questionNum,
                    'total' => count($questions),
                    'percentage' => round(($questionNum / count($questions)) * 100)
                ]
            ]);
        } else {
            // Toutes les questions répondues
            return response()->json([
                'message' => "✅ **Parfait ! J'ai toutes les informations nécessaires.**\n\n🎯 Je vais maintenant générer votre **" . $this->getDocumentLabel($sessionData['document_type']) . "** personnalisé avec l'IA.\n\n⏱️ **Génération en cours** (30-60 secondes)...\n\n💡 Votre document contiendra :\n• Structure professionnelle complète\n• Contenu adapté à votre secteur\n• Plan d'action personnalisé\n• Conformité réglementaire",
                'type' => 'ready_to_generate',
                'action_button' => [
                    'text' => '🚀 Générer mon document avec l\'IA',
                    'action' => 'generate'
                ]
            ]);
        }
    }

    /**
     * Générer le document avec l'IA - VERSION CORRIGÉE
     */
    private function handleGenerate(Request $request, array $sessionData): \Illuminate\Http\JsonResponse
    {
        try {
            $documentType = $sessionData['document_type'];
            $answers = $sessionData['answers'];

            Log::info('Génération document assistant', [
                'session_id' => $sessionData['id'],
                'document_type' => $documentType,
                'answers_count' => count($answers),
                'user_id' => auth()->id()
            ]);

            // Générer avec l'IA Groq
            $content = $this->groqService->generateDocument($documentType, $answers);

            if (!$content['success']) {
                throw new \Exception($content['error']);
            }

            // Créer un token pour le téléchargement
            $downloadToken = Str::random(40);
            $filename = $this->generateFilename($documentType);

            // NOUVEAU: Sauvegarder IMMÉDIATEMENT en base de données
            $document = Document::create([
                'user_id' => auth()->check() ? auth()->id() : null, // Null si anonyme
                'title' => str_replace('.docx', '', $filename),
                'type' => $documentType,
                'status' => auth()->check() ? 'generated' : 'anonymous',
                'content' => $content['content'],
                'metadata' => [
                    'source' => auth()->check() ? 'user_generation' : 'anonymous_generation',
                    'generated_by' => 'groq',
                    'session_id' => $sessionData['id'],
                    'user_answers' => $answers,
                    'generated_at' => now()->toISOString(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ],
                'preview_token' => $downloadToken,
                'token_expires_at' => now()->addHours(24),
                'generated_at' => now(),
                'excerpt' => \Str::limit(strip_tags($content['content']), 200)
            ]);

            // Garder aussi dans le cache pour compatibilité (temporaire)
            Cache::put("download_token:{$downloadToken}", [
                'document_id' => $document->id,
                'content' => $content['content'],
                'filename' => $filename,
                'document_type' => $documentType,
                'session_id' => $sessionData['id']
            ], now()->addHours(24));

            // Mettre à jour la session
            $sessionData['current_step'] = 'completed';
            $sessionData['download_token'] = $downloadToken;
            $sessionData['document_id'] = $document->id;
            $this->updateSession($sessionData);

            // Log succès
            Log::info('Document sauvegardé avec succès', [
                'document_id' => $document->id,
                'user_id' => auth()->id(),
                'status' => $document->status,
                'preview_token' => $downloadToken
            ]);

            return response()->json([
                'message' => "🎉 **Votre " . $this->getDocumentLabel($documentType) . " est prêt !**\n\n📄 Document personnalisé généré par IA\n✏️ Contenu adapté à votre entreprise\n🎯 Prêt à être utilisé\n\n💡 **Le document contient :**\n- Table des matières complète\n- Procédures détaillées\n- Plan d'action personnalisé\n- Sections à compléter selon vos besoins",
                'type' => 'completed',
                'document' => [
                    'id' => $document->id,
                    'content' => substr($content['content'], 0, 500) . '...', // Aperçu seulement
                    'filename' => $filename,
                    'type' => $documentType,
                    'word_count' => $document->word_count,
                    'estimated_pages' => $document->estimated_pages,
                    'download_token' => $downloadToken,
                    'is_saved' => true,
                    'is_authenticated' => auth()->check()
                ],
                'actions' => [
                    ['text' => '👁️ Prévisualiser', 'action' => 'preview', 'free' => true],
                    ['text' => '🔄 Nouveau document', 'action' => 'restart', 'free' => true]
                ],
                // CTA freemium seulement pour les anonymes
                'premium_features' => auth()->check() ? null : [
                    'title' => '📥 Télécharger votre document',
                    'subtitle' => 'Créez un compte gratuit pour télécharger en Word ou PDF',
                    'benefits' => [
                        '📝 **Format Word** éditable pour personnaliser',
                        '📄 **Format PDF** professionnel pour partager',
                        '💾 **Sauvegarde** automatique de tous vos documents',
                        '🎯 **Documents illimités** + nouveaux modèles',
                        '📊 **Dashboard** avec suivi et statistiques'
                    ],
                    'cta' => 'Créer mon compte GRATUIT',
                    'note' => 'Aucune carte bancaire requise • Téléchargement immédiat après inscription'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur génération document assistant', [
                'session_id' => $sessionData['id'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => "❌ **Erreur lors de la génération**\n\n" . $e->getMessage() . "\n\nVeuillez réessayer ou contacter le support si le problème persiste.",
                'type' => 'error',
                'action_button' => [
                    'text' => '🔄 Réessayer la génération',
                    'action' => 'generate'
                ]
            ], 500);
        }
    }

    /**
     * Prévisualisation du document - VERSION CORRIGÉE
     */
    public function preview(string $token)
    {
        // Essayer d'abord la BDD, puis le cache
        $document = Document::where('preview_token', $token)->first();

        if ($document) {
            return view('assistant.preview', [
                'content' => $document->content,
                'filename' => $document->title . '.docx',
                'document_type' => $document->type,
                'word_count' => $document->word_count,
                'estimated_pages' => $document->estimated_pages,
                'download_token' => $token,
                'is_authenticated' => Auth::check(),
                'session_id' => $document->metadata['session_id'] ?? null,
                'document_id' => $document->id
            ]);
        }

        // Fallback sur le cache (compatibilité temporaire)
        $data = Cache::get("download_token:{$token}");
        if (!$data) {
            return redirect('/')->with('error', 'Document introuvable ou expiré');
        }

        return view('assistant.preview', [
            'content' => $data['content'],
            'filename' => $data['filename'],
            'document_type' => $data['document_type'],
            'word_count' => str_word_count($data['content']),
            'estimated_pages' => ceil(str_word_count($data['content']) / 250),
            'download_token' => $token,
            'is_authenticated' => Auth::check(),
            'session_id' => $data['session_id'] ?? null
        ]);
    }

    /**
     * Téléchargement Word - VERSION CORRIGÉE
     */
    public function downloadWord(string $token)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.save.form', ['session' => $token])
                ->with('error', 'Inscription requise pour télécharger en Word');
        }

        // Récupérer le document depuis la BDD
        $document = Document::where('preview_token', $token)->first();

        if (!$document) {
            return redirect()->route('dashboard')->with('error', 'Document introuvable');
        }

        // Si le document était anonyme, l'associer à l'utilisateur connecté
        if (!$document->user_id) {
            $document->linkToUser(auth()->user());
            Log::info('Document anonyme associé à utilisateur', [
                'document_id' => $document->id,
                'user_id' => auth()->id()
            ]);
        }

        return $this->generateWordFile([
            'content' => $document->content,
            'filename' => $document->title . '.docx'
        ]);
    }

    /**
     * Téléchargement PDF - VERSION CORRIGÉE
     */
    public function downloadPdf(string $token)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.save.form', ['session' => $token])
                ->with('error', 'Inscription requise pour télécharger en PDF');
        }

        // Récupérer le document depuis la BDD
        $document = Document::where('preview_token', $token)->first();

        if (!$document) {
            return redirect()->route('dashboard')->with('error', 'Document introuvable');
        }

        // Si le document était anonyme, l'associer à l'utilisateur connecté
        if (!$document->user_id) {
            $document->linkToUser(auth()->user());
        }

        return $this->generatePdfFile([
            'content' => $document->content,
            'filename' => str_replace('.docx', '.pdf', $document->title . '.pdf')
        ]);
    }

    /**
     * Générer le fichier Word
     */
    private function generateWordFile(array $data)
    {
        try {
            $phpWord = new PhpWord();
            $section = $phpWord->addSection();

            // Ajouter le contenu formaté
            $this->addMarkdownToSection($section, $data['content']);

            // Préparer le téléchargement
            $filename = $data['filename'];
            $tempFile = tempnam(sys_get_temp_dir(), 'policify_word_');

            $writer = IOFactory::createWriter($phpWord, 'Word2007');
            $writer->save($tempFile);

            return response()->download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Erreur génération Word', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la génération du fichier Word');
        }
    }

    /**
     * Générer le fichier PDF
     */
    private function generatePdfFile(array $data)
    {
        try {
            // Convertir le Markdown en HTML
            $html = $this->convertMarkdownToHtml($data['content']);

            // Configuration Dompdf
            $options = new Options();
            $options->set('defaultFont', 'Arial');
            $options->set('isRemoteEnabled', false);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            return $dompdf->stream($data['filename'], [
                'Attachment' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur génération PDF', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la génération du fichier PDF');
        }
    }

    /**
     * Convertir Markdown en HTML pour PDF
     */
    private function convertMarkdownToHtml($markdown)
    {
        $html = '<html><head><meta charset="UTF-8"><style>
            body { font-family: Arial, sans-serif; font-size: 12px; line-height: 1.6; }
            h1 { color: #2563eb; border-bottom: 2px solid #2563eb; padding-bottom: 10px; }
            h2 { color: #1f2937; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; }
            h3 { color: #374151; }
            p { margin-bottom: 10px; text-align: justify; }
            ul, ol { margin-bottom: 10px; padding-left: 20px; }
            li { margin-bottom: 5px; }
            strong { font-weight: bold; }
        </style></head><body>';

        // Échapper le HTML
        $content = htmlspecialchars($markdown, ENT_QUOTES, 'UTF-8');

        // Convertir Markdown
        $content = preg_replace('/^# (.*$)/m', '<h1>$1</h1>', $content);
        $content = preg_replace('/^## (.*$)/m', '<h2>$1</h2>', $content);
        $content = preg_replace('/^### (.*$)/m', '<h3>$1</h3>', $content);
        $content = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $content);
        $content = preg_replace('/^[\*\-] (.+)$/m', '<li>$1</li>', $content);
        $content = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $content);
        $content = nl2br($content);

        $html .= $content . '</body></html>';

        return $html;
    }

    /**
     * Ajouter contenu Markdown à une section Word
     */
    private function addMarkdownToSection($section, $markdown)
    {
        $lines = explode("\n", $markdown);

        foreach ($lines as $line) {
            $line = trim($line);

            if (empty($line)) {
                continue;
            }

            // Titre niveau 1
            if (preg_match('/^# (.+)/', $line, $matches)) {
                $section->addTitle($matches[1], 1);
            }
            // Titre niveau 2
            elseif (preg_match('/^## (.+)/', $line, $matches)) {
                $section->addTitle($matches[1], 2);
            }
            // Titre niveau 3
            elseif (preg_match('/^### (.+)/', $line, $matches)) {
                $section->addTitle($matches[1], 3);
            }
            // Liste
            elseif (preg_match('/^[\*\-] (.+)/', $line, $matches)) {
                $listText = $this->cleanTextForWord($matches[1]);
                $section->addListItem($listText);
            }
            // Paragraphe normal
            else {
                $cleanText = $this->cleanTextForWord($line);
                $section->addText($cleanText, [
                    'name' => 'Arial',
                    'size' => 11
                ]);
            }
        }
    }

    /**
     * Nettoyer le texte pour Word
     */
    private function cleanTextForWord($text)
    {
        // Supprimer les balises Markdown
        $text = preg_replace('/\*\*(.*?)\*\*/', '$1', $text);
        $text = preg_replace('/\*(.*?)\*/', '$1', $text);
        $text = preg_replace('/\[À COMPLÉTER[^\]]*\]/', '[À COMPLÉTER]', $text);
        $text = str_replace(['<', '>', '&'], ['[', ']', 'et'], $text);

        return trim($text);
    }

    /**
     * Obtenir les données de session
     */
    private function getSessionData(string $sessionId): ?array
    {
        // Essayer d'abord le cache, puis la session
        $data = Cache::get("assistant_session:{$sessionId}");

        if (!$data) {
            $sessionData = session('assistant_session');
            if ($sessionData && $sessionData['id'] === $sessionId) {
                $data = $sessionData;
            }
        }

        return $data;
    }

    /**
     * Mettre à jour la session
     */
    private function updateSession(array $sessionData): void
    {
        session(['assistant_session' => $sessionData]);
        Cache::put("assistant_session:{$sessionData['id']}", $sessionData, now()->addHours(2));
    }

    /**
     * Questions par type de document
     */
    private function getQuestionsForDocument(string $documentType): array
    {
        $commonQuestions = [
            ['key' => 'sector', 'question' => 'Dans quel secteur opérez-vous principalement ?', 'options' => ['Services/Conseil', 'Commerce/E-commerce', 'Industrie/Production', 'Santé/Social', 'Finance/Assurance', 'Éducation', 'Autre']],
            ['key' => 'size', 'question' => 'Combien d\'employés utilisent des outils informatiques ?', 'options' => ['1-10', '11-50', '51-200', '201-500', 'Plus de 500']],
            ['key' => 'data_sensitivity', 'question' => 'Quel type de données manipulez-vous ?', 'options' => ['Données publiques uniquement', 'Données clients classiques', 'Données sensibles/personnelles', 'Données critiques/confidentielles']]
        ];

        $specificQuestions = [
            'pssi' => [
                ['key' => 'compliance', 'question' => 'Avez-vous des obligations réglementaires ?', 'options' => ['RGPD uniquement', 'RGPD + règles sectorielles', 'ISO 27001 visée', 'Certification requise', 'Aucune obligation spécifique']],
                ['key' => 'it_maturity', 'question' => 'Comment décririez-vous votre niveau IT ?', 'options' => ['Basique (PC + internet)', 'Standard (serveur + réseau)', 'Avancé (cloud + sécurité)', 'Expert (infrastructure complexe)']]
            ],
            'charte' => [
                ['key' => 'remote_work', 'question' => 'Vos employés travaillent-ils à distance ?', 'options' => ['Jamais', 'Occasionnellement', 'Régulièrement', 'Majoritairement', 'Exclusivement']],
                ['key' => 'personal_devices', 'question' => 'L\'usage d\'équipements personnels est-il autorisé ?', 'options' => ['Interdit', 'Toléré pour certains usages', 'Autorisé avec encadrement', 'Libre']]
            ],
            'sauvegarde' => [
                ['key' => 'data_types', 'question' => 'Quels types de données devez-vous sauvegarder ?', 'options' => ['Documents bureautiques', 'Base de données métier', 'Emails et communications', 'Tout le système', 'Données critiques uniquement']],
                ['key' => 'current_backup', 'question' => 'Avez-vous actuellement des sauvegardes ?', 'options' => ['Aucune sauvegarde', 'Sauvegardes manuelles', 'Automatiques partielles', 'Système complet', 'Redondance multiple']]
            ]
        ];

        return array_merge($commonQuestions, $specificQuestions[$documentType] ?? []);
    }

    /**
     * Libellé du document
     */
    private function getDocumentLabel(string $documentType): string
    {
        return match($documentType) {
            'pssi' => 'PSSI (Politique de Sécurité)',
            'charte' => 'Charte Utilisateur',
            'sauvegarde' => 'Procédure de Sauvegarde',
            default => 'Document de Sécurité'
        };
    }

    /**
     * Générer le nom du fichier
     */
    private function generateFilename(string $documentType): string
    {
        $names = [
            'pssi' => 'PSSI_Politique_Securite',
            'charte' => 'Charte_Utilisateur',
            'sauvegarde' => 'Procedure_Sauvegarde'
        ];

        $name = $names[$documentType] ?? 'Document_Cyber';
        return $name . '_' . date('Y-m-d') . '.docx';
    }
}
