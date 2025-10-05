<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'assessment_id',
        'title',
        'type',
        'status',
        'content',
        'metadata',
        'version',
        'parent_document_id',
        'generated_at',
        'last_reviewed_at',
        'expires_at',
        'excerpt',
        'tags',
        'preview_token', // Pour les documents anonymes
    ];

    protected $casts = [
        'metadata' => 'json',
        'tags' => 'json',
        'generated_at' => 'datetime',
        'last_reviewed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * RELATIONS
     */

    /**
     * Un document appartient à un utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Un document peut être lié à une évaluation
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Document parent (pour le versioning)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'parent_document_id');
    }

    /**
     * Documents enfants (versions)
     */
    public function children()
    {
        return $this->hasMany(Document::class, 'parent_document_id');
    }

    /**
     * SCOPES
     */

    /**
     * Scope : Documents récents
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope : Documents par type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope : Documents avec statut
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope : Documents anonymes (sans user_id)
     */
    public function scopeAnonymous($query)
    {
        return $query->whereNull('user_id');
    }

    /**
     * ACCESSEURS
     */

    /**
     * Obtenir le libellé du type de document
     */
    public function getTypeLabel()
    {
        return match($this->type) {
            'pssi' => 'PSSI - Politique de Sécurité',
            'charte' => 'Charte Utilisateur',
            'sauvegarde' => 'Procédure de Sauvegarde',
            'incident' => 'Plan de Réponse aux Incidents',
            'audit' => 'Rapport d\'Audit',
            default => 'Document de Sécurité'
        };
    }

    /**
     * Obtenir le libellé du statut
     */
    public function getStatusLabel()
    {
        return match($this->status) {
            'draft' => 'Brouillon',
            'generated' => 'Généré',
            'reviewed' => 'Révisé',
            'approved' => 'Approuvé',
            'outdated' => 'Obsolète',
            default => 'Inconnu'
        };
    }

    /**
     * Calculer le nombre de mots
     */
    public function getWordCountAttribute()
    {
        return str_word_count(strip_tags($this->content));
    }

    /**
     * Estimer le nombre de pages
     */
    public function getEstimatedPagesAttribute()
    {
        return max(1, ceil($this->word_count / 250));
    }

    /**
     * Obtenir un extrait du contenu
     */
    public function getExcerptAttribute()
    {
        if ($this->attributes['excerpt']) {
            return $this->attributes['excerpt'];
        }

        return \Str::limit(strip_tags($this->content), 200);
    }

    /**
     * Vérifier si le document est expiré
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Vérifier si le document est récent
     */
    public function isRecent($days = 7): bool
    {
        return $this->created_at->isAfter(now()->subDays($days));
    }

    /**
     * MÉTHODES UTILITAIRES
     */

    /**
     * Marquer comme révisé
     */
    public function markAsReviewed()
    {
        $this->update([
            'status' => 'reviewed',
            'last_reviewed_at' => now()
        ]);
    }

    /**
     * Créer une nouvelle version
     */
    public function createNewVersion($newContent, $newMetadata = [])
    {
        $newVersion = $this->replicate();
        $newVersion->content = $newContent;
        $newVersion->metadata = array_merge($this->metadata ?? [], $newMetadata);
        $newVersion->parent_document_id = $this->id;
        $newVersion->version = $this->incrementVersion();
        $newVersion->generated_at = now();
        $newVersion->save();

        return $newVersion;
    }

    /**
     * Incrémenter la version
     */
    private function incrementVersion(): string
    {
        $parts = explode('.', $this->version);
        $parts[1] = ((int)$parts[1]) + 1;
        return implode('.', $parts);
    }

    /**
     * Associer à un utilisateur (pour les documents anonymes)
     */
    public function linkToUser(User $user)
    {
        $this->update([
            'user_id' => $user->id,
            'status' => 'generated'
        ]);
    }
}
