<?php
// database/seeders/QuestionSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;

class QuestionSeeder extends Seeder
{
    public function run()
    {
        // Vérifier si on a déjà des questions, sinon créer
        if (Question::count() > 0) {
            $this->command->info('🔄 Questions déjà présentes, rien à faire');
            return;
        }

        $questions = [
            // QUESTION 1 - Gouvernance
            [
                'domain' => 'gouvernance',
                'code' => 'A1',
                'question' => 'Votre organisation dispose-t-elle d\'une politique de sécurité de l\'information formalisée et approuvée par la direction ?',
                'options' => [
                    ['text' => 'Aucune politique formalisée', 'points' => 0],
                    ['text' => 'Politique en cours de rédaction', 'points' => 2],
                    ['text' => 'Politique formalisée et approuvée', 'points' => 5],
                    ['text' => 'Politique diffusée, comprise et appliquée par tous', 'points' => 7]
                ],
                'max_points' => 7,
                'order' => 1
            ],

            // QUESTION 2 - Accès
            [
                'domain' => 'access',
                'code' => 'B1',
                'question' => 'L\'authentification multi-facteur (2FA/MFA) est-elle déployée ?',
                'options' => [
                    ['text' => 'Aucune authentification renforcée', 'points' => 0],
                    ['text' => 'MFA sur quelques comptes critiques', 'points' => 3],
                    ['text' => 'MFA sur tous les comptes administrateurs', 'points' => 6],
                    ['text' => 'MFA généralisée à tous les utilisateurs', 'points' => 8]
                ],
                'max_points' => 8,
                'order' => 2
            ]

            /*
            // 🔽 TOUTES LES AUTRES QUESTIONS COMMENTÉES 🔽
            // Décommentez quand vous voulez tester avec plus de questions

            // QUESTION 3 - Protection
            [
                'domain' => 'protection',
                'code' => 'C1',
                'question' => 'Les postes de travail sont-ils protégés par un antivirus/EDR ?',
                'options' => [
                    ['text' => 'Aucune protection', 'points' => 0],
                    ['text' => 'Antivirus basique', 'points' => 3],
                    ['text' => 'Solution EDR déployée', 'points' => 6],
                    ['text' => 'Solution XDR avec monitoring 24/7', 'points' => 8]
                ],
                'max_points' => 8,
                'order' => 3
            ],

            // QUESTION 4 - Continuité
            [
                'domain' => 'continuity',
                'code' => 'D1',
                'question' => 'Un plan de sauvegarde est-il en place et testé ?',
                'options' => [
                    ['text' => 'Pas de sauvegarde organisée', 'points' => 0],
                    ['text' => 'Sauvegardes ponctuelles non testées', 'points' => 2],
                    ['text' => 'Sauvegardes automatiques testées', 'points' => 6],
                    ['text' => 'Plan 3-2-1 avec tests de restauration', 'points' => 8]
                ],
                'max_points' => 8,
                'order' => 4
            ]
            */
        ];

        foreach ($questions as $questionData) {
            Question::create($questionData);
        }

        $this->command->info('✅ ' . count($questions) . ' questions créées pour les tests');
    }
}
