<?php
session_start();
require 'admin/db_config.php';
require 'vendor/autoload.php'; // Requerimos PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ... (recogida de datos y validaciones sin cambios)
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $tipo_documento = $_POST['tipo_documento'] ?? '';
    $numero_documento = trim($_POST['numero_documento'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Validaciones
    if (empty($nombre)) $errors[] = "El nombre es obligatorio.";
    if (empty($apellido)) $errors[] = "El apellido es obligatorio.";
    if (empty($tipo_documento)) $errors[] = "Debes seleccionar un tipo de documento.";
    if (empty($numero_documento)) $errors[] = "El número de documento es obligatorio.";
    if (empty($telefono)) $errors[] = "El teléfono es obligatorio.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "El correo electrónico no es válido.";
    if (strlen($password) < 8) $errors[] = "La contraseña debe tener al menos 8 caracteres.";
    if ($password !== $password_confirm) $errors[] = "Las contraseñas no coinciden.";

    // Verificar si el email ya existe
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Este correo electrónico ya está registrado.";
        }
        $stmt->close();
    }

    // Si no hay errores, registrar al usuario
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        // Generar token de verificación
        $verification_token = bin2hex(random_bytes(32));
        $token_expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, tipo_documento, numero_documento, telefono, email, password_hash, verification_token, token_expires_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $nombre, $apellido, $tipo_documento, $numero_documento, $telefono, $email, $password_hash, $verification_token, $token_expires_at);
        
        if ($stmt->execute()) {
            // --- ENVIAR CORREO DE VERIFICACIÓN ---
            $mail = new PHPMailer(true);
            try {
                // Configuración del servidor (la misma que usamos para el admin)
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'carlosstivengutierrezramirez@gmail.com'; // TU CORREO
                $mail->Password   = 'tualcxhvxobmifmt';        // TU CONTRASEÑA DE APLICACIÓN
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;

                // Destinatarios
                $mail->setFrom('no-reply@tallerelcientifico.com', 'Taller El Científico');
                $mail->addAddress($email, $nombre . ' ' . $apellido);

                // Contenido del correo
                $mail->isHTML(true);
                $mail->Subject = 'Verifica tu cuenta en Taller El Cientifico';
                $verification_link = "http://localhost/TALLER_EL_CIENTIFICO/verificar_email.php?token=" . $verification_token;
                
                $mail->addEmbeddedImage('assets/img/logo.jpg', 'logo_cientifico');
                $mail->Body    = "
                    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #ddd; border-radius: 10px; overflow: hidden;'>
                        <div style='background-color: #0d2c4f; color: white; padding: 20px; text-align: center;'>
                            <img src='cid:logo_cientifico' alt='Logo Taller El Científico' style='max-height: 60px;'>
                            <h1 style='margin: 10px 0 0; color: #fbc108;'>¡Bienvenido a Taller El Científico!</h1>
                        </div>
                        <div style='padding: 30px 20px; color: #333; line-height: 1.6;'>
                            <h2 style='color: #0d2c4f;'>Hola, " . htmlspecialchars($nombre) . "</h2>
                            <p>Gracias por registrarte. Solo falta un paso más para activar tu cuenta. Por favor, haz clic en el siguiente botón para verificar tu dirección de correo electrónico:</p>
                            <div style='text-align: center; margin: 30px 0;'>
                                <a href='" . $verification_link . "' style='background-color: #fbc108; color: #0d2c4f; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold;'>Verificar mi Correo</a>
                            </div>
                            <p>Si el botón no funciona, puedes copiar y pegar el siguiente enlace en tu navegador:</p>
                            <p style='word-break: break-all; font-size: 12px;'><a href='" . $verification_link . "'>" . $verification_link . "</a></p>
                            <p>Este enlace es válido por 24 horas.</p>
                        </div>
                    </div>
                ";
                $mail->AltBody = 'Para verificar tu cuenta, por favor visita el siguiente enlace: ' . $verification_link;

                $mail->send();
                $success_message = "¡Registro casi completo! Hemos enviado un correo a <strong>" . htmlspecialchars($email) . "</strong> para que verifiques tu cuenta.";

            } catch (Exception $e) {
                // No detenemos el registro si el email falla, pero lo podemos registrar
                // error_log("Mailer Error: " . $mail->ErrorInfo);
                $success_message = "¡Registro exitoso! Sin embargo, no pudimos enviar el correo de verificación. Podrás solicitar uno nuevo desde tu panel de cuenta.";
            }

        } else {
            $errors[] = "Error al registrar el usuario. Por favor, inténtalo de nuevo.";
        }
        $stmt->close();
    }
}
$conn->close();

$page_title = "Crear Cuenta";
include 'includes/header.php';
?>

<div class="login-page d-flex align-items-center justify-content-center py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="login-form-card shadow-lg p-5">
                    <h1 class="text-center mb-4">Crear una Cuenta</h1>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success_message): ?>
                        <div class="alert alert-success">
                            <?php echo $success_message; ?>
                        </div>
                         <div class="text-center mt-4">
                            <a href="login.php" class="btn btn-cientifico">Ir a Iniciar Sesión</a>
                        </div>
                    <?php else: ?>
                        <form action="registro.php" method="POST">
                            <!-- ... (el formulario HTML de registro no cambia) ... -->
                             <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nombre" class="form-label">Nombre(s)</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="apellido" class="form-label">Apellido(s)</label>
                                    <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo htmlspecialchars($apellido ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tipo_documento" class="form-label">Tipo de Documento</label>
                                    <select class="form-select" id="tipo_documento" name="tipo_documento" required>
                                        <option value="" disabled selected>Seleccionar...</option>
                                        <option value="CC" <?php if (($tipo_documento ?? '') == 'CC') echo 'selected'; ?>>Cédula de Ciudadanía</option>
                                        <option value="CE" <?php if (($tipo_documento ?? '') == 'CE') echo 'selected'; ?>>Cédula de Extranjería</option>
                                        <option value="TI" <?php if (($tipo_documento ?? '') == 'TI') echo 'selected'; ?>>Tarjeta de Identidad</option>
                                        <option value="PP" <?php if (($tipo_documento ?? '') == 'PP') echo 'selected'; ?>>Pasaporte</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="numero_documento" class="form-label">Número de Documento</label>
                                    <input type="text" class="form-control" id="numero_documento" name="numero_documento" value="<?php echo htmlspecialchars($numero_documento ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono de Contacto</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($telefono ?? ''); ?>" required>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="form-text">Debe tener al menos 8 caracteres.</div>
                            </div>
                            <div class="mb-3">
                                <label for="password_confirm" class="form-label">Confirmar Contraseña</label>
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-cientifico btn-lg">Crear Cuenta</button>
                            </div>
                        </form>
                    <?php endif; ?>

                    <p class="text-center mt-4">
                        ¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

