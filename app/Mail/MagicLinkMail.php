<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class MagicLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $token;
    public User $user;
    public ?string $sessionId;

    /**
     * Create a new message instance.
     */
    public function __construct(string $token, User $user, ?string $sessionId = null)
    {
        $this->token = $token;
        $this->user = $user;
        $this->sessionId = $sessionId;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('🔗 Votre lien de connexion Policify')
            ->view('emails.magic-link')
            ->with([
                'loginUrl' => route('auth.magic.login', $this->token),
                'userName' => $this->user->name,
                'userEmail' => $this->user->email,
                'hasDocument' => !empty($this->sessionId)
            ]);
    }
}
