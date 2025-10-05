<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Assessment;
use App\Models\Answer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssessmentController extends Controller
{
    public function start()
    {
        $questions = Question::orderBy('order')->get();
        return view('diagnostic.start', compact('questions'));
    }

    public function store(Request $request)
    {
        // Validation des données
        $request->validate([
            'answers' => 'required|array',
            'scores' => 'required|array',
            'total_score' => 'required|integer',
            'company_name' => 'required|string|max:255',
            'sector' => 'nullable|string',
            'employees_count' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Créer l'assessment
            $assessment = Assessment::create([
                'user_id' => auth()->id(),
                'company_name' => $request->company_name,
                'sector' => $request->sector,
                'employees_count' => $request->employees_count,
                'scores' => $request->scores,
                'total_score' => $request->total_score,
                'status' => 'completed'
            ]);

            // Sauvegarder les réponses individuelles
            foreach ($request->answers as $questionId => $answerData) {
                Answer::create([
                    'assessment_id' => $assessment->id,
                    'question_id' => $questionId,
                    'selected_option' => $answerData['option'],
                    'points_earned' => $answerData['points']
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'redirect' => route('diagnostic.results', $assessment),
                'message' => 'Diagnostic enregistré avec succès'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'enregistrement du diagnostic: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement: ' . $e->getMessage()
            ], 500);
        }
    }

    public function results(Assessment $assessment)
    {
        // Vérifier que l'utilisateur peut accéder à ce diagnostic
        if ($assessment->user_id !== auth()->id()) {
            abort(403, 'Accès non autorisé');
        }

        // Charger les relations nécessaires
        $assessment->load(['answers', 'answers.question']);

        // Calculer les statistiques par domaine
        $domainStats = $this->calculateDomainStats($assessment);

        // Générer les recommandations
        $recommendations = $this->generateRecommendations($assessment);

        return view('diagnostic.results', compact('assessment', 'domainStats', 'recommendations'));
    }

    private function calculateDomainStats($assessment)
    {
        $domains = config('diagnostic.domains');
        $stats = [];

        foreach ($domains as $domainKey => $domainConfig) {
            // Filtrer les réponses par domaine
            $domainAnswers = $assessment->answers->filter(function ($answer) use ($domainKey) {
                return $answer->question->domain === $domainKey;
            });

            $totalPoints = $domainAnswers->sum('points_earned');
            $maxPoints = $domainAnswers->sum(function ($answer) {
                return $answer->question->max_points;
            });

            $percentage = $maxPoints > 0 ? round(($totalPoints / $maxPoints) * 100) : 0;

            $stats[$domainKey] = [
                'label' => $domainConfig['label'],
                'color' => $domainConfig['color'],
                'score' => $totalPoints,
                'max_score' => $maxPoints,
                'percentage' => $percentage,
                'level' => $this->getMaturityLevel($percentage),
                'questions_count' => $domainAnswers->count()
            ];
        }

        return $stats;
    }

    private function getMaturityLevel($percentage)
    {
        if ($percentage >= 80) return 'Excellent';
        if ($percentage >= 60) return 'Bon';
        if ($percentage >= 40) return 'Moyen';
        if ($percentage >= 20) return 'Faible';
        return 'Critique';
    }

    private function generateRecommendations($assessment)
    {
        $recommendations = [];
        $domainStats = $this->calculateDomainStats($assessment);

        foreach ($domainStats as $domain => $stats) {
            $recommendations[$domain] = $this->getDomainRecommendations($domain, $stats['percentage']);
        }

        return $recommendations;
    }

    private function getDomainRecommendations($domain, $percentage)
    {
        $recommendations = [
            'gouvernance' => [
                'critical' => [
                    'Établir une politique de sécurité de l\'information',
                    'Nommer un responsable sécurité (RSSI)',
                    'Sensibiliser les équipes aux risques cyber'
                ],
                'low' => [
                    'Formaliser les procédures de sécurité',
                    'Mettre en place un comité sécurité',
                    'Effectuer une analyse de risques annuelle'
                ],
                'medium' => [
                    'Certifier ISO 27001',
                    'Automatiser le suivi de conformité',
                    'Former régulièrement les équipes'
                ],
                'good' => [
                    'Optimiser les processus existants',
                    'Intégrer la sécurité dans les projets',
                    'Améliorer la culture sécurité'
                ]
            ],
            'access' => [
                'critical' => [
                    'Implémenter l\'authentification forte (2FA)',
                    'Réviser tous les accès utilisateurs',
                    'Créer une politique de mots de passe'
                ],
                'low' => [
                    'Automatiser la gestion des identités (IAM)',
                    'Segmenter les accès par rôles',
                    'Auditer régulièrement les permissions'
                ],
                'medium' => [
                    'Déployer la gestion privilegiée (PAM)',
                    'Monitorer les accès en temps réel',
                    'Intégrer SSO entreprise'
                ],
                'good' => [
                    'Optimiser l\'expérience utilisateur',
                    'Implémenter Zero Trust',
                    'Automatiser la déprovisioning'
                ]
            ],
            'protection' => [
                'critical' => [
                    'Installer un antivirus sur tous les postes',
                    'Mettre à jour tous les systèmes',
                    'Configurer un firewall'
                ],
                'low' => [
                    'Déployer un EDR/XDR',
                    'Chiffrer les données sensibles',
                    'Sécuriser les communications'
                ],
                'medium' => [
                    'Implémenter un SOC/SIEM',
                    'Automatiser la détection de menaces',
                    'Protéger contre les ransomwares'
                ],
                'good' => [
                    'Optimiser la détection IA',
                    'Intégrer Threat Intelligence',
                    'Automatiser la réponse aux incidents'
                ]
            ],
            'continuity' => [
                'critical' => [
                    'Créer un plan de sauvegarde',
                    'Tester la restauration des données',
                    'Définir un plan de continuité d\'activité'
                ],
                'low' => [
                    'Automatiser les sauvegardes',
                    'Former les équipes aux procédures',
                    'Créer un plan de gestion de crise'
                ],
                'medium' => [
                    'Implémenter un site de secours',
                    'Automatiser le basculement',
                    'Tester régulièrement le PCA'
                ],
                'good' => [
                    'Optimiser les RTO/RPO',
                    'Intégrer la cyber-résilience',
                    'Améliorer la communication de crise'
                ]
            ]
        ];

        $level = 'critical';
        if ($percentage >= 80) $level = 'good';
        elseif ($percentage >= 60) $level = 'medium';
        elseif ($percentage >= 40) $level = 'low';

        return $recommendations[$domain][$level] ?? [];
    }

    public function export(Assessment $assessment)
    {
        // Vérifier l'accès
        if ($assessment->user_id !== auth()->id()) {
            abort(403, 'Accès non autorisé');
        }

        // TODO: Implémenter l'export PDF avec TCPDF ou DomPDF
        // Pour l'instant, redirection vers les résultats
        return redirect()->route('diagnostic.results', $assessment)
            ->with('info', 'Export PDF bientôt disponible');
    }
}
