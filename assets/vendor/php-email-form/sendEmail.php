<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ✅ Cargar PHPMailer manual (LOCAL)
require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

function cargarConfigMailJson(): array
{
    $ruta = '/var/www/html/config/mail.json';

    // 🔥 DEBUG 1: validar existencia
    if (!file_exists($ruta)) {
        die('ERROR: mail.json NO existe en -> ' . $ruta);
    }

    $contenido = file_get_contents($ruta);

    // 🔥 DEBUG 2: validar lectura
    if ($contenido === false) {
        die('ERROR: No se pudo leer mail.json');
    }

    // 🔥 DEBUG 3: mostrar contenido (opcional)
    // var_dump($contenido); exit;

    $config = json_decode($contenido, true);

    // 🔥 DEBUG 4: validar JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        die('JSON ERROR: ' . json_last_error_msg());
    }

    // 🔥 DEBUG 5: validar contenido
    if (!is_array($config) || empty($config)) {
        die('ERROR: JSON vacío o inválido');
    }

    return $config;
}

function sendEmail($fromEmail, $fromName, $destino, $asunto, $mensajeHTML)
{
    $mail = new PHPMailer(true);
    $config = cargarConfigMailJson();

    // 🔥 DEBUG 6: ver config real
    // var_dump($config); exit;

    // ✅ CONFIG
    $host       = $config['SMTP_HOST'] ?? '';
    $port       = (int)($config['SMTP_PORT'] ?? 0);
    $username   = $config['SMTP_USER'] ?? '';
    $password   = $config['SMTP_PASS'] ?? '';
    $secure     = strtolower($config['SMTP_SECURE'] ?? 'tls');

    // 🔥 DEBUG 7: validar claves críticas
    if (empty($host)) {
        die('ERROR: SMTP_HOST vacío');
    }

    if (empty($username)) {
        die('ERROR: SMTP_USER vacío');
    }

    if (empty($password)) {
        die('ERROR: SMTP_PASS vacío');
    }

    $fromEmail  = $username; // más seguro
    $fromName   = $fromName ?: 'Óptica Grisol';

    // Validaciones
    if (!filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
        return "Mailer Error: Invalid FROM email";
    }

    if (!filter_var($destino, FILTER_VALIDATE_EMAIL)) {
        return "Mailer Error: Invalid DESTINATION email";
    }

    try {
        $mail->CharSet = 'UTF-8';

        // SMTP
        $mail->isSMTP();
        $mail->SMTPDebug = 0; // pon 2 si quieres ver logs
        $mail->Debugoutput = 'error_log';
        $mail->Timeout = 15;

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

        // Fix SSL (DigitalOcean)
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true
            ]
        ];

        // Remitente
        $mail->setFrom($fromEmail, $fromName);

        // Destinatario
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