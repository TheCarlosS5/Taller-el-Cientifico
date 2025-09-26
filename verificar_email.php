<?php
session_start();
require 'admin/db_config.php';

$message = '';
$alert_type = 'danger';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Buscar el token en la base de datos
    $stmt = $conn->prepare("SELECT id, token_expires_at FROM usuarios WHERE verification_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user) {
        // Verificar si el token ha expirado
        if (strtotime($user['token_expires_at']) > time()) {
            // El token es válido, actualizamos el usuario
            $stmt_update = $conn->prepare("UPDATE usuarios SET is_verified = 1, verification_token = NULL, token_expires_at = NULL WHERE id = ?");
            $stmt_update->bind_param("i", $user['id']);
            $stmt_update->execute();
            $stmt_update->close();
            
            $message = "¡Tu correo ha sido verificado con éxito! Ahora puedes iniciar sesión.";
            $alert_type = 'success';
        } else {
            $message = "El enlace de verificación ha expirado. Por favor, solicita uno nuevo desde tu panel de cuenta.";
        }
    } else {
        $message = "El enlace de verificación no es válido o ya ha sido utilizado.";
    }
} else {
    $message = "No se proporcionó un token de verificación.";
}

$conn->close();
$page_title = "Verificación de Cuenta";
include 'includes/header.php';
?>

<div class="container py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card p-5">
                <h1 class="mb-4">Verificación de Cuenta</h1>
                <div class="alert alert-<?php echo $alert_type; ?>">
                    <?php echo $message; ?>
                </div>
                <?php if ($alert_type == 'success'): ?>
                    <a href="login.php" class="btn btn-cientifico btn-lg mt-3">Ir a Iniciar Sesión</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
