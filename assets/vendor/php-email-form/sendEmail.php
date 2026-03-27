<?php

require_once '/var/www/html/vendor/autoload.php';

use Mailgun\Mailgun;

function sendEmail($fromEmail, $fromName, $destino, $asunto, $mensajeHTML)
{
    // 🔐 USA EXACTAMENTE LA MISMA KEY DEL CURL (SIN key-)
    $apiKey = '8cb25230844bd05428ed12cc4abe77eb-c50aa110-c193edcc';
    $domain = 'mg.opticagrisol.com';

    if (!filter_var($destino, FILTER_VALIDATE_EMAIL)) {
        return "Error: correo destino inválido";
    }

    try {
        $mg = Mailgun::create($apiKey);

        $result = $mg->messages()->send($domain, [
            // ⚠️ FORZAMOS FROM CORRECTO DEL DOMINIO
            'from' => "Optica Grisol <no-responder@$domain>",
            'to' => $destino,
            'subject' => $asunto,
            'html' => $mensajeHTML
        ]);

        return true;

    } catch (Exception $e) {
        return "Mailgun Exception: " . $e->getMessage();
    }
}