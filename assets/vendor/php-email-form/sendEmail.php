<?php

require __DIR__ . '/PHPMailer/Exception.php';
require __DIR__ . '/PHPMailer/PHPMailer.php';
require __DIR__ . '/PHPMailer/SMTP.php';;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
    //  Cargar configuración
    $config = cargarConfigMailJson();

    $host     = $config['SMTP_HOST'] ?? 'mail.privateemail.com';
    $port     = (int)($config['SMTP_PORT'] ?? 587);
    $username = $config['SMTP_USER'] ?? '';
    $password = $config['SMTP_PASS'] ?? '';
    $secure   = strtolower($config['SMTP_SECURE'] ?? 'tls');

    //  Validación config
    if (empty($apiKey) || empty($domain)) {
        return "Error: configuración de Mailgun incompleta";
    }

    //  Validación email
    if (!filter_var($destino, FILTER_VALIDATE_EMAIL)) {
        return "Error: correo destino inválido";
    }

    $fromEmail = $fromEmail ?: $username;
    $fromName  = $fromName ?: 'Óptica Grisol';

    try {
        $mg = Mailgun::create($apiKey);

        $mg->messages()->send($domain, [
            //  FROM siempre del dominio
            'from' => "Optica Grisol <no-responder@$domain>",
            'to' => $destino,
            'subject' => $asunto,
            'html' => $mensajeHTML
        ]);

        return true;

    } catch (Exception $e) {
        return "Mailer Error: " . $mail->ErrorInfo;
    }
}
