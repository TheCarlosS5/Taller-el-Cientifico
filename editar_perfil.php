<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
require 'admin/db_config.php';
$user_id = $_SESSION['user_id'];

// Mensajes para mostrar al usuario
$profile_message = '';
$password_message = '';
$profile_alert_type = '';
$password_alert_type = '';

// --- LÓGICA PARA ACTUALIZAR DATOS DEL PERFIL ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $telefono = trim($_POST['telefono']);
    // (Omitimos el cambio de email por ahora para simplificar)

    $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, telefono = ? WHERE id = ?");
    $stmt->bind_param("sssi", $nombre, $apellido, $telefono, $user_id);
    if($stmt->execute()){
        $profile_message = "¡Tus datos han sido actualizados correctamente!";
        $profile_alert_type = "success";
        // Actualizar el nombre en la sesión
        $_SESSION['user_nombre_completo'] = trim($nombre . ' ' . $apellido);
    } else {
        $profile_message = "Hubo un error al actualizar tus datos.";
        $profile_alert_type = "danger";
    }
    $stmt->close();
}

// --- LÓGICA PARA CAMBIAR LA CONTRASEÑA ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $stmt = $conn->prepare("SELECT password_hash FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($current_password, $user['password_hash'])) {
        if (strlen($new_password) >= 8) {
            if ($new_password === $confirm_password) {
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt_update = $conn->prepare("UPDATE usuarios SET password_hash = ? WHERE id = ?");
                $stmt_update->bind_param("si", $new_password_hash, $user_id);
                if($stmt_update->execute()){
                    $password_message = "¡Contraseña actualizada con éxito!";
                    $password_alert_type = "success";
                } else {
                    $password_message = "Hubo un error al actualizar la contraseña.";
                    $password_alert_type = "danger";
                }
                $stmt_update->close();
            } else {
                $password_message = "La nueva contraseña y su confirmación no coinciden.";
                $password_alert_type = "danger";
            }
        } else {
            $password_message = "La nueva contraseña debe tener al menos 8 caracteres.";
            $password_alert_type = "danger";
        }
    } else {
        $password_message = "La contraseña actual es incorrecta.";
        $password_alert_type = "danger";
    }
}

// Obtener los datos actuales del usuario para rellenar el formulario
$stmt = $conn->prepare("SELECT nombre, apellido, email, telefono, is_verified FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

$page_title = "Editar Mi Perfil";
include 'includes/header.php';
?>

<div class="container py-5">
    <h1 class="display-5 mb-4">Editar Mi Perfil</h1>

    <!-- Pestañas de navegación -->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">Mi Información</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab" aria-controls="password" aria-selected="false">Cambiar Contraseña</button>
        </li>
    </ul>

    <div class="tab-content card" id="myTabContent">
        <!-- Panel de Mi Información -->
        <div class="tab-pane fade show active p-4" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <h4 class="mb-3">Datos Personales</h4>
            
            <?php if($profile_message): ?>
                <div class="alert alert-<?php echo $profile_alert_type; ?>"><?php echo $profile_message; ?></div>
            <?php endif; ?>

            <form method="POST" action="editar_perfil.php">
                <input type="hidden" name="update_profile" value="1">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nombre" class="form-label">Nombre(s)</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($user_data['nombre']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="apellido" class="form-label">Apellido(s)</label>
                        <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo htmlspecialchars($user_data['apellido']); ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($user_data['telefono']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" disabled readonly>
                    <div class="form-text">
                        <?php if($user_data['is_verified']): ?>
                            <span class="text-success"><i class="bi bi-check-circle-fill"></i> Correo verificado</span>
                        <?php else: ?>
                            <span class="text-warning"><i class="bi bi-exclamation-triangle-fill"></i> Correo no verificado.</span> <a href="#">Reenviar correo de verificación</a>
                        <?php endif; ?>
                    </div>
                </div>
                <button type="submit" class="btn btn-cientifico">Guardar Cambios</button>
            </form>
        </div>

        <!-- Panel de Cambiar Contraseña -->
        <div class="tab-pane fade p-4" id="password" role="tabpanel" aria-labelledby="password-tab">
            <h4 class="mb-3">Actualizar Contraseña</h4>

             <?php if($password_message): ?>
                <div class="alert alert-<?php echo $password_alert_type; ?>"><?php echo $password_message; ?></div>
            <?php endif; ?>

            <form method="POST" action="editar_perfil.php">
                <input type="hidden" name="change_password" value="1">
                <div class="mb-3">
                    <label for="current_password" class="form-label">Contraseña Actual</label>
                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                </div>
                <div class="mb-3">
                    <label for="new_password" class="form-label">Nueva Contraseña</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirmar Nueva Contraseña</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-cientifico">Cambiar Contraseña</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
