<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../../vendor/autoload.php';

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
    $mail = new PHPMailer(true);
    $config = cargarConfigMailJson();

    $host       = $config['SMTP_HOST'] ?? 'mail.privateemail.com';
    $port       = (int)($config['SMTP_PORT'] ?? 587);
    $username   = $config['SMTP_USER'] ?? '';
    $password   = $config['SMTP_PASS'] ?? '';
    $secure     = strtolower($config['SMTP_SECURE'] ?? 'tls');

    $fromEmail  = $fromEmail ?: $username;
    $fromName   = $fromName ?: 'Óptica Grisol';

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

        $mail->isSMTP();
        $mail->Host       = $host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $username;
        $mail->Password   = $password;
        $mail->Port       = $port;

        if ($secure === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true
            ]
        ];

        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($destino);

        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $mensajeHTML;

        $mail->send();

        return true;

    } catch (Exception $e) {
        return "Mailer Error: " . $mail->ErrorInfo;
    }
}