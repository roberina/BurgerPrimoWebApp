<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Täname ühenduse võtmise eest</title>
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
            padding: 30px 20px;
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
        .highlight {
            background-color: #fff;
            padding: 15px;
            border-left: 4px solid #D2691E;
            margin: 20px 0;
        }
        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Täname!</h1>
        <p>Burger Primo</p>
    </div>
    <div class="content">
        <p>Tere <?php echo e($name ?? 'Klient'); ?>,</p>
        
        <p>Täname, et võtsite meiega ühendust! Oleme saanud teie sõnumi ja vastame esimesel võimalusel.</p>
        
        <div class="highlight">
            <p><strong>Teie sõnumi kokkuvõte:</strong></p>
            <p><strong>Teema:</strong> 
                <?php
                    $subjectLabels = [
                        'reservation' => 'Laua broneerimine',
                        'catering' => 'Catering teenus',
                        'feedback' => 'Tagasiside',
                        'other' => 'Muu',
                    ];
                    $subjectLabel = $subjectLabels[$subject ?? 'other'] ?? 'Muu';
                ?>
                <?php echo e($subjectLabel); ?>

            </p>
            <p style="white-space: pre-wrap;"><?php echo e($userMessage ?? ''); ?></p>
        </div>

        <p>Kui teil on kiireloomulisi küsimusi, võite meile helistada:</p>
        <p style="text-align: center;">
            <strong>📞 +372 5743 8483</strong><br>
            <strong>📧 info@burgerprimo.ee</strong>
        </p>

        <p style="margin-top: 30px;">Parimate soovidega,<br>
        <strong>Burger Primo meeskond</strong></p>

        <div class="footer">
            <p>
                Kauba tn 5/2, Kuressaare, Saaremaa 93819<br>
                <a href="tel:+37257438483">+372 5743 8483</a> | 
                <a href="mailto:info@burgerprimo.ee">info@burgerprimo.ee</a>
            </p>
        </div>
    </div>
</body>
</html><?php /**PATH /home/kennu/BurgerPrimoWebApp/resources/views/emails/contact-confirmation.blade.php ENDPATH**/ ?>