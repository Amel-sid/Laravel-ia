<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        // Champs de base
        'name',
        'email',
        'password',

        // Champs Policify supplémentaires (optionnels pour l'instant)
        'company',
        'job_title',
        'company_size',
        'sector',
        'preferences',
        'timezone',
        'language',
        'documents_generated',
        'avg_satisfaction',
        'last_activity_at',
        'prefers_magic_link',
        'onboarding_completed',
        'onboarding_steps',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            // Casts Policify (optionnels)
            'preferences' => 'json',
            'onboarding_steps' => 'json',
            'last_activity_at' => 'datetime',
            'documents_generated' => 'integer',
            'avg_satisfaction' => 'decimal:2',
            'onboarding_completed' => 'boolean',
            'prefers_magic_link' => 'boolean',
        ];
    }

    /**
     * RELATIONS NÉCESSAIRES POUR LE DASHBOARD
     */

    /**
     * Relation : Un utilisateur a plusieurs documents
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Relation : Un utilisateur a plusieurs évaluations/diagnostics
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    /**
     * Relation : Un utilisateur a plusieurs sessions de chat
     */
    public function chatSessions(): HasMany
    {
        return $this->hasMany(ChatSession::class);
    }

    /**
     * SCOPES UTILES POUR LE DASHBOARD
     */

    /**
     * Scope : Documents récents (X jours)
     */
    public function scopeRecentDocuments($query, $days = 7)
    {
        return $this->documents()
            ->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * MÉTHODES HELPER POUR LES STATISTIQUES
     */

    /**
     * Obtenir le type de document le plus généré
     */
    public function getFavoriteDocumentTypeAttribute()
    {
        return $this->documents()
            ->select('type')
            ->groupBy('type')
            ->orderByRaw('COUNT(*) DESC')
            ->first()?->type;
    }

    /**
     * Obtenir le nombre de documents générés ce mois
     */
    public function getDocumentsThisMonthAttribute()
    {
        return $this->documents()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    /**
     * Obtenir le nombre de documents générés cette semaine
     */
    public function getDocumentsThisWeekAttribute()
    {
        return $this->documents()
            ->where('created_at', '>=', now()->startOfWeek())
            ->count();
    }

    /**
     * Vérifier si l'utilisateur a des documents
     */
    public function hasDocuments(): bool
    {
        return $this->documents()->exists();
    }

    /**
     * Mettre à jour la dernière activité
     */
    public function updateLastActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }
}
