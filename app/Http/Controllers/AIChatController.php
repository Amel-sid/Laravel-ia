<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\GroqService;

class AIChatController extends Controller
{
    private $groq;

    public function __construct(GroqService $groq)
    {
        $this->groq = $groq;
    }

    public function startDirect()
    {
        try {
            return response()->json([
                'success' => true,
                'session_id' => uniqid(),
                'message' => "🤖 Bonjour ! Je vais vous aider à créer un document de sécurité personnalisé.\n\nQuel document souhaitez-vous créer ?",
                'options' => [
                    ['id' => 'pssi', 'label' => '🛡️ PSSI (Politique de Sécurité)', 'priority' => 'Essentiel'],
                    ['id' => 'charte', 'label' => '👥 Charte utilisateur', 'priority' => 'Important'],
                    ['id' => 'sauvegarde', 'label' => '💾 Procédure de sauvegarde', 'priority' => 'Important']
                ],
                'type' => 'select_document'
            ]);
        } catch (\Exception $e) {
            return $this->handleError($e);
        }
    }

    public function processMessage(Request $request)
    {
        try {
            $sessionId = $request->session_id;

            // Simuler le stockage de session (vous pouvez utiliser la DB plus tard)
            $sessionData = session("chat_{$sessionId}", [
                'step' => 'document_selection',
                'answers' => []
            ]);

            if (isset($request->selected_option)) {
                return $this->handleDocumentSelection($request->selected_option, $sessionId);
            }

            if (isset($request->answer)) {
                return $this->handleQuestionAnswer($request->answer, $sessionId, $sessionData);
            }

            if (isset($request->action) && $request->action === 'generate') {
                return $this->generateDocument($sessionId, $sessionData);
            }

            return response()->json(['error' => 'Action non reconnue'], 400);

        } catch (\Exception $e) {
            return $this->handleError($e);
        }
    }

    private function handleDocumentSelection($documentType, $sessionId)
    {
        $questions = $this->getQuestionsForDocument($documentType);

        session(["chat_{$sessionId}" => [
            'step' => 'gathering_info',
            'document_type' => $documentType,
            'questions' => $questions,
            'current_question' => 0,
            'answers' => []
        ]]);

        $firstQuestion = $questions[0];

        return response()->json([
            'message' => "Parfait ! Je vais créer votre " . $this->getDocumentLabel($documentType) . ".\n\n**Question 1/" . count($questions) . "** : " . $firstQuestion['question'],
            'options' => $firstQuestion['options'],
            'type' => 'question',
            'progress' => [
                'current' => 1,
                'total' => count($questions)
            ]
        ]);
    }

    private function handleQuestionAnswer($answer, $sessionId, $sessionData)
    {
        $questions = $sessionData['questions'];
        $currentQ = $sessionData['current_question'];

        // Enregistrer la réponse
        $sessionData['answers'][$questions[$currentQ]['key']] = $answer;
        $sessionData['current_question']++;

        session(["chat_{$sessionId}" => $sessionData]);

        // Question suivante ou génération ?
        if ($sessionData['current_question'] < count($questions)) {
            $nextQuestion = $questions[$sessionData['current_question']];

            return response()->json([
                'message' => "**Question " . ($sessionData['current_question'] + 1) . "/" . count($questions) . "** : " . $nextQuestion['question'],
                'options' => $nextQuestion['options'],
                'type' => 'question',
                'progress' => [
                    'current' => $sessionData['current_question'] + 1,
                    'total' => count($questions)
                ]
            ]);
        } else {
            // Toutes les questions répondues
            return response()->json([
                'message' => "✅ Parfait ! J'ai toutes les informations nécessaires.\n\n🎯 Je vais maintenant générer votre " . $this->getDocumentLabel($sessionData['document_type']) . " personnalisé avec l'IA.\n\n⏱️ Cela prend généralement 30-60 secondes...",
                'type' => 'ready_to_generate',
                'action_button' => [
                    'text' => '🚀 Générer le document avec l\'IA',
                    'action' => 'generate'
                ]
            ]);
        }
    }

    private function generateDocument($sessionId, $sessionData)
    {
        try {
            $documentType = $sessionData['document_type'];
            $answers = $sessionData['answers'];

            Log::info('Starting AI document generation', [
                'document_type' => $documentType,
                'answers' => $answers
            ]);

            // Appeler l'IA Groq
            $result = $this->groq->generateDocument($documentType, $answers);

            if ($result['success']) {
                // Sauvegarder le contenu généré
                $sessionData['generated_content'] = $result['content'];
                session(["chat_{$sessionId}" => $sessionData]);

                return response()->json([
                    'message' => "🎉 **Votre " . $this->getDocumentLabel($documentType) . " est prêt !**\n\n📄 Document personnalisé généré par IA\n✏️ Contenu adapté à votre entreprise\n🎯 Prêt à être téléchargé\n\n💡 **Le document contient :**\n- Table des matières complète\n- Procédures détaillées\n- Plan d'action personnalisé\n- Sections à compléter par vos soins",
                    'type' => 'completed',
                    'document' => [
                        'content' => $result['content'],
                        'filename' => $documentType . '_' . date('Y-m-d') . '.md',
                        'type' => $documentType,
                        'provider' => $result['provider']
                    ],
                    'actions' => [
                        ['text' => '📥 Voir le document', 'action' => 'view'],
                        ['text' => '📧 Envoyer par email', 'action' => 'email'],
                        ['text' => '🔄 Créer un autre document', 'action' => 'restart']
                    ]
                ]);
            } else {
                throw new \Exception($result['error']);
            }

        } catch (\Exception $e) {
            Log::error('Document generation error', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => "❌ **Erreur lors de la génération**\n\nDésolé, une erreur est survenue : " . $e->getMessage() . "\n\nVeuillez réessayer ou contacter le support.",
                'type' => 'error',
                'action_button' => [
                    'text' => '🔄 Réessayer',
                    'action' => 'generate'
                ]
            ], 500);
        }
    }

    private function getQuestionsForDocument($documentType)
    {
        $allQuestions = [
            'pssi' => [
                ['key' => 'sector', 'question' => 'Dans quel secteur opérez-vous principalement ?', 'options' => ['Services/Conseil', 'Commerce/E-commerce', 'Industrie/Production', 'Santé/Social', 'Finance/Assurance', 'Éducation', 'Autre']],
                ['key' => 'size', 'question' => 'Combien d\'employés utilisent des outils informatiques ?', 'options' => ['1-10', '11-50', '51-200', '201-500', 'Plus de 500']],
                ['key' => 'data_sensitivity', 'question' => 'Quel type de données manipulez-vous ?', 'options' => ['Données publiques uniquement', 'Données clients classiques', 'Données sensibles/personnelles', 'Données critiques/confidentielles']],
                ['key' => 'compliance', 'question' => 'Avez-vous des obligations réglementaires ?', 'options' => ['RGPD uniquement', 'RGPD + règles sectorielles', 'ISO 27001 visée', 'Certification requise', 'Aucune obligation spécifique']],
                ['key' => 'it_maturity', 'question' => 'Comment décririez-vous votre niveau IT ?', 'options' => ['Basique (PC + internet)', 'Standard (serveur + réseau)', 'Avancé (cloud + sécurité)', 'Expert (infrastructure complexe)']]
            ],

            'charte' => [
                ['key' => 'remote_work', 'question' => 'Vos employés travaillent-ils à distance ?', 'options' => ['Jamais', 'Occasionnellement', 'Régulièrement', 'Majoritairement', 'Exclusivement']],
                ['key' => 'personal_devices', 'question' => 'L\'usage d\'équipements personnels est-il autorisé ?', 'options' => ['Interdit', 'Toléré pour certains usages', 'Autorisé avec encadrement', 'Libre']],
                ['key' => 'main_tools', 'question' => 'Quels sont vos outils principaux ?', 'options' => ['Microsoft 365', 'Google Workspace', 'Solutions métier spécifiques', 'Mix de solutions', 'Outils développés en interne']],
                ['key' => 'internet_usage', 'question' => 'Comment encadrez-vous l\'usage d\'internet ?', 'options' => ['Usage libre', 'Filtrage basique', 'Contrôle strict', 'Monitoring actif']]
            ],

            'sauvegarde' => [
                ['key' => 'data_types', 'question' => 'Quels types de données devez-vous sauvegarder ?', 'options' => ['Documents bureautiques', 'Base de données métier', 'Emails et communications', 'Tout le système', 'Données critiques uniquement']],
                ['key' => 'current_backup', 'question' => 'Avez-vous actuellement des sauvegardes ?', 'options' => ['Aucune sauvegarde', 'Sauvegardes manuelles', 'Automatiques partielles', 'Système complet', 'Redondance multiple']],
                ['key' => 'rto_rpo', 'question' => 'En cas de perte, quel délai de récupération acceptez-vous ?', 'options' => ['Quelques heures', '1 jour maximum', '2-3 jours', '1 semaine', 'Plus d\'une semaine']]
            ]
        ];

        return $allQuestions[$documentType] ?? [];
    }

    private function getDocumentLabel($documentType)
    {
        $labels = [
            'pssi' => 'PSSI (Politique de Sécurité)',
            'charte' => 'Charte utilisateur',
            'sauvegarde' => 'Procédure de sauvegarde'
        ];

        return $labels[$documentType] ?? $documentType;
    }

    private function handleError(\Exception $e)
    {
        Log::error('AIChatController error: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'error' => 'Erreur serveur: ' . $e->getMessage()
        ], 500);
    }

    public function startFromDiagnostic($assessmentId)
    {
        try {
            return response()->json([
                'success' => true,
                'session_id' => uniqid(),
                'message' => "🔍 **Analyse de votre diagnostic terminée !**\n\nBasé sur vos réponses, voici les documents que je recommande de créer en priorité :\n\nChoisissez celui que vous souhaitez générer en premier :",
                'options' => [
                    ['id' => 'pssi', 'label' => '🛡️ PSSI - CRITIQUE', 'priority' => 'Urgent - Score faible détecté'],
                    ['id' => 'charte', 'label' => '👥 Charte utilisateur - IMPORTANT', 'priority' => 'Important - Sensibilisation requise'],
                    ['id' => 'sauvegarde', 'label' => '💾 Procédure de sauvegarde - MOYEN', 'priority' => 'Recommandé - Continuité d\'activité']
                ],
                'type' => 'select_document'
            ]);
        } catch (\Exception $e) {
            return $this->handleError($e);
        }
    }
}
