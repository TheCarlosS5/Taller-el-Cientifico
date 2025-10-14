<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
require 'admin/db_config.php';
$user_id = $_SESSION['user_id'];

$profile_message = '';
$profile_alert_type = '';

// LÓGICA PARA ACTUALIZAR DATOS DEL PERFIL
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $telefono = trim($_POST['telefono']);
    $avatar_url = trim($_POST['avatar_url']); // Nuevo campo

    if (!empty($avatar_url) && !filter_var($avatar_url, FILTER_VALIDATE_URL)) {
        $profile_message = "La URL de la imagen no es válida.";
        $profile_alert_type = "danger";
    } else {
        $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, telefono = ?, avatar_url = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $nombre, $apellido, $telefono, $avatar_url, $user_id);
        
        if($stmt->execute()){
            $profile_message = "¡Tus datos han sido actualizados correctamente!";
            $profile_alert_type = "success";
            $_SESSION['user_nombre_completo'] = trim($nombre . ' ' . $apellido);
            $_SESSION['user_avatar_url'] = $avatar_url;
        } else {
            $profile_message = "Hubo un error al actualizar tus datos.";
            $profile_alert_type = "danger";
        }
        $stmt->close();
    }
}
//... (lógica de cambio de contraseña sin cambios)

$stmt = $conn->prepare("SELECT nombre, apellido, email, telefono, avatar_url FROM usuarios WHERE id = ?");
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
    <div class="card">
        <div class="card-body p-4">
            <h4 class="mb-4">Datos Personales</h4>
            
            <?php if($profile_message): ?>
                <div class="alert alert-<?php echo $profile_alert_type; ?>"><?php echo $profile_message; ?></div>
            <?php endif; ?>

            <form method="POST" action="editar_perfil.php">
                <input type="hidden" name="update_profile" value="1">
                
                <div class="mb-4 text-center">
                    <?php if (!empty($user_data['avatar_url'])): ?>
                        <img src="<?php echo htmlspecialchars($user_data['avatar_url']); ?>" alt="Avatar" class="avatar-preview mb-2">
                    <?php else: ?>
                        <div class="user-initial avatar-preview-initial mx-auto mb-2"><?php echo strtoupper(substr($user_data['nombre'], 0, 1)); ?></div>
                    <?php endif; ?>
                    <label for="avatar_url" class="form-label">URL de la Foto de Perfil (Opcional)</label>
                    <input type="url" class="form-control" id="avatar_url" name="avatar_url" value="<?php echo htmlspecialchars($user_data['avatar_url'] ?? ''); ?>" placeholder="https://ejemplo.com/imagen.png">
                </div>
                <hr>
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
                    <label for="email" class="form-label">Correo Electrónico (No se puede cambiar)</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" disabled readonly>
                </div>
                <button type="submit" class="btn btn-cientifico">Guardar Cambios</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

