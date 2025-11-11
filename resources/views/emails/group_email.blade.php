<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $sujet ?? 'Notification' }}</title>
</head>
<body>
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h2 style="color: #333;">{{ $sujet ?? 'Notification' }}</h2>
        
        <div style="margin: 20px 0;">
            {!! $contenu !!}
        </div>
        
        <hr style="border: none; border-top: 1px solid #eee;">
        
        <footer style="color: #666; font-size: 12px;">
            <p>Cordialement,<br>AFT-IMPORT-EXPORT</p>
        </footer>
    </div>
</body>
</html>