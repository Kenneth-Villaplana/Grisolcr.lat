<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Mailgun\Mailgun;

function sendEmail($fromEmail, $fromName, $destino, $asunto, $mensajeHTML)
{
    $apiKey = trim('key-ccc080423cf27d3449415151c6053c10-c50aa110-614e2a18'); 
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