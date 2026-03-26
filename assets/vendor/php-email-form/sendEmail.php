<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ✅ usar composer
require_once __DIR__ . '/../../../vendor/autoload.php';

function cargarConfigMailJson(): array
{
    $ruta = __DIR__ . '/../../../config/mail.example.json';

    if (!file_exists($ruta)) {
        return [];
    }

    $contenido = file_get_contents($ruta);
    $config = json_decode($contenido, true);

    return is_array($config) ? $config : [];
}

function sendEmail($fromEmail, $fromName, $destino, $asunto, $mensajeHTML)
{
    $mail = new PHPMailer(true);
    $config = cargarConfigMailJson();

    // ✅ usar config correcta
    $host       = $config['SMTP_HOST'] ?? 'mail.privateemail.com';
    $port       = (int)($config['SMTP_PORT'] ?? 587);
    $username   = $config['SMTP_USER'] ?? '';
    $password   = $config['SMTP_PASS'] ?? '';
    $secure     = strtolower($config['SMTP_SECURE'] ?? 'tls');

    $fromEmail  = $fromEmail ?: $username;
    $fromName   = $fromName ?: 'Óptica Grisol';

    // Validaciones
    if (!filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
        return "Mailer Error: Invalid FROM email";
    }

    if (!filter_var($destino, FILTER_VALIDATE_EMAIL)) {
        return "Mailer Error: Invalid DESTINATION email";
    }

    if (empty($password)) {
        return "Mailer Error: SMTP password vacío";
    }

    try {
        $mail->CharSet = 'UTF-8';

        // CONFIG SMTP
        $mail->isSMTP();
        $mail->Host       = $host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $username;
        $mail->Password   = $password;
        $mail->Port       = $port;

        // Seguridad
        if ($secure === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        // ✅ FIX DigitalOcean (IMPORTANTE)
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true
            ]
        ];

        // Remitente
        $mail->setFrom($fromEmail, $fromName);

        // Destino
        $mail->addAddress($destino);

        // Contenido
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $mensajeHTML;

        $mail->send();

        return true;

    } catch (Exception $e) {
        return "Mailer Error: " . $mail->ErrorInfo;
    }}