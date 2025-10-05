<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqService
{
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key');

        if (empty($this->apiKey)) {
            throw new \Exception('GROQ_API_KEY non configurée dans le fichier .env');
        }
    }

    public function chat(array $messages, string $model = 'llama-3.1-8b-instant')
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.3,
                'max_tokens' => 8000
            ]);

            if (!$response->successful()) {
                $errorBody = $response->body();
                Log::error('Erreur Groq API', [
                    'status' => $response->status(),
                    'body' => $errorBody,
                    'headers' => $response->headers()
                ]);
                throw new \Exception('Erreur API Groq: ' . $response->status() . ' - ' . $errorBody);
            }

            return $response->json('choices.0.message.content');

        } catch (\Exception $e) {
            Log::error('GroqService chat error: ' . $e->getMessage());
            throw $e;
        }
    }

    // ✅ CETTE MÉTHODE ÉTAIT MANQUANTE !
    public function generateDocument(string $documentType, array $answers)
    {
        try {
            $prompt = $this->buildPrompt($documentType, $answers);

            $content = $this->chat([
                [
                    'role' => 'system',
                    'content' => 'Tu es un expert en cybersécurité spécialisé dans la rédaction de documents pour PME françaises. Tu rédiges en français professionnel avec format Markdown. Génère des documents complets de 8-12 pages avec des sections détaillées et un plan d\'action concret.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ]);

            return [
                'success' => true,
                'content' => $content,
                'provider' => 'Groq AI (Llama 3)',
                'generated_at' => now()
            ];

        } catch (\Exception $e) {
            Log::error('Document generation failed', [
                'document_type' => $documentType,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Impossible de générer le document: ' . $e->getMessage()
            ];
        }
    }

    private function buildPrompt(string $documentType, array $answers): string
    {
        $prompt = "MISSION : Génère un document professionnel de cybersécurité pour une PME française.\n\n";
        $prompt .= "TYPE DE DOCUMENT : " . strtoupper($documentType) . "\n\n";

        $prompt .= "INFORMATIONS SUR L'ENTREPRISE :\n";
        foreach ($answers as $key => $value) {
            $labels = [
                'sector' => 'Secteur d\'activité',
                'size' => 'Nombre d\'employés',
                'data_sensitivity' => 'Type de données',
                'compliance' => 'Obligations réglementaires',
                'it_maturity' => 'Niveau IT',
                'remote_work' => 'Travail à distance',
                'personal_devices' => 'Équipements personnels',
                'main_tools' => 'Outils principaux',
                'internet_usage' => 'Usage internet',
                'data_types' => 'Types de données',
                'current_backup' => 'Sauvegardes actuelles',
                'rto_rpo' => 'Délai de récupération'
            ];
            $prompt .= "- " . ($labels[$key] ?? ucfirst($key)) . " : {$value}\n";
        }

        $prompt .= "\nEXIGENCES OBLIGATOIRES :\n";
        $prompt .= "- Document complet de 8-12 pages une fois imprimé\n";
        $prompt .= "- Format Markdown professionnel avec titres ## pour les sections principales\n";
        $prompt .= "- Contenu adapté au secteur, à la taille et au niveau de maturité IT\n";
        $prompt .= "- Sections [À COMPLÉTER] pour les informations sensibles/spécifiques\n";
        $prompt .= "- Plan d'action concret avec échéances\n";
        $prompt .= "- Conformité RGPD et réglementations françaises\n";
        $prompt .= "- Ton professionnel mais accessible\n\n";

        // Structure spécifique selon le type de document
        if ($documentType === 'pssi') {
            $prompt .= $this->getPSSIStructure();
        } elseif ($documentType === 'charte') {
            $prompt .= $this->getCharteStructure();
        } elseif ($documentType === 'sauvegarde') {
            $prompt .= $this->getSauvegardeStructure();
        }

        $prompt .= "\nGénère maintenant le document complet avec tous les détails nécessaires :";

        return $prompt;
    }

    private function getPSSIStructure(): string
    {
        return "STRUCTURE PSSI OBLIGATOIRE :\n" .
            "## 1. Contexte et objectifs de sécurité\n" .
            "## 2. Organisation de la sécurité informatique\n" .
            "## 3. Gestion des accès et des identités\n" .
            "## 4. Protection des données et des systèmes\n" .
            "## 5. Gestion des incidents de sécurité\n" .
            "## 6. Continuité d'activité et plan de reprise\n" .
            "## 7. Sensibilisation et formation\n" .
            "## 8. Plan d'action et mise en œuvre\n\n";
    }

    private function getCharteStructure(): string
    {
        return "STRUCTURE CHARTE UTILISATEUR OBLIGATOIRE :\n" .
            "## 1. Objet et champ d'application\n" .
            "## 2. Règles générales d'utilisation\n" .
            "## 3. Gestion des accès et mots de passe\n" .
            "## 4. Usage d'internet et messagerie\n" .
            "## 5. Protection des données personnelles\n" .
            "## 6. Équipements et outils informatiques\n" .
            "## 7. Télétravail et mobilité\n" .
            "## 8. Sanctions et mise en application\n\n";
    }

    private function getSauvegardeStructure(): string
    {
        return "STRUCTURE PROCÉDURE SAUVEGARDE OBLIGATOIRE :\n" .
            "## 1. Objectifs et périmètre\n" .
            "## 2. Identification des données critiques\n" .
            "## 3. Stratégie de sauvegarde\n" .
            "## 4. Procédures techniques\n" .
            "## 5. Tests et vérifications\n" .
            "## 6. Procédure de restauration\n" .
            "## 7. Responsabilités et organisation\n" .
            "## 8. Planification et suivi\n\n";
    }
}
