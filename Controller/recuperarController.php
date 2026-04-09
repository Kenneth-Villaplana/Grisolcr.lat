<?php
// MOSTRAR ERRORES (DEBUG)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once __DIR__ . '/../Model/recuperarModel.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../View/recuperarCuenta.php");
    exit;
}

$correo = trim($_POST["Email"] ?? '');

if (empty($correo)) {
    $_SESSION["txtMensaje"] = "Debe ingresar un correo electrónico.";
    header("Location: ../View/recuperarCuenta.php");
    exit;
}

$usuario = BuscarUsuarioPorCorreo($correo);

if (!$usuario) {
    $_SESSION["txtMensaje"] = "El correo no está registrado.";
    header("Location: ../View/recuperarCuenta.php");
    exit;
}

// Crear token y guardarlo
$token = bin2hex(random_bytes(32));
GuardarTokenRecuperacion($correo, $token);

// URL base (ya no dependemos de mail.json)
$base_url = 'https://opticagrisol.com';
$enlace = $base_url . "/View/restablecerContrasenna.php?token=" . urlencode($token);

$asunto = "Recuperación de Contraseña Óptica Grisol";

$nombre = htmlspecialchars($usuario['Nombre'] ?? '', ENT_QUOTES, 'UTF-8');
$apellido = htmlspecialchars($usuario['Apellido'] ?? '', ENT_QUOTES, 'UTF-8');

$mensaje = "
<div style='font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px;'>
    <div style='max-width: 600px; margin: auto; background: white; border-radius: 10px; padding: 25px; border: 1px solid #ddd;'>
        <h2 style='text-align:center; color:#0D3B66; margin-bottom: 10px;'>
            Recuperación de Contraseña
        </h2>
        <p style='font-size: 15px; color: #333;'>
            Hola <strong>{$nombre} {$apellido}</strong>,
        </p>
        <p style='font-size: 15px; color: #333;'>
            Hemos recibido una solicitud para restablecer la contraseña de su cuenta en 
            <strong>Óptica Grisol</strong>.
        </p>
        <div style='text-align:center; margin: 30px 0;'>
            <a href='{$enlace}' 
               style='background-color: #0D3B66; color: white; padding: 12px 25px; 
                      text-decoration: none; border-radius: 6px; font-size: 16px;
                      display: inline-block;'>
                Restablecer Contraseña
            </a>
        </div>
        <p style='word-break: break-all; font-size: 13px; color: #0D3B66;'>
            {$enlace}
        </p>
        <p style='font-size: 14px; color: #777; margin-top: 20px;'>
            Si usted no solicitó este cambio, puede ignorar este mensaje.
        </p>
        <p style='text-align:center; margin-top: 30px; font-size: 14px; color:#999;'>
            © " . date("Y") . " Óptica Grisol
        </p>
    </div>
</div>
";

require_once __DIR__ . '/../assets/vendor/php-email-form/emailSender.php';

// CORRECCIÓN IMPORTANTE: email correcto
$resultado = sendEmail(
    'no-responder@opticagrisol.com',
    'Óptica Grisol',
    $correo,
    $asunto,
    $mensaje
);

if ($resultado === true) {
    $_SESSION["txtMensaje"] = "Se ha enviado un enlace de recuperación a su correo.";
} else {
    $_SESSION["txtMensaje"] = "Error enviando correo: " . $resultado;
}

header("Location: ../View/recuperarCuenta.php");
exit;
?>