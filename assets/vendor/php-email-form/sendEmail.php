<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer/Exception.php';
require __DIR__ . '/PHPMailer/PHPMailer.php';
require __DIR__ . '/PHPMailer/SMTP.php';

function cargarConfigMailJson(): array
{
    $ruta = __DIR__ . '/../../../config/mail.json';

    if (!file_exists($ruta)) {
        return [];
    }

    $contenido = file_get_contents($ruta);
    $config = json_decode($contenido, true);

    return is_array($config) ? $config : [];
}

function sendEmail($emisor, $password, $destino, $asunto, $mensajeHTML)
{
    $mail = new PHPMailer(true);
    $config = cargarConfigMailJson();

    $emisor = trim((string)($emisor ?: ($config['MAIL_FROM'] ?? '')));
    $password = (string)($password ?: ($config['MAIL_APP_PASSWORD'] ?? ''));
    $host = trim((string)($config['MAIL_HOST'] ?? 'smtp.gmail.com'));
    $port = (int)($config['MAIL_PORT'] ?? 587);
    $encryption = strtolower(trim((string)($config['MAIL_ENCRYPTION'] ?? 'tls')));
    $timeout = (int)($config['MAIL_TIMEOUT'] ?? 15);
    $fromName = trim((string)($config['MAIL_FROM_NAME'] ?? 'Óptica Grisol'));
    $debug = !empty($config['MAIL_DEBUG']);

    $destino = trim((string)$destino);
    $asunto = (string)$asunto;

    if (!filter_var($emisor, FILTER_VALIDATE_EMAIL)) {
        return "Mailer Error: Invalid address: (From): $emisor";
    }

    if (!filter_var($destino, FILTER_VALIDATE_EMAIL)) {
        return "Mailer Error: Invalid address: (To): $destino";
    }

    if (empty($password)) {
        return "Mailer Error: SMTP password is empty";
    }

    try {
        if ($debug) {
            $mail->SMTPDebug = 2;
            $mail->Debugoutput = function ($str, $level) {
                error_log("SMTP[$level]: $str");
            };
        }

        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        $mail->isSMTP();
        $mail->Host = $host;
        $mail->SMTPAuth = true;
        $mail->Username = $emisor;
        $mail->Password = $password;
        $mail->Port = $port;
        $mail->Timeout = $timeout;
        $mail->SMTPKeepAlive = false;

        if ($encryption === 'ssl' || $encryption === 'smtps') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        $mail->SMTPOptions = [
            'socket' => [
                'bindto' => '0.0.0.0:0'
            ],
        ];

        $mail->setFrom($emisor, $fromName);
        $mail->addAddress($destino);

        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body = $mensajeHTML;

        $mail->send();
        return true;

    } catch (Exception $e) {
        return "Mailer Error: {$mail->ErrorInfo}";
    }
}