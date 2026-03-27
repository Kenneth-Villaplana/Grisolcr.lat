<?php

function sendEmail($fromEmail, $fromName, $destino, $asunto, $mensajeHTML)
{
    $apiKey = trim('key-ccc080423cf27d3449415151c6053c10-c50aa110-614e2a18');
    $domain = 'mg.opticagrisol.com';

    if (!filter_var($destino, FILTER_VALIDATE_EMAIL)) {
        return "Error: correo destino inválido";
    }

    $ch = curl_init();

    $url = "https://api.mailgun.net/v3/$domain/messages";

    $headers = [
        'Authorization: Basic ' . base64_encode("api:$apiKey")
    ];

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'from' => "Optica Grisol <no-responder@$domain>",
        'to' => $destino,
        'subject' => $asunto,
        'html' => $mensajeHTML
    ]);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($error) {
        return "cURL Error: $error";
    }

    if ($httpCode < 200 || $httpCode >= 300) {
        return "Mailgun Error ($httpCode): $response";
    }

    return true;
}