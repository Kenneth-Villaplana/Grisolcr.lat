<?php

require_once '/var/www/html/vendor/autoload.php';

use Mailgun\Mailgun;

function cargarConfigMailJson(): array
{
    $ruta = '/var/www/html/config/mail.json';

    if (!file_exists($ruta)) {
        return [];
    }

    $contenido = file_get_contents($ruta);
    $config = json_decode($contenido, true);

    return is_array($config) ? $config : [];
}

function sendEmail($fromEmail, $fromName, $destino, $asunto, $mensajeHTML)
{
    $config = cargarConfigMailJson();

    $apiKey = $config['mailgun_api_key'] ?? '';
    $domain = $config['mailgun_domain'] ?? '';

    if (empty($apiKey) || empty($domain)) {
        return "Mailgun config missing";
    }

    if (!filter_var($destino, FILTER_VALIDATE_EMAIL)) {
        return "Invalid destination email";
    }

    // fallback remitente
    $fromEmail = $fromEmail ?: "no-responder@" . $domain;
    $fromName  = $fromName ?: "Óptica Grisol";

    try {

        $mg = Mailgun::create($apiKey);

        $mg->messages()->send($domain, [
            'from'    => "{$fromName} <{$fromEmail}>",
            'to'      => $destino,
            'subject' => $asunto,
            'html'    => $mensajeHTML
        ]);

        return true;

    } catch (Exception $e) {
        return "Mailgun Error: " . $e->getMessage();
    }
}