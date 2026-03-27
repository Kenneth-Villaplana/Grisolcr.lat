<?php

require_once '/var/www/html/vendor/autoload.php';

use Mailgun\Mailgun;

function sendEmail($fromEmail, $fromName, $destino, $asunto, $mensajeHTML)
{
    $apiKey = trim('key-bb798807bd8f763b31511cd9b3b702a3-c50aa110-c84a9f1b'); 
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