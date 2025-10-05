<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Document;
use App\Models\Assessment;
use Illuminate\Http\Response;

class LandingController extends Controller
{
    /**
     * Page d'accueil principale - Landing page
     */
    public function index()
    {
        // Statistiques pour la landing page
        $stats = $this->getLandingStats();

        // Témoignages clients (peut être en base plus tard)
        $testimonials = $this->getTestimonials();

        return view('landing.index', [
            'stats' => $stats,
            'testimonials' => $testimonials,
            'features' => $this->getFeatures()
        ]);
    }

    /**
     * Page de démonstration
     */
    public function demo()
    {
        return view('landing.demo', [
            'demo_steps' => $this->getDemoSteps()
        ]);
    }

    /**
     * Obtenir les statistiques pour la landing page
     */
    private function getLandingStats(): array
    {
        return [
            'documents_generated' => Document::count() ?: 2500, // Fallback si pas de données
            'users_count' => User::count() ?: 150,
            'assessments_completed' => Assessment::count() ?: 800,
            'avg_generation_time' => '2 min',
            'compliance_rate' => '100%',
            'satisfaction_rate' => '98%'
        ];
    }

    /**
     * Témoignages clients
     */
    private function getTestimonials(): array
    {
        return [
            [
                'name' => 'Thomas Martin',
                'position' => 'CEO',
                'company' => 'TechStart',
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150',
                'rating' => 5,
                'text' => 'En 5 minutes j\'avais ma PSSI complète. Fini les consultants à 500€/jour !'
            ],
            [
                'name' => 'Marie Dubois',
                'position' => 'DRH',
                'company' => 'Commerce Plus',
                'avatar' => 'https://images.unsplash.com/photo-1494790108755-2616c64e34e8?w=150',
                'rating' => 5,
                'text' => 'Parfait pour notre mise en conformité RGPD. Documents professionnels et adaptés.'
            ],
            [
                'name' => 'Pierre Leroy',
                'position' => 'CTO',
                'company' => 'Innovation Lab',
                'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150',
                'rating' => 5,
                'text' => 'L\'IA comprend vraiment notre secteur. Résultat bluffant de qualité !'
            ]
        ];
    }

    /**
     * Fonctionnalités principales
     */
    private function getFeatures(): array
    {
        return [
            [
                'icon' => 'fas fa-shield-alt',
                'title' => 'PSSI Complète',
                'description' => 'Politique de Sécurité personnalisée selon votre secteur et taille',
                'color' => 'blue'
            ],
            [
                'icon' => 'fas fa-users',
                'title' => 'Charte Utilisateur',
                'description' => 'Règles d\'usage IT claires pour sensibiliser vos équipes',
                'color' => 'green'
            ],
            [
                'icon' => 'fas fa-database',
                'title' => 'Procédure Sauvegarde',
                'description' => 'Stratégie de sauvegarde adaptée à vos données critiques',
                'color' => 'purple'
            ],
            [
                'icon' => 'fas fa-exclamation-triangle',
                'title' => 'Gestion Incidents',
                'description' => 'Plan de réponse aux incidents de sécurité structuré',
                'color' => 'orange'
            ],
            [
                'icon' => 'fas fa-graduation-cap',
                'title' => 'Plan Formation',
                'description' => 'Programme de sensibilisation cybersécurité pour vos équipes',
                'color' => 'indigo'
            ],
            [
                'icon' => 'fas fa-balance-scale',
                'title' => 'Conformité RGPD',
                'description' => 'Documents conformes aux réglementations françaises et européennes',
                'color' => 'red'
            ]
        ];
    }

    /**
     * Étapes de la démonstration
     */
    private function getDemoSteps(): array
    {
        return [
            [
                'step' => 1,
                'title' => 'Répondez aux questions',
                'description' => 'Notre IA vous pose 3-5 questions sur votre entreprise',
                'icon' => 'fas fa-question-circle',
                'color' => 'blue',
                'duration' => '30 secondes'
            ],
            [
                'step' => 2,
                'title' => 'L\'IA génère votre document',
                'description' => 'Document professionnel de 8-12 pages personnalisé',
                'icon' => 'fas fa-robot',
                'color' => 'green',
                'duration' => '60 secondes'
            ],
            [
                'step' => 3,
                'title' => 'Téléchargez et utilisez',
                'description' => 'Format Word professionnel prêt à imprimer',
                'icon' => 'fas fa-download',
                'color' => 'purple',
                'duration' => '10 secondes'
            ]
        ];
    }

    /**
     * API endpoint pour les statistiques (AJAX)
     */
    public function getStats(Request $request)
    {
        if ($request->ajax()) {
            return response()->json($this->getLandingStats());
        }

        return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Tracking des conversions (analytics simple)
     */
    public function trackConversion(Request $request)
    {
        $request->validate([
            'action' => 'required|string|in:cta_click,demo_view,assistant_start',
            'source' => 'nullable|string|max:50'
        ]);

        // Log simple pour analytics
        \Log::info('Landing conversion', [
            'action' => $request->action,
            'source' => $request->source,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()
        ]);

        return response()->json(['success' => true]);
    }
}
