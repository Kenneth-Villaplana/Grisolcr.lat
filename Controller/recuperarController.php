<?php
include_once __DIR__ . '/../Model/recuperarModel.php';

session_start();

// SIEMPRE ENTRARÁ AL CONTROLADOR SI HAY POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    echo "ENTRÓ AL CONTROLLER<br>"; // prueba
    print_r($_POST);                // prueba
    // exit;  // <- quítalo cuando confirmemos que funciona

    $correo = $_POST["Email"] ?? '';

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

    // Crear token
    $token = bin2hex(random_bytes(32));
    GuardarTokenRecuperacion($correo, $token);

    $enlace = "http://127.0.0.1/OptiGestion/View/restablecerContrasenna.php?token=$token";

    // Datos del correo
    $correo_emisor = "linethleivacr@gmail.com";
    $app_password = "fqnn wxyr laui xspz";

    $asunto = "Recuperación de Contraseña Optica Grisol";
    $mensaje= "
<div style='font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px;'>
    <div style='max-width: 600px; margin: auto; background: white; border-radius: 10px; padding: 25px; border: 1px solid #ddd;'>

        <h2 style='text-align:center; color:#0D3B66; margin-bottom: 10px;'>
            Recuperación de Contraseña
        </h2>

        <p style='font-size: 15px; color: #333;'>
            Hola <strong>{$usuario['Nombre']} {$usuario['Apellido']}</strong>,
        </p>

        <p style='font-size: 15px; color: #333;'>
            Hemos recibido una solicitud para restablecer la contraseña de su cuenta en 
            <strong>Óptica Grisol</strong>. Si usted realizó esta solicitud, por favor haga clic en el siguiente botón:
        </p>

        <div style='text-align:center; margin: 30px 0;'>
            <a href='$enlace' 
            style='background-color: #0D3B66; color: white; padding: 12px 25px; 
                   text-decoration: none; border-radius: 6px; font-size: 16px;
                   display: inline-block;'>
                Restablecer Contraseña
            </a>
        </div>

        <p style='font-size: 14px; color: #555;'>
            Si el botón no funciona, puede copiar y pegar el siguiente enlace en su navegador:
        </p>

        <p style='word-break: break-all; font-size: 13px; color: #0D3B66;'>
            $enlace
        </p>

        <p style='font-size: 14px; color: #777; margin-top: 20px;'>
            Si usted no solicitó este cambio, puede ignorar este mensaje. 
            Su cuenta permanecerá segura.
        </p>

        <p style='text-align:center; margin-top: 30px; font-size: 14px; color:#999;'>
            © " . date("Y") . " Óptica Grisol — Sistema de Gestión
        </p>

    </div>
</div>
";


    require __DIR__ . "/../assets/vendor/php-email-form/sendEmail.php";

    $resultado = sendEmail($correo_emisor, $app_password, $correo, $asunto, $mensaje);

    if ($resultado === true) {
        $_SESSION["txtMensaje"] = "Se ha enviado un enlace de recuperación a su correo.";
    } else {
        $_SESSION["txtMensaje"] = "Error enviando correo: $resultado";
    }

    header("Location: ../View/recuperarCuenta.php");
    exit;
}
?>