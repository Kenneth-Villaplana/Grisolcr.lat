<?php

function sendEmail($fromEmail, $fromName, $destino, $asunto, $mensajeHTML)
{
    // 🔐 CONFIG MAILGUN
    $apiKey = 'key-d2b46df408536cacb434a3a4a622ce48-c50aa110-12696da7';
    $domain = 'mg.opticagrisol.com';

    // Validaciones básicas
    if (!filter_var($destino, FILTER_VALIDATE_EMAIL)) {
        return "Error: correo destino inválido";
    }

    if (!filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
        return "Error: correo remitente inválido";
    }

    // Inicializar cURL
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://api.mailgun.net/v3/$domain/messages");
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, "api:$apiKey");

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'from' => "$fromName <$fromEmail>",
        'to' => $destino,
        'subject' => $asunto,
        'html' => $mensajeHTML
    ]);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Ejecutar
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    // Manejo de errores
    if ($error) {
        return "cURL Error: " . $error;
    }

    if ($httpCode >= 200 && $httpCode < 300) {
        return true;
    }

    return "Mailgun Error ($httpCode): " . $response;
}