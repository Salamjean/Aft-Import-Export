<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $sujet ?? 'Notification' }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f8f9fa; padding: 20px; text-align: center; }
        .content { padding: 20px; background: white; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>AFT-IMPORT-EXPORT</h2>
        </div>
        
        <div class="content">
            <h3>{{ $sujet ?? 'Notification' }}</h3>
            
            <p>Bonjour {{ $name }},</p>
            
            <div style="margin: 20px 0;">
                {!! $contenu !!}
            </div>
            
            <p>Cordialement,<br>L'équipe AFT-IMPORT-EXPORT</p>
        </div>
        
        <div class="footer">
            <p>AFT-IMPORT-EXPORT<br>
            Email: contact@aft-import-export.com<br>
            Téléphone: +XX XX XX XX XX</p>
        </div>
    </div>
</body>
</html>