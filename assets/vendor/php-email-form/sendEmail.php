<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer/Exception.php';
require __DIR__ . '/PHPMailer/PHPMailer.php';
require __DIR__ . '/PHPMailer/SMTP.php';


function sendEmail($emisor, $password, $destino, $asunto, $mensajeHTML)
{
    $mail = new PHPMailer(true);

    // 1) Tomar credenciales desde Azure Application Settings si no vienen por parámetro
    $emisorEnv = trim((string) getenv("MAIL_FROM"));
    $passEnv = (string) getenv("MAIL_APP_PASSWORD");

    $emisor = trim((string) ($emisor ?: $emisorEnv));
    $password = (string) ($password ?: $passEnv);

    $destino = trim((string) $destino);
    $asunto = (string) $asunto;

    // 2) Validaciones básicas para evitar "Invalid address (From)"
    if (!filter_var($emisor, FILTER_VALIDATE_EMAIL)) {
        return "Mailer Error: Invalid address: (From): $emisor";
    }
    if (!filter_var($destino, FILTER_VALIDATE_EMAIL)) {
        return "Mailer Error: Invalid address: (To): $destino";
    }
    if (empty($password)) {
        return "Mailer Error: SMTP password is empty (MAIL_APP_PASSWORD)";
    }

    // 3) Modo TEST/DEBUG (se ve en Azure Log Stream)
    $isTest = (string) getenv("APP_MAIL_TEST") === "1";
    if ($isTest) {
        $mail->SMTPDebug = 2; // 0=off, 2=client+server
        $mail->Debugoutput = function ($str, $level) {
            error_log("SMTP[$level]: $str");
        };
        error_log("MAIL TEST: From={$emisor} To={$destino} Subject={$asunto}");
    }

    try {
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->Timeout = 15;

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $emisor;
        $mail->Password = $password;

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->Timeout = 15;
        $mail->SMTPKeepAlive = false;

        // 5) Forzar IPv4 (MUY importante en Azure para evitar entregas “fantasma”)
        $mail->SMTPOptions = [
            'socket' => [
                'bindto' => '0.0.0.0:0'
            ],
            // Si algún entorno rompe por certificados, descomenta SOLO para prueba:
            // 'ssl' => [
            //     'verify_peer'       => false,
            //     'verify_peer_name'  => false,
            //     'allow_self_signed' => true,
            // ],
        ];

        // 6) Mensaje
        $mail->setFrom($emisor, 'Óptica Grisol');
        $mail->addAddress($destino);

        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body = $mensajeHTML;

        $mail->send();

        if ($isTest) {
            error_log("MAIL TEST: PHPMailer->send() OK");
        }

        return true;

    } catch (Exception $e) {
        // ErrorInfo suele ser más útil que $e->getMessage()
        if ($isTest) {
            error_log("MAIL TEST ERROR: " . $mail->ErrorInfo);
        }
        return "Mailer Error: {$mail->ErrorInfo}";
    }
}
