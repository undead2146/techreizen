<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Wachtwoord Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        .content {
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            text-align: center;
            color: #777;
        }
        .password {
            font-family: monospace;
            font-size: 18px;
            background-color: #f8f9fa;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
            display: inline-block;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>TechReizen</h2>
    </div>

    <div class="content">
        <h3>Wachtwoord Herstel</h3>
        
        <p>Beste {{ $user->login }},</p>
        
        @if(isset($resetUrl))
            <p>U heeft een verzoek ingediend om uw wachtwoord te herstellen. Klik op de onderstaande link om een nieuw wachtwoord in te stellen:</p>
            
            <p style="text-align: center; margin: 30px 0;">
                <a href="{{ $resetUrl }}" style="background-color: #0066cc; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                    Wachtwoord Herstellen
                </a>
            </p>
            
            <p>U kunt deze link ook kopiëren en in uw browser plakken:</p>
            <p style="word-break: break-all; background-color: #f8f9fa; padding: 10px; border: 1px solid #ddd; border-radius: 3px;">
                {{ $resetUrl }}
            </p>
            
            <p>Deze link is 60 minuten geldig.</p>
            
            <p>Als u geen wachtwoord herstel heeft aangevraagd, kunt u deze e-mail negeren en blijft uw huidige wachtwoord geldig.</p>
        @else
            <p>Uw wachtwoord voor TechReizen is gereset volgens uw verzoek. Hieronder vindt u uw nieuwe inloggegevens:</p>
            
            <p><strong>Gebruikersnaam:</strong> {{ $user->login }}</p>
            <p><strong>Nieuw wachtwoord:</strong> <span class="password">{{ $password }}</span></p>
            
            <p>U kunt inloggen met deze gegevens via <a href="{{ config('app.url') . route('login', [], false) }}">onze website</a>.</p>
            
            <p>Wij raden u aan om dit wachtwoord zo snel mogelijk te wijzigen na het inloggen.</p>
        @endif
        
        <p>Als u dit verzoek niet heeft gedaan, neem dan direct contact op met onze klantenservice.</p>
        
        <p>Met vriendelijke groet,<br>
        Het TechReizen Team</p>
    </div>

    <div class="footer">
        <p>Dit is een automatisch gegenereerd bericht. Gelieve niet te antwoorden op deze e-mail.</p>
        <p>&copy; {{ date('Y') }} TechReizen. Alle rechten voorbehouden.</p>
    </div>
</body>
</html>
