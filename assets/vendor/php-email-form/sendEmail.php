<?php

require_once '/var/www/html/vendor/autoload.php';

use Mailgun\Mailgun;

function cargarConfigMailJson(): array
{
    $ruta = __DIR__ . '/../config/mail.json'; // ajusta si tu ruta cambia

    if (!file_exists($ruta)) {
        return [];
    }

    $contenido = file_get_contents($ruta);
    $config = json_decode($contenido, true);

    return is_array($config) ? $config : [];
}

function sendEmail($fromEmail, $fromName, $destino, $asunto, $mensajeHTML)
{
    // 🔐 Cargar configuración
    $config = cargarConfigMailJson();

    $apiKey = $config['mailgun_api_key'] ?? '';
    $domain = $config['mailgun_domain'] ?? '';

    // 🚨 Validación config
    if (empty($apiKey) || empty($domain)) {
        return "Error: configuración de Mailgun incompleta";
    }

    // 🚨 Validación email
    if (!filter_var($destino, FILTER_VALIDATE_EMAIL)) {
        return "Error: correo destino inválido";
    }

    try {
        $mg = Mailgun::create($apiKey);

        $mg->messages()->send($domain, [
            // ⚠️ FROM siempre del dominio
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