<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/PHPMailer/PHPMailer.php";
require __DIR__ . "/PHPMailer/SMTP.php";
require __DIR__ . "/PHPMailer/Exception.php";

function enviarCorreo($correo_emisor, $password_app, $correo_destino, $asunto, $mensajeHTML)
{
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = $correo_emisor;
        $mail->Password = $password_app;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom($correo_emisor, "Óptica Grisol");
        $mail->addAddress($correo_destino);

        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body = $mensajeHTML;

        $mail->send();
        return true;

    } catch (Exception $e) {
        return "Error enviando correo: " . $e->getMessage();
    }
}
