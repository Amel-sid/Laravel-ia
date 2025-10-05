<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    LandingController,
    AssistantController,
    AuthController,
    AssessmentController,
    AIChatController,
    HomeController
};

/**
 * Calcule le score de maturité sécurité d'un utilisateur
 */
function calculateSecurityMaturityScore($user) {
    $score = 20; // Score de base
    $level = 'Débutant';
    
    // Points selon les documents créés
    $documentsCount = $user->documents()->count();
    $score += min($documentsCount * 15, 45); // Max 45 points pour les documents
    
    // Points selon les types de documents
    $documentTypes = $user->documents()->distinct('type')->count();
    $score += $documentTypes * 10; // 10 points par type différent
    
    // Points selon l'ancienneté
    $daysSinceCreation = $user->created_at->diffInDays(now());
    if ($daysSinceCreation > 30) $score += 10;
    if ($daysSinceCreation > 90) $score += 5;
    
    // Points selon l'activité récente
    $recentDocs = $user->documents()->where('created_at', '>=', now()->subWeek())->count();
    if ($recentDocs > 0) $score += 10;
    
    // Déterminer le niveau
    if ($score >= 80) $level = 'Expert';
    elseif ($score >= 60) $level = 'Avancé';
    elseif ($score >= 40) $level = 'Intermédiaire';
    
    return [
        'score' => min($score, 100), // Plafonné à 100
        'level' => $level,
        'color' => $score >= 80 ? 'green' : ($score >= 60 ? 'blue' : ($score >= 40 ? 'yellow' : 'red'))
    ];
}

/**
 * Calcule le statut de conformité d'un utilisateur
 */
function calculateComplianceStatus($user) {
    $status = 'Non conforme';
    $percentage = 0;
    $color = 'red';
    $requirements = [];
    
    // Documents requis pour une conformité de base
    $requiredDocs = ['pssi', 'charte', 'sauvegarde'];
    $userDocTypes = $user->documents()->distinct('type')->pluck('type')->toArray();
    
    // Calcul basé sur les documents existants
    $completedRequirements = array_intersect($requiredDocs, $userDocTypes);
    $percentage = (count($completedRequirements) / count($requiredDocs)) * 100;
    
    // Définir les exigences avec leur statut
    $requirements = [
        'pssi' => [
            'name' => 'PSSI (Politique de Sécurité)',
            'completed' => in_array('pssi', $userDocTypes),
            'description' => 'Document définissant la politique de sécurité'
        ],
        'charte' => [
            'name' => 'Charte Utilisateur',
            'completed' => in_array('charte', $userDocTypes),
            'description' => 'Règles d\'usage pour les utilisateurs'
        ],
        'sauvegarde' => [
            'name' => 'Procédure de Sauvegarde',
            'completed' => in_array('sauvegarde', $userDocTypes),
            'description' => 'Plan de sauvegarde et restauration'
        ]
    ];
    
    // Bonus pour la récence des documents (documents récents = conformité maintenue)
    $recentDocs = $user->documents()->where('created_at', '>=', now()->subMonths(6))->count();
    if ($recentDocs >= 2) $percentage += 10; // Bonus récence
    
    // Déterminer le statut
    if ($percentage >= 90) {
        $status = 'Conforme';
        $color = 'green';
    } elseif ($percentage >= 70) {
        $status = 'Partiellement conforme';
        $color = 'yellow';
    } elseif ($percentage >= 40) {
        $status = 'En cours de conformité';
        $color = 'blue';
    }
    
    return [
        'status' => $status,
        'percentage' => min($percentage, 100),
        'color' => $color,
        'requirements' => $requirements,
        'missing_count' => count($requiredDocs) - count($completedRequirements)
    ];
}

/**
 * Calcule les alertes et actions urgentes pour un utilisateur
 */
function calculateUrgentAlerts($user) {
    $alerts = [];
    $urgentActions = [];
    
    // 1. Vérifier les documents manquants (critique)
    $requiredDocs = ['pssi', 'charte', 'sauvegarde'];
    $userDocTypes = $user->documents()->distinct('type')->pluck('type')->toArray();
    $missingDocs = array_diff($requiredDocs, $userDocTypes);
    
    if (count($missingDocs) > 0) {
        $docNames = [
            'pssi' => 'PSSI (Politique de Sécurité)',
            'charte' => 'Charte Utilisateur', 
            'sauvegarde' => 'Procédure de Sauvegarde'
        ];
        
        foreach ($missingDocs as $docType) {
            $alerts[] = [
                'type' => 'critical',
                'icon' => 'exclamation-triangle',
                'title' => 'Document manquant',
                'message' => "Le document '{$docNames[$docType]}' est requis pour la conformité",
                'action' => 'Créer le document',
                'action_url' => route('assistant.start'),
                'created_at' => now()
            ];
        }
    }
    
    // 2. Documents obsolètes (plus de 1 an)
    $oldDocs = $user->documents()->where('created_at', '<=', now()->subYear())->get();
    if ($oldDocs->count() > 0) {
        $alerts[] = [
            'type' => 'warning',
            'icon' => 'clock',
            'title' => 'Documents obsolètes',
            'message' => "{$oldDocs->count()} document(s) datent de plus d'un an et doivent être mis à jour",
            'action' => 'Réviser les documents',
            'action_url' => route('assistant.start'),
            'created_at' => now()
        ];
    }
    
    // 3. Compte récent sans activité (nouveau utilisateur)
    $daysSinceCreation = $user->created_at->diffInDays(now());
    if ($daysSinceCreation <= 7 && $user->documents()->count() === 0) {
        $alerts[] = [
            'type' => 'info',
            'icon' => 'light-bulb',
            'title' => 'Bienvenue !',
            'message' => 'Commencez par créer votre premier document de cybersécurité',
            'action' => 'Créer un document',
            'action_url' => route('assistant.start'),
            'created_at' => now()
        ];
    }
    
    // 4. Score de maturité faible
    $securityScore = calculateSecurityMaturityScore($user);
    if ($securityScore['score'] < 50) {
        $alerts[] = [
            'type' => 'warning',
            'icon' => 'shield-exclamation',
            'title' => 'Score de sécurité faible',
            'message' => "Votre score de maturité ({$securityScore['score']}%) peut être amélioré",
            'action' => 'Améliorer le score',
            'action_url' => route('assistant.start'),
            'created_at' => now()
        ];
    }
    
    // 5. Activité récente positive (encouragement)
    $recentDocs = $user->documents()->where('created_at', '>=', now()->subWeek())->count();
    if ($recentDocs >= 2) {
        $alerts[] = [
            'type' => 'success',
            'icon' => 'check-circle',
            'title' => 'Excellente activité !',
            'message' => "Vous avez créé {$recentDocs} document(s) cette semaine. Continuez !",
            'action' => 'Voir mes documents',
            'action_url' => '#documents-recents',
            'created_at' => now()
        ];
    }
    
    // Actions urgentes basées sur le niveau de conformité
    $compliance = calculateComplianceStatus($user);
    if ($compliance['percentage'] < 70) {
        $urgentActions[] = [
            'priority' => 'high',
            'title' => 'Améliorer la conformité',
            'description' => 'Votre niveau de conformité est insuffisant',
            'progress' => $compliance['percentage'],
            'action' => 'Compléter les documents',
            'action_url' => route('assistant.start'),
            'deadline' => now()->addDays(7)->format('d/m/Y')
        ];
    }
    
    // Trier les alertes par priorité (critical, warning, info, success)
    $priorityOrder = ['critical' => 1, 'warning' => 2, 'info' => 3, 'success' => 4];
    usort($alerts, function($a, $b) use ($priorityOrder) {
        return $priorityOrder[$a['type']] - $priorityOrder[$b['type']];
    });
    
    return [
        'alerts' => collect($alerts)->take(5), // Limiter à 5 alertes max
        'urgent_actions' => collect($urgentActions),
        'has_critical' => collect($alerts)->where('type', 'critical')->count() > 0,
        'total_count' => count($alerts) + count($urgentActions)
    ];
}

/*
|--------------------------------------------------------------------------
| 🆓 ROUTES PUBLIQUES - FREEMIUM GRATUIT
|--------------------------------------------------------------------------
*/

// Landing page
Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/demo', [LandingController::class, 'demo'])->name('demo');

// Assistant IA - GÉNÉRATION GRATUITE
Route::prefix('assistant')->name('assistant.')->group(function () {
    // ✅ GRATUIT - Accessible à tous
    Route::get('/start', [AssistantController::class, 'start'])->name('start');
    Route::post('/session', [AssistantController::class, 'createSession'])->name('session');
    Route::post('/message', [AssistantController::class, 'processMessage'])->name('message');
    Route::get('/preview/{token}', [AssistantController::class, 'preview'])->name('preview');
});

// Authentification - PUBLIQUE (pour que les anonymes puissent s'inscrire)
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/save-document', [AuthController::class, 'showSaveForm'])->name('save.form');
    Route::post('/magic-link', [AuthController::class, 'sendMagicLink'])->name('magic.send');
    Route::get('/magic/{token}', [AuthController::class, 'loginViaMagicLink'])->name('magic.login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login-custom', [AuthController::class, 'login'])->name('login.custom');
});

/*
|--------------------------------------------------------------------------
| 🔐 ROUTES PREMIUM - INSCRIPTION REQUISE
|--------------------------------------------------------------------------
*/
// Test dashboard temporaire sans auth
Route::get('/dashboard-test', function() {
    $user = App\Models\User::where('email', 'test@test.com')->first();

    // Calcul du score de maturité sécurité
    $securityScore = calculateSecurityMaturityScore($user);
    
    // Calcul du statut de conformité
    $complianceStatus = calculateComplianceStatus($user);
    
    // Calcul des alertes urgentes
    $urgentAlerts = calculateUrgentAlerts($user);

    $stats = [
        'total_documents' => $user->documents()->count(),
        'recent_documents' => $user->documents()->where('created_at', '>=', now()->subWeek())->count(),
        'monthly_documents' => $user->documents()->whereMonth('created_at', now()->month)->count(),
        'favorite_type' => $user->documents()->select('type')->groupBy('type')->orderByRaw('COUNT(*) DESC')->first()?->type,
        'security_score' => $securityScore,
        'compliance_status' => $complianceStatus,
        'urgent_alerts' => $urgentAlerts,
    ];

    $documents = $user->documents()->latest()->take(5)->get();

    return view('dashboard', compact('user', 'stats', 'documents'));
})->name('dashboard-test');

Route::get('/dashboard', function() {
    $user = auth()->user();

    // Calcul du score de maturité sécurité
    $securityScore = calculateSecurityMaturityScore($user);
    
    // Calcul du statut de conformité
    $complianceStatus = calculateComplianceStatus($user);
    
    // Calcul des alertes urgentes
    $urgentAlerts = calculateUrgentAlerts($user);

    $stats = [
        'total_documents' => $user->documents()->count(),
        'recent_documents' => $user->documents()->where('created_at', '>=', now()->subWeek())->count(),
        'monthly_documents' => $user->documents()->whereMonth('created_at', now()->month)->count(),
        'favorite_type' => $user->documents()->select('type')->groupBy('type')->orderByRaw('COUNT(*) DESC')->first()?->type,
        'security_score' => $securityScore,
        'compliance_status' => $complianceStatus,
        'urgent_alerts' => $urgentAlerts,
    ];

    $documents = $user->documents()->latest()->take(5)->get();

    return view('dashboard', compact('user', 'stats', 'documents'));
})->middleware('auth')->name('dashboard');
Route::middleware('auth')->group(function () {

    // Dashboard principal


    // Déconnexion (seuls les connectés peuvent se déconnecter)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // 📥 TÉLÉCHARGEMENTS PREMIUM (Word/PDF)
    Route::get('/download/{token}/word', [AssistantController::class, 'downloadWord'])->name('download.word');
    Route::get('/download/{token}/pdf', [AssistantController::class, 'downloadPdf'])->name('download.pdf');

    // 💾 SAUVEGARDE ET GESTION DES DOCUMENTS
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', function() {
            $documents = auth()->user()->documents()->latest()->paginate(10);
            return view('documents.index', compact('documents'));
        })->name('index');

        Route::get('/{document}', function($id) {
            $document = auth()->user()->documents()->findOrFail($id);
            return view('documents.show', compact('document'));
        })->name('show');

        Route::delete('/{document}', function($id) {
            $document = auth()->user()->documents()->findOrFail($id);
            $document->delete();
            return redirect()->route('documents.index')->with('success', 'Document supprimé');
        })->name('destroy');

        // Télécharger depuis le dashboard
        Route::get('/{document}/download', function($id) {
            $document = auth()->user()->documents()->findOrFail($id);
            // Logique de téléchargement depuis le dashboard
            return response()->download($document->file_path ?? '');
        })->name('download');
    });

    // 🔍 DIAGNOSTIC PREMIUM
    Route::prefix('diagnostic')->name('diagnostic.')->group(function () {
        Route::get('/', [AssessmentController::class, 'start'])->name('start');
        Route::post('/', [AssessmentController::class, 'store'])->name('store');
        Route::get('/{assessment}/results', [AssessmentController::class, 'results'])->name('results');
        Route::get('/{assessment}/export', [AssessmentController::class, 'export'])->name('export');
    });

    // 🤖 AI CHAT PREMIUM
    Route::prefix('ai-chat')->name('ai.')->group(function () {
        Route::get('/', fn() => view('ai-chat.index'))->name('index');
        Route::post('/start-direct', [AIChatController::class, 'startDirect'])->name('start.direct');
        Route::post('/start-diagnostic/{assessment}', [AIChatController::class, 'startFromDiagnostic'])->name('start.diagnostic');
        Route::post('/message', [AIChatController::class, 'processMessage'])->name('message');
    });

    // 👤 PROFIL UTILISATEUR
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', function() {
            return view('profile.edit', ['user' => auth()->user()]);
        })->name('edit');
        Route::patch('/', [AuthController::class, 'updateProfile'])->name('update');
        Route::delete('/', [AuthController::class, 'deleteAccount'])->name('destroy');
    });
});

/*
|--------------------------------------------------------------------------
| 🔗 API ET UTILITAIRES
|--------------------------------------------------------------------------
*/

// API publique pour analytics
Route::post('/api/stats', [LandingController::class, 'getStats'])->name('api.stats');
Route::post('/api/track-conversion', [LandingController::class, 'trackConversion'])->name('api.track_conversion');

// Routes Laravel par défaut (si nécessaire)
require __DIR__.'/auth.php';
