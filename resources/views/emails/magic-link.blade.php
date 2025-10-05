<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Policify</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #374151;
            margin: 0;
            padding: 0;
            background-color: #f9fafb;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .content {
            padding: 40px 30px;
        }
        .btn {
            display: inline-block;
            background: #3b82f6;
            color: white;
            padding: 16px 32px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
        }
        .btn:hover {
            background: #2563eb;
        }
        .footer {
            background: #f3f4f6;
            padding: 20px 30px;
            text-align: center;
            font-size: 14px;
            color: #6b7280;
        }
        .highlight {
            background: #fef3c7;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #f59e0b;
            margin: 20px 0;
        }
        .document-info {
            background: #dbeafe;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Header -->
    <div class="header">
        <h1 style="margin: 0; font-size: 28px;">🛡️ Policify</h1>
        <p style="margin: 10px 0 0 0; opacity: 0.9; font-size: 18px;">Votre lien de connexion</p>
    </div>

    <!-- Content -->
    <div class="content">
        <h2 style="color: #1f2937; margin-bottom: 20px;">
            Bonjour {{ $userName }} !
        </h2>

        <p>Vous avez demandé un lien de connexion pour accéder à votre compte Policify.</p>

        @if($hasDocument)
            <div class="document-info">
                <p style="margin: 0; font-weight: 600; color: #1e40af;">
                    📄 Votre document de cybersécurité est prêt !
                </p>
                <p style="margin: 10px 0 0 0; font-size: 14px; color: #3730a3;">
                    En vous connectant, vous pourrez le retrouver dans votre dashboard et en créer d'autres.
                </p>
            </div>
        @endif

        <p style="text-align: center; margin: 30px 0;">
            <a href="{{ $loginUrl }}" class="btn">
                🚀 Se connecter automatiquement
            </a>
        </p>

        <div class="highlight">
            <p style="margin: 0; font-weight: 600; color: #92400e;">
                ⏰ Ce lien expire dans 15 minutes
            </p>
            <p style="margin: 10px 0 0 0; font-size: 14px; color: #a16207;">
                Si vous n'avez pas demandé cette connexion, ignorez cet email.
            </p>
        </div>

        <p>Si le bouton ne fonctionne pas, copiez et collez ce lien dans votre navigateur :</p>
        <p style="background: #f3f4f6; padding: 10px; border-radius: 4px; font-family: monospace; word-break: break-all; font-size: 14px;">
            {{ $loginUrl }}
        </p>

        <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">

        <h3 style="color: #1f2937;">🎯 Pourquoi créer un compte Policify ?</h3>
        <ul style="color: #4b5563;">
            <li>📄 <strong>Sauvegarde automatique</strong> de tous vos documents</li>
            <li>📊 <strong>Dashboard personnalisé</strong> avec vos statistiques</li>
            <li>🔄 <strong>Historique et versions</strong> de vos documents</li>
            <li>🆕 <strong>Nouveaux modèles</strong> ajoutés chaque mois</li>
            <li>💬 <strong>Support prioritaire</strong> par notre équipe</li>
        </ul>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p style="margin: 0;">
            Cet email a été envoyé par <strong>Policify</strong><br>
            Assistant IA spécialisé en cybersécurité pour PME
        </p>
        <p style="margin: 10px 0 0 0;">
            <a href="{{ route('home') }}" style="color: #3b82f6;">Retour au site</a> |
            <a href="mailto:support@policify.fr" style="color: #3b82f6;">Support</a>
        </p>
    </div>
</div>
</body>
</html>
