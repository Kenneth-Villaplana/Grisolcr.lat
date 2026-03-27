<?php

require_once '/var/www/html/vendor/autoload.php';

use Mailgun\Mailgun;

function sendEmail($fromEmail, $fromName, $destino, $asunto, $mensajeHTML)
{
    $apiKey = trim('key-8cb25230844bd05428ed12cc4abe77eb-c50aa110-c193edcc'); 
    $domain = 'mg.opticagrisol.com';

    if (!filter_var($destino, FILTER_VALIDATE_EMAIL)) {
        return "Error: correo destino inválido";
    }

    try {
        $mg = Mailgun::create($apiKey);

        $result = $mg->messages()->send($domain, [
            'from' => "$fromName <$fromEmail>",
            'to' => $destino,
            'subject' => $asunto,
            'html' => $mensajeHTML
        ]);

        return true;

    } catch (Exception $e) {
        return "Mailgun Exception: " . $e->getMessage();
    }
}