<?php
// Usamos __DIR__ para rutas a prueba de errores.
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/db_config.php';

session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Definimos el correo del administrador aquí para no tener que escribirlo en el formulario.
define('ADMIN_EMAIL', 'carlosstivengutierrezramirez@gmail.com');

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = ADMIN_EMAIL; // Usamos siempre el mismo correo.

    $stmt = $conn->prepare("SELECT id, nombre FROM administradores WHERE email = ? AND activo = 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin) {
        // ... (El resto de la lógica para generar código y enviar correo sigue igual)
        $admin_nombre = $admin['nombre'];
        $code = rand(100000, 999999);
        $expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        $stmt_insert = $conn->prepare("INSERT INTO admin_codes (email, code, expires_at) VALUES (?, ?, ?)");
        $stmt_insert->bind_param("sss", $email, $code, $expires_at);
        $stmt_insert->execute();

        $mail = new PHPMailer(true);
        try {
            // Configuración del servidor (sin cambios)
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'carlosstivengutierrezramirez@gmail.com';
            $mail->Password   = 'tualcxhvxobmifmt';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            // Destinatarios y contenido (sin cambios)
            $mail->setFrom('no-reply@tallerelcientifico.com', 'Taller El Científico');
            $mail->addAddress($email, $admin_nombre);
            $mail->isHTML(true);
            $mail->Subject = 'Tu Codigo de Acceso para Taller El Cientifico';
            $logo_path = __DIR__ . '/../assets/img/logo.jpg';
            $mail->addEmbeddedImage($logo_path, 'logo_cientifico');
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #ddd; border-radius: 10px; overflow: hidden;'>
                    <div style='background-color: #0d2c4f; color: white; padding: 20px; text-align: center;'>
                        <img src='cid:logo_cientifico' alt='Logo Taller El Científico' style='max-height: 60px;'>
                        <h1 style='margin: 10px 0 0; color: #fbc108;'>Acceso al Panel de Administrador</h1>
                    </div>
                    <div style='padding: 30px 20px; color: #333; line-height: 1.6;'>
                        <h2 style='color: #0d2c4f;'>Hola, " . htmlspecialchars($admin_nombre) . "</h2>
                        <p>Este es tu código para acceder al panel de administración:</p>
                        <div style='text-align: center; margin: 30px 0;'>
                            <span style='font-size: 36px; font-weight: bold; letter-spacing: 5px; color: #0d2c4f; background-color: #f0f0f0; padding: 15px 30px; border-radius: 8px;'>
                                " . $code . "
                            </span>
                        </div>
                        <p>Este código es válido por <strong>10 minutos</strong>.</p>
                    </div>
                </div>";

            $mail->send();
            header('Location: verify_code.php?email=' . urlencode($email));
            exit();

        } catch (Exception $e) {
            $message = "No se pudo enviar el correo. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $message = "El correo del administrador principal no está configurado o está inactivo.";
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
        .admin-login-page { background-color: var(--cientifico-dark-blue); }
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
                            <!-- No hay campo de email, se envía directamente -->
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

