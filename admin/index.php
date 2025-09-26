<?php
session_start();
require 'db_config.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT id, nombre FROM administradores WHERE email = ? AND activo = 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin) {
        $admin_id = $admin['id'];
        $admin_nombre = $admin['nombre'];
        $code = rand(100000, 999999);
        $expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        $stmt = $conn->prepare("INSERT INTO admin_codes (email, code, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $code, $expires_at);
        $stmt->execute();

        $mail = new PHPMailer(true);

        try {
            //Server settings
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;  // Descomenta esta línea para ver errores detallados
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'carlosstivengutierrezramirez@gmail.com'; // <-- TU CORREO GMAIL COMPLETO
            $mail->Password   = 'tualcxhvxobmifmt';        // <-- TU CONTRASEÑA DE APLICACIÓN DE 16 LETRAS
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            //Recipients
            $mail->setFrom('no-reply@tallerelcientifico.com', 'Taller El Científico');
            $mail->addAddress($email, $admin_nombre);

            // --- INICIO DE LA PLANTILLA HTML ---
            $mail->isHTML(true);
            $mail->Subject = 'Tu Codigo de Acceso para Taller El Cientifico';
            
            // Adjuntar el logo para que se muestre en el correo
            // La ruta '../assets/img/logo.jpg' es relativa a la ubicación de este archivo (admin/index.php)
            $mail->addEmbeddedImage('../assets/img/logo.jpg', 'logo_cientifico');

            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #ddd; border-radius: 10px; overflow: hidden;'>
                    <div style='background-color: #0d2c4f; color: white; padding: 20px; text-align: center;'>
                        <img src='cid:logo_cientifico' alt='Logo Taller El Científico' style='max-height: 60px;'>
                        <h1 style='margin: 10px 0 0; color: #fbc108;'>Acceso al Panel de Administrador</h1>
                    </div>
                    <div style='padding: 30px 20px; color: #333; line-height: 1.6;'>
                        <h2 style='color: #0d2c4f;'>Hola, " . htmlspecialchars($admin_nombre) . "</h2>
                        <p>Has solicitado un código para acceder a tu panel de administración. Por favor, utiliza el siguiente código para continuar:</p>
                        <div style='text-align: center; margin: 30px 0;'>
                            <span style='font-size: 36px; font-weight: bold; letter-spacing: 5px; color: #0d2c4f; background-color: #f0f0f0; padding: 15px 30px; border-radius: 8px; border: 1px dashed #ccc;'>
                                " . $code . "
                            </span>
                        </div>
                        <p>Este código es válido por <strong>10 minutos</strong>. Si no has solicitado este acceso, puedes ignorar este correo de forma segura.</p>
                        <p>¡Gracias por tu gestión!</p>
                        <p><strong>El equipo de Taller El Científico</strong></p>
                    </div>
                    <div style='background-color: #f8f9fa; color: #888; padding: 15px; text-align: center; font-size: 12px;'>
                        Este es un correo automático. Por favor, no respondas a este mensaje.
                    </div>
                </div>
            ";
            
            $mail->AltBody = 'Hola ' . $admin_nombre . ', tu código de acceso es: ' . $code . '. Es válido por 10 minutos.';
            // --- FIN DE LA PLANTILLA HTML ---

            $mail->send();
            header('Location: verify_code.php?email=' . urlencode($email));
            exit();
        } catch (Exception $e) {
            $message = "No se pudo enviar el correo. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $message = "El correo electrónico no corresponde a un administrador activo.";
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Taller El Científico</title>
    <link rel="stylesheet" href="../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-login-page {
            background-color: var(--cientifico-dark-blue);
        }
    </style>
</head>
<body class="admin-login-page">
    <div class="login-page d-flex align-items-center justify-content-center py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-4">
                    <div class="login-form-card shadow-lg p-5 text-center">
                        <img src="../assets/img/logo.jpg" alt="Logo" class="login-logo mb-4">
                        <h1 class="mb-3">Acceso de Administrador</h1>
                        <p class="text-muted mb-4">Presiona el botón para recibir un código de acceso en tu correo.</p>

                        <?php if ($message): ?>
                            <div class="alert alert-danger"><?php echo $message; ?></div>
                        <?php endif; ?>

                        <form action="index.php" method="POST">
                            <input type="hidden" name="email" value="carlosstivengutierrezramirez@gmail.com">
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">Enviar Código de Acceso</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

