<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer/Exception.php';
require __DIR__ . '/PHPMailer/PHPMailer.php';
require __DIR__ . '/PHPMailer/SMTP.php';

function sendEmail($emisor, $password, $destino, $asunto, $mensajeHTML)
{
    $mail = new PHPMailer(true);

    // Normalizar valores (clave para evitar Invalid address)
    $emisor  = trim((string)$emisor);
    $password = (string)$password;
    $destino = trim((string)$destino);

    // Validaciones mínimas
    if (!filter_var($emisor, FILTER_VALIDATE_EMAIL)) {
        return "Mailer Error: Invalid From address ($emisor)";
    }

    if (!filter_var($destino, FILTER_VALIDATE_EMAIL)) {
        return "Mailer Error: Invalid To address ($destino)";
    }

    if (empty($password)) {
        return "Mailer Error: SMTP password is empty";
    }

    try {
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $emisor;
        $mail->Password   = $password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        
        $mail->setFrom($emisor, 'Óptica Grisol');
        $mail->addAddress($destino);

        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $mensajeHTML;

        $mail->send();
        return true;

    } catch (Exception $e) {
        return "Mailer Error: {$mail->ErrorInfo}";
    }
}
