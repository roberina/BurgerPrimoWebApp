<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uus kontaktvorm</title>
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
            background-color: #D2691E;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .field {
            margin-bottom: 20px;
        }
        .field-label {
            font-weight: bold;
            color: #D2691E;
            margin-bottom: 5px;
        }
        .field-value {
            background-color: white;
            padding: 10px;
            border-left: 3px solid #D2691E;
            margin-top: 5px;
        }
        .message-box {
            background-color: white;
            padding: 15px;
            border-left: 3px solid #D2691E;
            margin-top: 5px;
            white-space: pre-wrap;
            min-height: 100px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Uus Kontaktvorm Sõnum</h1>
        <p>Burger Primo</p>
    </div>
    <div class="content">
        <div class="field">
            <div class="field-label">Nimi:</div>
            <div class="field-value">{{ $name ?? 'N/A' }}</div>
        </div>

        <div class="field">
            <div class="field-label">E-post:</div>
            <div class="field-value">
                <a href="mailto:{{ $email ?? '' }}">{{ $email ?? 'N/A' }}</a>
            </div>
        </div>

        @if(!empty($phone))
        <div class="field">
            <div class="field-label">Telefon:</div>
            <div class="field-value">
                <a href="tel:{{ $phone }}">{{ $phone }}</a>
            </div>
        </div>
        @endif

        <div class="field">
            <div class="field-label">Teema:</div>
            <div class="field-value">
                @php
                    $subjectLabels = [
                        'reservation' => 'Laua broneerimine',
                        'catering' => 'Catering teenus',
                        'feedback' => 'Tagasiside',
                        'other' => 'Muu',
                    ];
                    $subjectLabel = $subjectLabels[$subject ?? 'other'] ?? 'Muu';
                @endphp
                {{ $subjectLabel }}
            </div>
        </div>

        <div class="field">
            <div class="field-label">Sõnum:</div>
            <div class="message-box">{{ $userMessage ?? 'N/A' }}</div>
        </div>

        <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">
        
        <p style="font-size: 12px; color: #666; text-align: center;">
            See e-kiri saadeti automaatselt Burger Primo kontaktvormist.<br>
            Palun vastake otse kliendi e-posti aadressile.
        </p>
    </div>
</body>
</html>