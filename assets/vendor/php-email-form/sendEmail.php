<?php

function sendEmail($fromEmail, $fromName, $destino, $asunto, $mensajeHTML)
{
    // 🔐 CONFIG MAILGUN
    $apiKey = 'key-ccc080423cf27d3449415151c6053c10-c50aa110-614e2a18'; // 
    $domain = 'mg.opticagrisol.com';

    // ✅ Validaciones básicas
    if (!filter_var($destino, FILTER_VALIDATE_EMAIL)) {
        return "Error: correo destino inválido";
    }

    if (!filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
        return "Error: correo remitente inválido";
    }

    // ⚠️ Mailgun recomienda usar un FROM del dominio verificado
    $from = "$fromName <$fromEmail>";

    // Inicializar cURL
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://api.mailgun.net/v3/$domain/messages");
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, "api:$apiKey");

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'from' => $from,
        'to' => $destino,
        'subject' => $asunto,
        'html' => $mensajeHTML
    ]);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // 🔥 DEBUG (IMPORTANTE)
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // evita que se quede pegado
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    // Ejecutar
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    // ❌ Error de conexión
    if ($error) {
        return "cURL Error: " . $error;
    }

    // ❌ Error Mailgun
    if ($httpCode < 200 || $httpCode >= 300) {
        return "Mailgun Error ($httpCode): " . $response;
    }

    // ✅ Éxito
    return true;
}