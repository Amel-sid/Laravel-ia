<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash, Mail, Cache, Log};
use Illuminate\Support\Str;
use App\Models\User;
use App\Mail\MagicLinkMail;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Afficher le formulaire de sauvegarde du document
     */
    public function showSaveForm(Request $request)
    {
        $sessionId = $request->get('session');

        if (!$sessionId) {
            return redirect()->route('assistant.start')
                ->with('error', 'Session expirée. Veuillez recommencer.');
        }

        // Récupérer les informations du document depuis la session
        $documentData = Cache::get("assistant_session:{$sessionId}");

        return view('auth.save-document', [
            'session_id' => $sessionId,
            'document_data' => $documentData
        ]);
    }

    /**
     * Envoyer un Magic Link par email
     */
    public function sendMagicLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'session_id' => 'nullable|string|max:100'
        ]);

        try {
            $email = $request->email;
            $sessionId = $request->session_id;

            // ✅ CRÉER $user ICI (avant de l'utiliser)
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $this->extractNameFromEmail($email),
                    'email_verified_at' => now(),
                    'password' => Hash::make('') // ← Mot de passe vide haché
                ]
            );

            // Générer le token magic link
            $token = Str::random(60);

            // Stocker en cache
            Cache::put("magic_link:{$token}", [
                'user_id' => $user->id,  // ← Maintenant $user existe
                'email' => $email,
                'session_id' => $sessionId,
                'intended_action' => 'save_document',
                'created_at' => now()
            ], now()->addMinutes(15));

            // ❌ COMMENTER l'envoi email :
            // Mail::to($email)->send(new MagicLinkMail($token, $user, $sessionId));

            // ✅ MODE TEST :
            $magicLink = route('auth.magic.login', $token);

            Log::info('🔗 MAGIC LINK TEST', [
                'email' => $email,
                'user_id' => $user->id,  // ← Maintenant $user existe
                'link' => $magicLink
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Email envoyé ! 📧',
                'magic_link' => $magicLink
            ]);

        } catch (\Exception $e) {
            Log::error('Magic link error', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage()
            ], 500);
        }
    }    /**
     * Connexion via Magic Link
     */
    public function loginViaMagicLink(Request $request, string $token)
    {
        try {
            $data = Cache::get("magic_link:{$token}");

            if (!$data) {
                return redirect()->route('home')
                    ->with('error', 'Lien expiré ou invalide. Veuillez demander un nouveau lien.');
            }

            $user = User::find($data['user_id']);

            if (!$user) {
                return redirect()->route('home')
                    ->with('error', 'Utilisateur introuvable.');
            }

            // Connecter l'utilisateur
            Auth::login($user);

            // Associer la session anonyme au compte
            if (!empty($data['session_id'])) {
                $this->linkAnonymousSession($user, $data['session_id']);
            }

            // Supprimer le token utilisé
            Cache::forget("magic_link:{$token}");

            Log::info('Magic link login successful', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return redirect()->route('dashboard')
                ->with('success', '🎉 Connexion réussie ! Votre document a été sauvegardé.');

        } catch (\Exception $e) {
            Log::error('Magic link login error', [
                'token' => $token,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('home')
                ->with('error', 'Erreur lors de la connexion. Veuillez réessayer.');
        }
    }

    /**
     * Inscription classique
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|string|min:8|confirmed',
            'session_id' => 'nullable|string|max:100'
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now()
            ]);

            // Associer session anonyme si présente
            if ($request->session_id) {
                $this->linkAnonymousSession($user, $request->session_id);
            }

            Auth::login($user);

            Log::info('User registered', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Compte créé avec succès !',
                'redirect' => route('dashboard')
            ]);

        } catch (\Exception $e) {
            Log::error('Registration error', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du compte.'
            ], 500);
        }
    }

    /**
     * Connexion classique
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'session_id' => 'nullable|string|max:100'
        ]);

        try {
            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                // Associer session anonyme si présente
                if ($request->session_id) {
                    $this->linkAnonymousSession($user, $request->session_id);
                }

                Log::info('User logged in', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Connexion réussie !',
                    'redirect' => route('dashboard')
                ]);
            }

            throw ValidationException::withMessages([
                'email' => ['Les identifiants sont incorrects.']
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Identifiants incorrects.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Login error', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la connexion.'
            ], 500);
        }
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Vous avez été déconnecté.');
    }

    /**
     * Associer les données de session anonyme au compte utilisateur
     */
    private function linkAnonymousSession(User $user, string $sessionId): void
    {
        try {
            // Récupérer les données de la session anonyme
            $sessionData = Cache::get("assistant_session:{$sessionId}");

            if (!$sessionData) {
                Log::warning('Anonymous session not found', ['session_id' => $sessionId]);
                return;
            }

            // Si un document a été généré, le sauvegarder
            if (isset($sessionData['download_token'])) {
                $documentData = Cache::get("download_token:{$sessionData['download_token']}");

                if ($documentData) {
                    // Créer le document en base de données
                    $user->documents()->create([
                        'title' => $this->generateDocumentTitle($sessionData['document_type'], $user),
                        'type' => $sessionData['document_type'],
                        'content' => $documentData['content'],
                        'status' => 'generated',
                        'metadata' => [
                            'answers' => $sessionData['answers'] ?? [],
                            'generated_by' => 'groq',
                            'source' => 'anonymous_session',
                            'session_id' => $sessionId,
                            'generated_at' => now()
                        ]
                    ]);

                    Log::info('Anonymous document linked to user', [
                        'user_id' => $user->id,
                        'session_id' => $sessionId,
                        'document_type' => $sessionData['document_type']
                    ]);
                }
            }

            // Nettoyer les données temporaires
            Cache::forget("assistant_session:{$sessionId}");
            if (isset($sessionData['download_token'])) {
                Cache::forget("download_token:{$sessionData['download_token']}");
            }

        } catch (\Exception $e) {
            Log::error('Error linking anonymous session', [
                'user_id' => $user->id,
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Extraire un nom à partir de l'email
     */
    private function extractNameFromEmail(string $email): string
    {
        $localPart = explode('@', $email)[0];

        // Remplacer les caractères communs par des espaces
        $name = str_replace(['.', '_', '-', '+'], ' ', $localPart);

        // Capitaliser chaque mot
        return Str::title($name);
    }

    /**
     * Générer le titre du document
     */
    private function generateDocumentTitle(string $documentType, User $user): string
    {
        $company = $user->company ?? 'Entreprise';
        $date = now()->format('Y-m-d');

        return match($documentType) {
            'pssi' => "{$company} - PSSI - {$date}",
            'charte' => "{$company} - Charte Utilisateur - {$date}",
            'sauvegarde' => "{$company} - Procédure Sauvegarde - {$date}",
            default => "{$company} - Document Sécurité - {$date}"
        };
    }
}
