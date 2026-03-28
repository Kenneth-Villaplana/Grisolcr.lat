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
    // 🔐 Cargar configuración
    $config = cargarConfigMailJson();

    $host     = $config['SMTP_HOST'] ?? 'mail.privateemail.com';
    $port     = (int)($config['SMTP_PORT'] ?? 587);
    $username = $config['SMTP_USER'] ?? '';
    $password = $config['SMTP_PASS'] ?? '';
    $secure   = strtolower($config['SMTP_SECURE'] ?? 'tls');

    // 🚨 Validaciones
    if (empty($username) || empty($password)) {
        return "Error: configuración SMTP incompleta";
    }

    if (!filter_var($destino, FILTER_VALIDATE_EMAIL)) {
        return "Error: correo destino inválido";
    }

    $fromEmail = $fromEmail ?: $username;
    $fromName  = $fromName ?: 'Óptica Grisol';

    try {
        $mail = new PHPMailer(true);

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

        // 🔥 FIX DigitalOcean
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
    }
}
